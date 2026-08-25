<?php

session_start();

$result = null;
$error = "";

if (isset($_POST['calculate'])) {

    $studentName = trim($_POST['student_name']);
    $semester = $_POST['semester'];

    $marks = [
        $_POST['subject1'],
        $_POST['subject2'],
        $_POST['subject3'],
        $_POST['subject4'],
        $_POST['subject5']
    ];

    /* ================= VALIDATION ================= */

    if ($studentName == "") {

        $error = "Please enter student name.";

    } elseif (!preg_match("/^[A-Za-z ]+$/", $studentName)) {

        $error = "Student name should contain only letters.";

    } elseif (
        !is_numeric($semester) ||
        $semester < 1 ||
        $semester > 8
    ) {

        $error = "Invalid semester.";

    } else {

        foreach ($marks as $mark) {

            if (
                $mark === "" ||
                !is_numeric($mark) ||
                $mark < 0 ||
                $mark > 100
            ) {

                $error =
                    "Marks must be between 0 and 100.";

                break;
            }
        }
    }


    /* ================= CALCULATION ================= */

    if ($error == "") {

        $marks = array_map('floatval', $marks);

        $total = array_sum($marks);

        $percentage = $total / 5;


        /* Grade */

        if ($percentage >= 90) {

            $grade = "A+";

        } elseif ($percentage >= 80) {

            $grade = "A";

        } elseif ($percentage >= 70) {

            $grade = "B";

        } elseif ($percentage >= 60) {

            $grade = "C";

        } elseif ($percentage >= 50) {

            $grade = "D";

        } else {

            $grade = "F";
        }


        /* Pass / Fail */

        $status = "PASS";

        foreach ($marks as $mark) {

            if ($mark < 35) {
                $status = "FAIL";
                break;
            }
        }

        if ($percentage < 35) {
            $status = "FAIL";
        }


        /* Store student name in Session */

        $_SESSION['result_student_name'] = $studentName;


        /* Store semester in Cookie for 30 days */

        setcookie(
            "last_semester",
            $semester,
            time() + (30 * 24 * 60 * 60),
            "/"
        );


        $result = [
            "name" => $studentName,
            "semester" => $semester,
            "marks" => $marks,
            "total" => $total,
            "percentage" => $percentage,
            "grade" => $grade,
            "status" => $status
        ];
    }
}


/* Get previous semester from Cookie */

$lastSemester = "";

if (isset($_COOKIE['last_semester'])) {
    $lastSemester = $_COOKIE['last_semester'];
}

?>

<!DOCTYPE html>

<html>

<head>

    <title>Student Result</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
        }

        .container {
            width: 700px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px #bbb;
        }

        h1 {
            text-align: center;
            color: #007bff;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        input,
        select {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
        }

        button {
            background: #007bff;
            color: white;
            border: none;
            padding: 12px 20px;
            cursor: pointer;
            width: 100%;
        }

        button:hover {
            background: #0056b3;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            margin-bottom: 15px;
        }

        .cookie {
            background: #fff3cd;
            padding: 10px;
            margin-bottom: 20px;
            color: #856404;
        }

        .result {
            margin-top: 30px;
            padding: 20px;
            background: #e8f5e9;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: center;
        }

        th {
            background: #343a40;
            color: white;
        }

        .pass {
            color: green;
            font-weight: bold;
        }

        .fail {
            color: red;
            font-weight: bold;
        }

        .back {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #007bff;
        }

    </style>

</head>

<body>

<div class="container">

    <h1>Student Marks & Result</h1>


    <!-- SESSION -->

    <?php if (isset($_SESSION['result_student_name'])) { ?>

        <p>
            Student stored in Session:
            <strong>
                <?php
                echo htmlspecialchars(
                    $_SESSION['result_student_name']
                );
                ?>
            </strong>
        </p>

    <?php } ?>


    <!-- COOKIE -->

    <?php if ($lastSemester != "") { ?>

        <div class="cookie">

            Last selected semester from Cookie:

            <strong>
                Semester
                <?php echo htmlspecialchars($lastSemester); ?>
            </strong>

        </div>

    <?php } ?>


    <!-- ERROR -->

    <?php if ($error != "") { ?>

        <div class="error">

            <?php echo htmlspecialchars($error); ?>

        </div>

    <?php } ?>


    <!-- MARKS FORM -->

    <form method="POST">

        <div class="form-group">

            <label>Student Name</label>

            <input
                type="text"
                name="student_name"
                required
                pattern="[A-Za-z ]+"
                value="<?php
                echo isset($_POST['student_name'])
                    ? htmlspecialchars($_POST['student_name'])
                    : '';
                ?>"
            >

        </div>


        <div class="form-group">

            <label>Semester</label>

            <select name="semester" required>

                <option value="">
                    Select Semester
                </option>

                <?php

                for ($i = 1; $i <= 8; $i++) {

                    $selected = "";

                    if (
                        isset($_POST['semester']) &&
                        $_POST['semester'] == $i
                    ) {

                        $selected = "selected";

                    } elseif (
                        !isset($_POST['semester']) &&
                        $lastSemester == $i
                    ) {

                        $selected = "selected";
                    }

                    echo "
                    <option value='$i' $selected>
                        Semester $i
                    </option>";
                }

                ?>

            </select>

        </div>


        <?php

        $subjects = [
            "subject1" => "Subject 1",
            "subject2" => "Subject 2",
            "subject3" => "Subject 3",
            "subject4" => "Subject 4",
            "subject5" => "Subject 5"
        ];

        foreach ($subjects as $field => $label) {

        ?>

            <div class="form-group">

                <label>
                    <?php echo $label; ?> Marks
                </label>

                <input
                    type="number"
                    name="<?php echo $field; ?>"
                    min="0"
                    max="100"
                    required
                    value="<?php
                    echo isset($_POST[$field])
                        ? htmlspecialchars($_POST[$field])
                        : '';
                    ?>"
                >

            </div>

        <?php

        }

        ?>


        <button type="submit" name="calculate">

            Calculate Result

        </button>

    </form>


    <!-- RESULT -->

    <?php if ($result != null) { ?>

        <div class="result">

            <h2>Result</h2>

            <p>
                <strong>Student Name:</strong>
                <?php
                echo htmlspecialchars($result['name']);
                ?>
            </p>

            <p>
                <strong>Semester:</strong>
                <?php
                echo htmlspecialchars($result['semester']);
                ?>
            </p>


            <table>

                <tr>
                    <th>Subject</th>
                    <th>Marks</th>
                </tr>

                <?php

                $subjectNumber = 1;

                foreach ($result['marks'] as $mark) {

                ?>

                    <tr>

                        <td>
                            Subject <?php echo $subjectNumber; ?>
                        </td>

                        <td>
                            <?php echo $mark; ?>
                        </td>

                    </tr>

                <?php

                    $subjectNumber++;
                }

                ?>

                <tr>

                    <th>Total Marks</th>

                    <th>
                        <?php echo $result['total']; ?>
                        / 500
                    </th>

                </tr>

                <tr>

                    <th>Percentage</th>

                    <th>
                        <?php
                        echo number_format(
                            $result['percentage'],
                            2
                        );
                        ?>%
                    </th>

                </tr>

                <tr>

                    <th>Grade</th>

                    <th>
                        <?php echo $result['grade']; ?>
                    </th>

                </tr>

                <tr>

                    <th>Result</th>

                    <th class="<?php
                        echo $result['status'] == "PASS"
                            ? "pass"
                            : "fail";
                    ?>">

                        <?php echo $result['status']; ?>

                    </th>

                </tr>

            </table>

        </div>

    <?php } ?>


    <a class="back" href="index.php">
        ← Back to Student Management
    </a>

</div>

</body>

</html>
