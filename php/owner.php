<?php
// owner.php
session_start();
require 'config.php'; // your DB credentials: $servername, $username, $password, $dbname

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $userpassword = $_POST['password'];
    $role = 'owner'; // owner form

    try {
        // Connect to database
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Check Users table
        $stmt = $conn->prepare("SELECT * FROM Users JOIN Person On Users.PersonID = Person.PersonID WHERE Email = :email AND Role = :role");
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':role', $role);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            if (password_verify($userpassword, $user['PasswordHash'])) {
                // Login success
                $_SESSION['user_id'] = $user['UserID'];
                $_SESSION['person_id'] = $user['PersonID'];
                $_SESSION['role'] = $user['Role'];
                $_SESSION['name'] = $user['FName'] . ' ' . $user['LName'];

                // Redirect to owner dashboard
                header("Location: ../owner_dashboard.php");
                exit;
            } else {
                $error = "Incorrect password.";
            }
        } else {
            $error = "No owner found with this email.";
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
<title>Owner Login</title>
</head>
<body>
<h2>Owner Login</h2>

<?php
if ($error) {
    echo "<p style='color:red;'>$error</p>";
}
?>

<p><a href="../index.html">Back to Login</a></p>
</body>
</html>

