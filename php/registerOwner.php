<!doctype html>
<html>

<head>
    <title>Register Owner</title>
    <link rel="stylesheet" href="../css/style.css" />
</head>

<body>
    <div class="container"></div>
<?php
// register.php
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
    $bio = $_POST['bio'];
    $language =$_POST ['language'];
    $fSize =$_POST ['fSize'];
    $occupation =$_POST ['occupation'];
    $email = $_POST['email'];
    $userpassword = $_POST['password'];
    $role = 'owner'; // this is an owner registration

    try {
        // Connect to DB
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Start transaction
        $conn->beginTransaction();

        // 1️⃣ Insert into Person table
        $stmt = $conn->prepare("INSERT INTO Person(FName, LName, DateOfBirth, Gender) VALUES(:firstName, :lastName, :dob, :gender)");
        $stmt->bindParam(':firstName', $firstName);
        $stmt->bindParam(':lastName', $lastName);
        $stmt->bindParam(':dob', $dob);
        $stmt->bindParam(':gender', $gender);
        $stmt->execute();

        // Get the new PersonID
        $person_id = $conn->lastInsertId();

        // 2️⃣ Insert into Owner table
        $stmt = $conn->prepare("INSERT INTO FamilyOwner
                (PersonID, Occupation, FamilySize, LanguageSpoken, Biography)
                VALUES (:person_id, :occupation, :fSize, :language, :bio)");
        $stmt->bindParam(':person_id', $person_id);
        $stmt->bindParam(':occupation', $occupation);
        $stmt->bindParam(':language', $language);
        $stmt->bindParam(':fSize', $fSize);
        $stmt->bindParam(':bio', $bio);
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
    } catch (PDOException $e) {
        $conn->rollBack();
        $error = "Error: " . $e->getMessage();
    }
}
?>
</div>
</body>
</html>