<?php
session_start();

/* ================= DATABASE CONNECTION ================= */

$host = "localhost";
$username = "root";
$password = "";
$database = "college_db";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

/* ================= LOGOUT ================= */

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

/* ================= LOGIN ================= */

if (isset($_POST['login'])) {

    $loginName = trim($_POST['login_name']);

    if ($loginName != "") {
        $_SESSION['user_name'] = $loginName;
        header("Location: index.php");
        exit();
    } else {
        $loginError = "Please enter your name.";
    }
}

/* ================= LOGIN CHECK ================= */

if (!isset($_SESSION['user_name'])) {
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Management - Login</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f2f5;
        }

        .login-box {
            width: 400px;
            margin: 100px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px #aaa;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background: #0056b3;
        }

        .error {
            color: red;
            text-align: center;
        }
    </style>
</head>

<body>

<div class="login-box">

    <h2>Student Management System</h2>

    <?php
    if (isset($loginError)) {
        echo "<p class='error'>$loginError</p>";
    }
    ?>

    <form method="POST">

        <label>Enter User Name:</label>

        <input
            type="text"
            name="login_name"
            placeholder="Enter your name"
            required
        >

        <button type="submit" name="login">
            Login
        </button>

    </form>

</div>

</body>
</html>

<?php
exit();
}

/* ================= ADD / UPDATE STUDENT ================= */

if (isset($_POST['save_student'])) {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $mobile = trim($_POST['mobile']);
    $course = trim($_POST['course']);
    $semester = $_POST['semester'];

    $errors = [];

    /* Validation */

    if ($name == "") {
        $errors[] = "Name is required.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Enter a valid email address.";
    }

    if (!preg_match("/^[0-9]{10}$/", $mobile)) {
        $errors[] = "Mobile number must contain exactly 10 digits.";
    }

    if ($course == "") {
        $errors[] = "Please select a course.";
    }

    if (!is_numeric($semester) || $semester < 1 || $semester > 8) {
        $errors[] = "Semester must be between 1 and 8.";
    }

    if (empty($errors)) {

        /* Store preferred course in Cookie for 30 days */

        setcookie(
            "preferred_course",
            $course,
            time() + (30 * 24 * 60 * 60),
            "/"
        );

        /* UPDATE */

        if (!empty($_POST['student_id'])) {

            $id = intval($_POST['student_id']);

            $stmt = $conn->prepare(
                "UPDATE students
                 SET name=?, email=?, mobile=?, course=?, semester=?
                 WHERE id=?"
            );

            $stmt->bind_param(
                "ssssii",
                $name,
                $email,
                $mobile,
                $course,
                $semester,
                $id
            );

            $stmt->execute();

            $stmt->close();

            header("Location: index.php?message=updated");
            exit();

        } else {

            /* INSERT */

            $stmt = $conn->prepare(
                "INSERT INTO students
                 (name, email, mobile, course, semester)
                 VALUES (?, ?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "ssssi",
                $name,
                $email,
                $mobile,
                $course,
                $semester
            );

            $stmt->execute();

            $stmt->close();

            header("Location: index.php?message=added");
            exit();
        }

    } else {
        $errorMessage = implode("<br>", $errors);
    }
}

/* ================= DELETE ================= */

if (isset($_GET['delete'])) {

    $id = intval($_GET['delete']);

    $stmt = $conn->prepare("DELETE FROM students WHERE id=?");

    $stmt->bind_param("i", $id);

    $stmt->execute();

    $stmt->close();

    header("Location: index.php?message=deleted");
    exit();
}

/* ================= EDIT ================= */

$editStudent = null;

if (isset($_GET['edit'])) {

    $id = intval($_GET['edit']);

    $stmt = $conn->prepare(
        "SELECT * FROM students WHERE id=?"
    );

    $stmt->bind_param("i", $id);

    $stmt->execute();

    $result = $stmt->get_result();

    $editStudent = $result->fetch_assoc();

    $stmt->close();
}

/* ================= FETCH STUDENTS ================= */

$result = $conn->query(
    "SELECT * FROM students ORDER BY id DESC"
);

/* ================= COOKIE ================= */

$preferredCourse = "";

if (isset($_COOKIE['preferred_course'])) {
    $preferredCourse = $_COOKIE['preferred_course'];
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Student Management System</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background: #f4f6f9;
        }

        .header {
            background: #007bff;
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            margin: 0;
        }

        .logout {
            background: #dc3545;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
        }

        .container {
            width: 90%;
            margin: 30px auto;
        }

        .welcome {
            background: white;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
        }

        .form-box {
            background: white;
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px #ddd;
        }

        .form-row {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }

        .form-group {
            flex: 1;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .btn {
            background: #28a745;
            color: white;
            border: none;
            padding: 11px 20px;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn:hover {
            background: #218838;
        }

        .cancel {
            background: #6c757d;
            color: white;
            padding: 11px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-left: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        th {
            background: #343a40;
            color: white;
            padding: 12px;
        }

        td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }

        tr:nth-child(even) {
            background: #f2f2f2;
        }

        .edit {
            background: #ffc107;
            color: black;
            padding: 7px 12px;
            text-decoration: none;
            border-radius: 4px;
        }

        .delete {
            background: #dc3545;
            color: white;
            padding: 7px 12px;
            text-decoration: none;
            border-radius: 4px;
        }

        .success {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 5px;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 5px;
        }

        .cookie {
            background: #fff3cd;
            color: #856404;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

    </style>

</head>

<body>

<div class="header">

    <h1>Student Management System</h1>

    <a
        class="logout"
        href="index.php?logout=1"
        onclick="return confirm('Are you sure you want to logout?');"
    >
        Logout
    </a>

</div>


<div class="container">

    <!-- SESSION -->

    <div class="welcome">

        <h3>
            Welcome,
            <?php echo htmlspecialchars($_SESSION['user_name']); ?>!
        </h3>

        <p>
            Logged-in user's name is stored using PHP Session.
        </p>

    </div>


    <!-- COOKIE -->

    <?php if ($preferredCourse != "") { ?>

        <div class="cookie">

            Your preferred course is:

            <strong>
                <?php echo htmlspecialchars($preferredCourse); ?>
            </strong>

        </div>

    <?php } ?>


    <!-- MESSAGES -->

    <?php

    if (isset($_GET['message'])) {

        if ($_GET['message'] == "added") {
            echo "<div class='success'>Student added successfully.</div>";
        }

        if ($_GET['message'] == "updated") {
            echo "<div class='success'>Student updated successfully.</div>";
        }

        if ($_GET['message'] == "deleted") {
            echo "<div class='success'>Student deleted successfully.</div>";
        }
    }

    if (isset($errorMessage)) {
        echo "<div class='error'>$errorMessage</div>";
    }

    ?>


    <!-- STUDENT FORM -->

    <div class="form-box">

        <h2>
            <?php
            echo $editStudent
                ? "Edit Student"
                : "Add Student";
            ?>
        </h2>

        <form method="POST">

            <input
                type="hidden"
                name="student_id"
                value="<?php
                echo $editStudent
                    ? $editStudent['id']
                    : '';
                ?>"
            >

            <div class="form-row">

                <div class="form-group">

                    <label>Student Name</label>

                    <input
                        type="text"
                        name="name"
                        required
                        pattern="[A-Za-z ]{2,100}"
                        title="Name should contain only letters and spaces"
                        value="<?php
                        echo $editStudent
                            ? htmlspecialchars($editStudent['name'])
                            : '';
                        ?>"
                    >

                </div>


                <div class="form-group">

                    <label>Email</label>

                    <input
                        type="email"
                        name="email"
                        required
                        value="<?php
                        echo $editStudent
                            ? htmlspecialchars($editStudent['email'])
                            : '';
                        ?>"
                    >

                </div>

            </div>


            <div class="form-row">

                <div class="form-group">

                    <label>Mobile</label>

                    <input
                        type="text"
                        name="mobile"
                        required
                        pattern="[0-9]{10}"
                        maxlength="10"
                        title="Enter exactly 10 digits"
                        value="<?php
                        echo $editStudent
                            ? htmlspecialchars($editStudent['mobile'])
                            : '';
                        ?>"
                    >

                </div>


                <div class="form-group">

                    <label>Course</label>

                    <select name="course" required>

                        <option value="">Select Course</option>

                        <?php

                        $courses = [
                            "BCA",
                            "BBA",
                            "B.Sc IT",
                            "B.Com",
                            "MCA",
                            "MBA"
                        ];

                        foreach ($courses as $c) {

                            $selected = "";

                            if (
                                $editStudent &&
                                $editStudent['course'] == $c
                            ) {
                                $selected = "selected";
                            }

                            elseif (
                                !$editStudent &&
                                $preferredCourse == $c
                            ) {
                                $selected = "selected";
                            }

                            echo "<option value='" .
                                htmlspecialchars($c) .
                                "' $selected>" .
                                htmlspecialchars($c) .
                                "</option>";
                        }

                        ?>

                    </select>

                </div>


                <div class="form-group">

                    <label>Semester</label>

                    <select name="semester" required>

                        <option value="">Select Semester</option>

                        <?php

                        for ($i = 1; $i <= 8; $i++) {

                            $selected = "";

                            if (
                                $editStudent &&
                                $editStudent['semester'] == $i
                            ) {
                                $selected = "selected";
                            }

                            echo "<option value='$i' $selected>
                                    Semester $i
                                  </option>";
                        }

                        ?>

                    </select>

                </div>

            </div>


            <button
                type="submit"
                name="save_student"
                class="btn"
            >

                <?php
                echo $editStudent
                    ? "Update Student"
                    : "Add Student";
                ?>

            </button>


            <?php if ($editStudent) { ?>

                <a
                    href="index.php"
                    class="cancel"
                >
                    Cancel
                </a>

            <?php } ?>

        </form>

    </div>


    <!-- STUDENT TABLE -->

    <h2>All Students</h2>

    <table>

        <tr>

            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Mobile</th>
            <th>Course</th>
            <th>Semester</th>
            <th>Actions</th>

        </tr>

        <?php

        if ($result->num_rows > 0) {

            while ($row = $result->fetch_assoc()) {

        ?>

        <tr>

            <td>
                <?php echo $row['id']; ?>
            </td>

            <td>
                <?php
                echo htmlspecialchars($row['name']);
                ?>
            </td>

            <td>
                <?php
                echo htmlspecialchars($row['email']);
                ?>
            </td>

            <td>
                <?php
                echo htmlspecialchars($row['mobile']);
                ?>
            </td>

            <td>
                <?php
                echo htmlspecialchars($row['course']);
                ?>
            </td>

            <td>
                <?php
                echo htmlspecialchars($row['semester']);
                ?>
            </td>

            <td>

                <a
                    class="edit"
                    href="index.php?edit=<?php echo $row['id']; ?>"
                >
                    Edit
                </a>

                <a
                    class="delete"
                    href="index.php?delete=<?php echo $row['id']; ?>"
                    onclick="return confirm('Delete this student?');"
                >
                    Delete
                </a>

            </td>

        </tr>

        <?php

            }

        } else {

            echo "
            <tr>
                <td colspan='7'>
                    No students found.
                </td>
            </tr>
            ";

        }

        ?>

    </table>

    <br>

    <p>
        <a href="q2.php">Go to Question 2 - Marks & Result</a>
    </p>

</div>

</body>
</html>

<?php
$conn->close();
?>
