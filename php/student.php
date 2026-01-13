<?php
// student.php
session_start();
require 'config.php'; // your DB credentials: $servername, $username, $password, $dbname

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $userpassword = $_POST['password'];
    $role = $_POST['role']; // should be 'student'

    try {
        // Connect to DB
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepare SQL to fetch user by email and role
        $stmt = $conn->prepare("SELECT * FROM Users JOIN Person ON Users.PersonID = Person.PersonID WHERE Email = :email AND Role = :role");
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':role', $role);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Verify password (assuming you hashed it during registration)
            if (password_verify($userpassword, $user['PasswordHash'])) {
                // Password correct, start session
                $_SESSION['user_id'] = $user['UserID'];
                $_SESSION['role'] = $user['Role'];
                $_SESSION['person_id'] = $user['PersonID'];
                $_SESSION['name'] = $user['FName'] . ' ' . $user['LName'];

                // Redirect to student dashboard
                header("Location: ../student_dashboard.php");
                exit;
            } else {
                $error = "Incorrect password.";
            }
        } else {
            $error = "No student found with this email.";
        }

    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student Login</title>
</head>
<body>
<h2>Student Login</h2>

<?php
if (!empty($error)) {
    echo "<p style='color:red;'>$error</p>";
}
?>

<p><a href="../index.html">Back to Login</a></p>
</body>
</html>
