<!doctype html>
<html>

<head>
    <title>Approve Request</title>
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

$ownerID = $_SESSION['person_id'];
$requestID = $_POST['RequestID'];

try {
    $conn = new PDO(
        "mysql:host=$servername;dbname=$dbname;charset=utf8",
        $username,
        $password
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $conn->beginTransaction();

    // 1️⃣ Get request details
    $stmt = $conn->prepare("SELECT * FROM Request WHERE RequestID = :rid");
    $stmt->execute([':rid' => $requestID]);
    $request = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$request) {
        throw new Exception("Request not found.");
    }

    if ($request['Status'] !== 'pending') {
        throw new Exception("Request is already processed.");
    }

    $studentID = $request['StudentID'];
    $houseID   = $request['HouseID'];
    $roomNo    = $request['RoomNo'];

    // 2️⃣ Check if student already has a room and end previous contract
    $stmt = $conn->prepare("
        SELECT * FROM Contract
        WHERE StudentID = :sid AND EndDate IS NULL
    ");
    $stmt->execute([':sid' => $studentID]);
    $currentContract = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($currentContract) {
        // End current contract
        $stmt = $conn->prepare("
            UPDATE Contract SET EndDate = CURDATE()
            WHERE ContractID = :cid
        ");
        $stmt->execute([':cid' => $currentContract['ContractID']]);

        // Remove student from previous room
        $stmt = $conn->prepare("
            DELETE FROM Student_RoomHouse
            WHERE StudentID = :sid
        ");
        $stmt->execute([':sid' => $studentID]);
    }
//free up prev room
    // Free the previous room
$stmt = $conn->prepare("
    UPDATE RoomHouse RH
    JOIN Student_RoomHouse SRH
        ON RH.HouseID = SRH.HouseID AND RH.RoomNo = SRH.RoomNo
    SET RH.isAvailable = 1
    WHERE SRH.StudentID = :studentID
");
$stmt->execute([':studentID' => $studentID]);


    // 3️⃣ Assign room
    $stmt = $conn->prepare("
        INSERT INTO Student_RoomHouse (StudentID, HouseID, RoomNo)
        VALUES (:sid, :hid, :rno)
    ");
    $stmt->execute([
        ':sid' => $studentID,
        ':hid' => $houseID,
        ':rno' => $roomNo
    ]);

    //making room taken
    $stmt = $conn->prepare("
    UPDATE RoomHouse 
    SET isAvailable = 0
    WHERE HouseID = :hid AND RoomNo = :rno
");
$stmt->execute([
    ':hid' => $houseID,
    ':rno' => $roomNo
]);


    // 4️⃣ Create new contract
    $stmt = $conn->prepare("
        INSERT INTO Contract (StartDate, EndDate, StudentID, OwnerID)
        VALUES (CURDATE(), NULL, :sid, :oid)
    ");
    $stmt->execute([
        ':sid' => $studentID,
        ':oid' => $ownerID
    ]);

    // 5️⃣ Update request status
    $stmt = $conn->prepare("UPDATE Request SET Status='approved' WHERE RequestID=:rid");
    $stmt->execute([':rid' => $requestID]);

    $conn->commit();

    echo "
    <div class='card'>
            <h2>Success 🎉 Request approved!</h2>
            <p>Your request has been submitted successfully.</p>
            <p>Student has been assigned the room.</p>

            <div style='margin-top: 20px; display: flex; gap: 16px; flex-wrap: wrap;'>
                <a href='viewOwnerRequests.php' class='btn' style='background: #e5e7eb; color: #0f172a;'>Back to Requests</a>
            </div>
        </div>
    ";

} catch (Exception $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>
    <div style='margin-top: 20px; display: flex; gap: 16px; flex-wrap: wrap;'>
                <a href='viewOwnerRequests.php' class='btn' style='background: #e5e7eb; color: #0f172a;'>Back to Requests</a>
            </div>";
}

?>
</div>
</body>
</html>