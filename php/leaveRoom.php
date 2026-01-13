<!doctype html>
<html>

<head>
    <title>Leave a Room</title>
    <link rel="stylesheet" href="../css/style.css" />
</head>

<body>
    <div class="container">
<?php
session_start();
require 'config.php';

if (!isset($_SESSION['person_id'])) {
    die("You must be logged in as a student.");
}

$studentID = $_SESSION['person_id'];

try {
    $conn = new PDO(
        "mysql:host=$servername;dbname=$dbname;charset=utf8",
        $username,
        $password
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $conn->beginTransaction();


    // 2️⃣ Check if student already has a room and end previous contract
    $stmt = $conn->prepare("
        SELECT * FROM Contract
        WHERE StudentID = :sid AND EndDate IS NULL
    ");
    $stmt->execute([':sid' => $studentID]);
    $currentContract = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$currentContract) {
    echo "<p style='color:orange'>You do not currently have an active room.</p>";
}


    else {
        // End current contract
        $stmt = $conn->prepare("
            UPDATE Contract SET EndDate = CURDATE()
            WHERE ContractID = :cid
        ");
        $stmt->execute([':cid' => $currentContract['ContractID']]);

        

        $stmt = $conn->prepare("
    UPDATE RoomHouse RH
    JOIN Student_RoomHouse SRH
        ON RH.HouseID = SRH.HouseID AND RH.RoomNo = SRH.RoomNo
    SET RH.isAvailable = 1
    WHERE SRH.StudentID = :studentID
");
$stmt->execute([':studentID' => $studentID]);

// Remove student from previous room
$stmt = $conn->prepare("
            DELETE FROM Student_RoomHouse
            WHERE StudentID = :sid
        ");
        $stmt->execute([':sid' => $studentID]);
    

    $conn->commit();

    echo "
        <div class='card'>
            <h2>Success 🎉</h2>
            <p>Room is left successfully.</p>
        </div>
    ";
    }

} catch (Exception $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}

echo "<br><a href='../student_dashboard.php'>Back to Home Page</a>";
?>
</div>
</body>
</html>
