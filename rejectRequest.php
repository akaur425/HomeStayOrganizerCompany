<!doctype html>
<html>

<head>
    <title>Cancel a Request</title>
    <link rel="stylesheet" href="css/style.css" />
</head>

<body>
    <div class="container"></div>
<?php
session_start();
require 'php/config.php';

if (!isset($_SESSION['person_id'])) {
    die("You must be logged in as an owner.");
}

if (!isset($_POST['RequestID'])) {
    die("Invalid request.");
}

$requestID = $_POST['RequestID'];

try {
    $conn = new PDO(
        "mysql:host=$servername;dbname=$dbname;charset=utf8",
        $username,
        $password
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Update request status to rejected
    $stmt = $conn->prepare("UPDATE Request SET Status='rejected' WHERE RequestID=:rid AND Status='pending'");
    $stmt->execute([':rid' => $requestID]);

    if ($stmt->rowCount() === 0) {
        throw new Exception("Request not found or already processed.");
    }

    echo "
    <div class='card'>
            <h2>Request Cancelled</h2>

            <div style='margin-top: 20px; display: flex; gap: 16px; flex-wrap: wrap;'>
                <a href='viewRequests.php' class='btn' style='background: #e5e7eb; color: #0f172a;'>Back to Requests</a>
            </div>
        </div>";

} catch (Exception $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>
    <div style='margin-top: 20px; display: flex; gap: 16px; flex-wrap: wrap;'>
                <a href='viewRequests.php' class='btn' style='background: #e5e7eb; color: #0f172a;'>Back to Requests</a>
            </div>";
}

?>
</div>
</body>
</html>