<?php

// Database connection
$conn = new mysqli("localhost", "root", "", "college_db");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check form submission
if (isset($_POST['submit'])) {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $course = $_POST['course'];
    $semester = $_POST['semester'];

    // Insert data using prepared statement
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

    if ($stmt->execute()) {
        echo "<h2>Student data inserted successfully!</h2>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Student</title>
</head>

<body>

<h2>Student Registration Form</h2>

<form method="POST" action="insert.php">

    <label>Student Name:</label>
    <input type="text" name="name" required>
    <br><br>

    <label>Email:</label>
    <input type="email" name="email" required>
    <br><br>

    <label>Mobile:</label>
    <input type="text" name="mobile" required>
    <br><br>

    <label>Course:</label>
    <select name="course" required>
        <option value="">Select Course</option>
        <option value="BCA">BCA</option>
        <option value="BBA">BBA</option>
        <option value="B.Sc IT">B.Sc IT</option>
        <option value="B.Com">B.Com</option>
        <option value="MCA">MCA</option>
        <option value="MBA">MBA</option>
    </select>
    <br><br>

    <label>Semester:</label>
    <select name="semester" required>
        <option value="">Select Semester</option>
        <option value="1">Semester 1</option>
        <option value="2">Semester 2</option>
        <option value="3">Semester 3</option>
        <option value="4">Semester 4</option>
        <option value="5">Semester 5</option>
        <option value="6">Semester 6</option>
        <option value="7">Semester 7</option>
        <option value="8">Semester 8</option>
    </select>
    <br><br>

    <button type="submit" name="submit">
        Insert Student
    </button>

</form>

</body>
</html>
