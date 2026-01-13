<!doctype html>
<html>

<head>
    <title>Confirm a Room</title>
    <link rel="stylesheet" href="../css/style.css" />
</head>

<body>
    <div class="container"></div>
<?php
session_start();
require 'config.php';

/* 1️⃣ Check login */
if (!isset($_SESSION['person_id'])) {
    die("You must be logged in.");
}

$studentID = $_SESSION['person_id'];

/* 2️⃣ Validate POST data */
if (
    !isset($_POST['RoomNo']) ||
    !isset($_POST['HouseID']) ||
    !isset($_POST['OwnerID'])
) {
    die("Invalid request.");
}

$roomNo  = $_POST['RoomNo'];
$houseID = $_POST['HouseID'];
$ownerID = $_POST['OwnerID'];

try {
    $conn = new PDO(
        "mysql:host=$servername;dbname=$dbname;charset=utf8",
        $username,
        $password
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    /* 3️⃣ Check if student already has a pending request */
    $check = $conn->prepare("
        SELECT RequestID
        FROM Request
        WHERE StudentID = :sid
          AND Status = 'pending'
    ");
    $check->execute([
        ':sid' => $studentID
    ]);

    if ($check->rowCount() > 0) {
        echo "<h3>You already have a pending request.</h3>";
        echo "<p>Please cancel it before requesting another room.</p>";
        echo "<a href='../viewRequests.php'>View My Requests</a>";
        exit;
    }

    /* 4️⃣ Insert new request */
    $insert = $conn->prepare("
        INSERT INTO Request
            (StudentID, RoomNo, HouseID, OwnerID, RequestDate, Status)
        VALUES
            (:sid, :rno, :hid, :oid, CURDATE(), 'pending')
    ");

    $insert->execute([
        ':sid' => $studentID,
        ':rno' => $roomNo,
        ':hid' => $houseID,
        ':oid' => $ownerID
    ]);

    /* 5️⃣ Success message */
    
    echo "<div class='card'>
            <h2>Success 🎉</h2>
            <p>Your request has been submitted successfully.</p>
            <p>Please wait for approval or rejection.</p>

            <div style='margin-top: 20px; display: flex; gap: 16px; flex-wrap: wrap;'>
                <a href='../student_dashboard.php' class='btn' style='background: #e5e7eb; color: #0f172a;'>Back to Dashboard</a>
            </div>
        </div>";

} catch (Exception $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>
    <div style='margin-top: 20px; display: flex; gap: 16px; flex-wrap: wrap;'>
                <a href='../student_dashboard.php' class='btn' style='background: #e5e7eb; color: #0f172a;'>Back to Dashboard</a>
            </div>";
}
?>
</div>
</body>
</html>