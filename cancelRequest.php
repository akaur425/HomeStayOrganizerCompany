<?php
session_start();
require 'php/config.php';

if (!isset($_SESSION['person_id'])) {
    die("Unauthorized.");
}

if (!isset($_POST['RequestID'])) {
    die("Invalid request.");
}

$studentID = $_SESSION['person_id'];
$requestID = $_POST['RequestID'];

try {
    $conn = new PDO(
        "mysql:host=$servername;dbname=$dbname;charset=utf8",
        $username,
        $password
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "
        UPDATE Request
        SET Status = 'cancelled'
        WHERE RequestID = :rid
          AND StudentID = :sid
          AND Status = 'pending'
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':rid' => $requestID,
        ':sid' => $studentID
    ]);

    header("Location: viewOwnerRequests.php");
    exit;

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
