<!doctype html>
<html>

<head>
    <title>Register Student</title>
    <link rel="stylesheet" href="../css/style.css" />
</head>

<body>
    <div class="container"></div>
<?php
// register.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require 'config.php'; // DB credentials: $servername, $username, $password, $dbname

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Collect form data
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $nationality = $_POST['nationality'];
    $school_id = $_POST['SchoolID'];
    $duration = $_POST['duration'];
    $email = $_POST['email'];
    $userpassword = $_POST['password'];
    $role = 'student'; // this is a student registration

    try {
        // Connect to DB
        $conn = new PDO(
    "mysql:host=$servername;dbname=$dbname;charset=utf8mb4",
    $username,
    $password
);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Start transaction
        $conn->beginTransaction();

        // 1️⃣ Insert into Person table
        $stmt = $conn->prepare("INSERT INTO Person(FName, LName, DateOfBirth, Gender) VALUES(:fname, :lname, :dob, :gender)");
        $stmt->bindParam(':fname', $firstName);
        $stmt->bindParam(':lname', $lastName);
        $stmt->bindParam(':dob', $dob);
        $stmt->bindParam(':gender', $gender);
        $stmt->execute();

        // Get the new PersonID
        $person_id = $conn->lastInsertId();

        // 2️⃣ Insert into Student table
        $stmt = $conn->prepare("INSERT INTO Student(PersonID, Nationality, SchoolID, DurationOfStudiesInYears) 
                                VALUES(:person_id, :nationality, :school_id, :duration)");
        $stmt->bindParam(':person_id', $person_id);
        $stmt->bindParam(':nationality', $nationality);
        $stmt->bindParam(':school_id', $school_id);
        $stmt->bindParam(':duration', $duration);
        $stmt->execute();

        // 3️⃣ Insert into Users table
        $password_hash = password_hash($userpassword, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO Users(Email, PasswordHash, Role, PersonID) 
                                VALUES(:email, :password_hash, :role, :person_id)");
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password_hash', $password_hash);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':person_id', $person_id);
        $stmt->execute();

        // Commit transaction
        echo "<div class='card'>
            <h2>Success 🎉</h2>
            <p>Your account has been created successfully.</p>
            <p>You can login now.</p>

            <div style='margin-top: 20px; display: flex; gap: 16px; flex-wrap: wrap;'>
                <a href='../index.html' class='btn' style='background: #e5e7eb; color: #0f172a;'>Back to Login</a>
            </div>
        </div>";
    }  catch (PDOException $e) {
    if (isset($conn) && $conn->inTransaction()) {
        $conn->rollBack();
    }
    $error = "Error: " . $e->getMessage();
}

}
?>
</div>
</body>
</html>