<!doctype html>
<html>

<head>
    <title>Insert a Record</title>
    <link rel="stylesheet" href="../css/style.css" />
</head>

<body>
    <div class="container">

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require 'config.php';

/* ---------------- CONNECT ---------------- */
try {
    $conn = new PDO(
        "mysql:host=$servername;dbname=$dbname;charset=utf8",
        $username,
        $password
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("<p style='color:red'>DB Connection Failed: " . $e->getMessage() . "</p>");
}

/* ---------------- ROUTE BY TABLE ---------------- */
if (!isset($_POST['table'])) {
    die("<p style='color:red'>No table selected.</p>");
}

$table = $_POST['table'];

try {
    if ($table === "PersonWritesFeedback") {
        $sql = "INSERT INTO PersonWritesFeedback
                (PersonID, ContractID, Rating, CommentText, CommentDate)
                VALUES (:pid, :cid, :r, :t, :d)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':pid' => $_SESSION['person_id'],
            ':cid' => $_POST['ContractID'],
            ':r'   => $_POST['Rating'],
            ':t'   => $_POST['FeedbackText'],
            ':d'   => $_POST['FeedbackDate']
        ]);
    }

    /* -------- HOBBIES -------- */
    elseif ($table === "House") {
        $checkSql = "SELECT 1 FROM House WHERE OwnerID = :oid LIMIT 1";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->execute([
        ':oid' => $_SESSION['person_id']
    ]);

    if ($checkStmt->fetch()) {
        die("You already have a house registered. You cannot add another one.");
    }
        $sql = "INSERT INTO House (StreetNumber, StreetName, City, ZipCode, isPets, isSmoking, NoOfAvailableRooms, TotalNumberOfRooms, OwnerID)
                VALUES (:stNo, :stN, :city, :zip, :iP, :iS, :arN, :rN, :oid)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':stNo' => $_POST['StreetNumber'],
            ':stN' => $_POST['StreetName'],
            ':city' => $_POST['City'],
            ':zip' => $_POST['ZipCode'],
            ':iP' => $_POST['IsPets'],
            ':iS' => $_POST['IsSmoking'],
            ':arN' => $_POST['NoOfAvailableRooms'],
            ':rN' => $_POST['TotalNumberOfRooms'],
            ':oid' => $_SESSION['person_id']
        ]);
        $house_id = $conn->lastInsertId();
    }

    /* -------- INTERESTS -------- */
    elseif ($table === "RoomHouse") {

        $houseID = $_POST['HouseID'];
    $isAvailable = (int) $_POST['Availability'];

    /* 1️⃣ Count existing rooms */
    $countSql = "
        SELECT 
            COUNT(*) AS totalRooms,
            SUM(isAvailable = 1) AS availableRooms
        FROM RoomHouse
        WHERE HouseID = :hid
    ";
    $countStmt = $conn->prepare($countSql);
    $countStmt->execute([':hid' => $houseID]);
    $counts = $countStmt->fetch(PDO::FETCH_ASSOC);

    /* 2️⃣ Get limits from House */
    $limitSql = "
        SELECT NoOfAvailableRooms, TotalNumberOfRooms
        FROM House
        WHERE HouseID = :hid
    ";
    $limitStmt = $conn->prepare($limitSql);
    $limitStmt->execute([':hid' => $houseID]);
    $limits = $limitStmt->fetch(PDO::FETCH_ASSOC);

    if (!$limits) {
        die("Invalid house selected.");
    }

    /* 3️⃣ Enforce rules */
    if ($counts['totalRooms'] >= $limits['TotalNumberOfRooms']) {
        die("Cannot add more rooms. Total room limit reached.");
    }

    if ($isAvailable === 1 && $counts['availableRooms'] >= $limits['NoOfAvailableRooms']) {
        die("Cannot add more available rooms than allowed.");
    }

        $sql = "INSERT INTO RoomHouse (RoomNo, HouseID, Price, Size, isAvailable, StartDate, EndDate)
                VALUES (:rNO, :hid, :price, :size, :isA, :sdate, :edate)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':rNO' => $_POST['RoomNumber'],
            ':hid' => $_POST['HouseID'],
            ':price' => $_POST['Rent'],
            ':size' => $_POST['Size'],
            ':isA' => $_POST['Availability'],
            ':sdate' => $_POST['StartDate'],
            ':edate' => $_POST['EndDate']
        ]);
    }

    else {
        throw new Exception("Unknown table selected.");
    }

    echo "
    
    <div class='card'>
            <h2>Success 🎉</h2>
            <p>Your information was added successfully.</p>

            <div style='margin-top: 20px; display: flex; gap: 16px; flex-wrap: wrap;'>
                <a href='../ownerInsertOperations.php' class='btn'>Insert More</a>
                <a href='../owner_dashboard.php' class='btn' style='background: #e5e7eb; color: #0f172a;'>Back to Dashboard</a>
            </div>
        </div>";

} catch (Exception $e) {
    echo "<p style='color:red'>Insert failed: " . $e->getMessage() . "</p>
    <div style='margin-top: 20px; display: flex; gap: 16px; flex-wrap: wrap;'>
               
                <a href='../owner_dashboard.php' class='btn' style='background: #e5e7eb; color: #0f172a;'>Back to Dashboard</a>
            </div>";
}

$conn = null;
?>
</div>
</body>
</html>