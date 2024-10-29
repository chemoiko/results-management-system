
<?php
include 'connection.php';

mysql://xkofzkjzzkct05up:tafvbqjxlwugf7l3@wyqk6x041tfxg39e.chr7pe7iynqr.eu-west-1.rds.amazonaws.com:3306/dhvdlcoce2vbat74
// Insert Grade and calculate total
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $regNo = $_POST['regNo'];
    $coursework = $_POST['coursework'];
    $test = $_POST['test'];
    $final = $_POST['final'];
    $courseCode = $_POST['courseCode'];

    // Calculate total score and grade
    $total = ($coursework /30 * 0.15) + ($test/20 * 0.15) + ($final /100 * 0.7);
    $total = $total * 100;
    // Determine grade based on total
   // Determine grade based on total with stricter and exclusive ranges
    if ($total >= 80) {
        $grade = 5.0;
    } elseif ($total >= 75 && $total < 80) {
        $grade = 4.5;
    } elseif ($total >= 70 && $total < 75) {
        $grade = 4.0;
    } elseif ($total >= 65 && $total < 70) {
        $grade = 3.5;
    } elseif ($total >= 60 && $total < 65) {
        $grade = 3.0;
    } elseif ($total >= 55 && $total < 60) {
        $grade = 2.5;
    } elseif ($total >= 50 && $total < 55) {
        $grade = 2.0;
    } else {
        $grade = 0.0;
    }


    // Insert into marks table
    $query = "INSERT INTO marks (regNo, courseCode, coursework, test, final, total, grade) 
              VALUES ('$regNo', '$courseCode', '$coursework', '$test', '$final', '$total', '$grade')";
    mysqli_query($conn, $query);

    // Insert GPA record or update if exists
    $query = "INSERT INTO gpa (regNo, gpa) VALUES ('$regNo', '$grade') 
              ON DUPLICATE KEY UPDATE gpa = '$grade'";
    mysqli_query($conn, $query);
}

// Fetch Students and Course Units for Dropdown
$students = array();
$result = mysqli_query($conn, "SELECT * FROM student");
while ($row = mysqli_fetch_assoc($result)) {
    $students[] = $row;
}

$courseUnits = array();
$result = mysqli_query($conn, "SELECT * FROM courseunit");
while ($row = mysqli_fetch_assoc($result)) {
    $courseUnits[] = $row;
}

// Fetch Grades and Calculate GPA for Display
// Fetch Grades and Calculate GPA for Display
$results = array();
$result = mysqli_query($conn, "
    SELECT s.name AS student_name, s.regNo, cu.courseUnitName, m.grade, m.courseCode
    FROM marks m
    JOIN student s ON m.regNo = s.regNo
    JOIN courseunit cu ON m.courseCode = cu.courseCode
");
while ($row = mysqli_fetch_assoc($result)) {
    $results[] = $row;
}

// Calculate GPA per student
$gpas = array();
foreach ($results as $result) {
    $regNo = $result['regNo'];
    if (!isset($gpas[$regNo])) {
        $gpas[$regNo] = array('grades' => array(), 'name' => $result['student_name']);
    }
    // Store grade by course code
    $gpas[$regNo]['grades'][$result['courseCode']] = $result['grade'];
}

// Calculate GPA
foreach ($gpas as $regNo => &$data) {
    $data['gpa'] = array_sum($data['grades']) / count($data['grades']);
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Results Management System</title>
    <link rel="stylesheet" href="styles.css">
     <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500&display=swap" rel="stylesheet">
</head>
<body>

<div id="begin">
    <h1 id="group">GROUP 2</h1>
    <img src="miu-logo.png" alt="logo" id="logo">
</div>
<h1>Enter Student Results</h1>
<form method="post">
    <label for="regNo">Select Student:</label>
    <select name="regNo" required>
        <?php foreach ($students as $student): ?>
            <option value="<?php echo $student['regNo']; ?>"><?php echo $student['name']; ?></option>
        <?php endforeach; ?>
    </select><br>

    <label for="coursework">Coursework Marks:</label>
    <input type="number" name="coursework" placeholder="out of 30"><br>

    <label for="test">Test Marks:</label>
    <input type="number" name="test" placeholder="out of 20" required><br>

    <label for="final">Final Exam Marks:</label>
    <input type="number" name="final" placeholder="out of 100"><br>

     <label for="courseCode">Select Course Unit:</label>
    <select name="courseCode" required>
        <?php foreach ($courseUnits as $courseUnit): ?>
            <option value="<?php echo $courseUnit['courseCode']; ?>"><?php echo $courseUnit['courseUnitName']; ?></option>
        <?php endforeach; ?>
    </select><br>

    <button type="submit">Submit Results</button>
</form>

<h1>Student Results</h1>
<table border="1">
    <tr>
        <th>Student Name</th>
        <th>Registration Number</th>
        <?php foreach ($courseUnits as $courseUnit): ?>
            <th><?php echo $courseUnit['courseUnitName']; ?></th>
        <?php endforeach; ?>
        <th>Overall GPA</th>
    </tr>
         <?php foreach ($gpas as $regNo => $gpa): ?>
<tr>
    <td><?php echo htmlspecialchars($gpa['name']); ?></td>
    <td><?php echo htmlspecialchars($regNo); ?></td>
    <?php foreach ($courseUnits as $courseUnit): ?>
        <td><?php echo isset($gpa['grades'][$courseUnit['courseCode']]) ? number_format($gpa['grades'][$courseUnit['courseCode']], 2) : '-'; ?></td>
    <?php endforeach; ?>
    <td><?php echo isset($gpa['gpa']) ? number_format($gpa['gpa'], 2) : '-'; ?></td>
</tr>
<?php endforeach; ?>

</table>

</body>
</html>
