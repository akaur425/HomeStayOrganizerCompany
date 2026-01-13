<?php
session_start();
require 'php/config.php';

if (!isset($_SESSION['person_id'])) {
    die("You must be logged in.");
}

$studentID = $_SESSION['person_id'];

try {
    $conn = new PDO(
        "mysql:host=$servername;dbname=$dbname;charset=utf8",
        $username,
        $password
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "
        SELECT 
            r.RequestID,
            r.RoomNo,
            r.HouseID,
            r.Status,
            r.RequestDate,
            h.City,
            h.StreetName, 
            p.FName,
            p.LName
        FROM Request r
        JOIN House h ON r.HouseID = h.HouseID
        JOIN Person p ON p.PersonID = h.OwnerID
        WHERE r.StudentID = :sid
        ORDER BY r.RequestDate DESC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute([':sid' => $studentID]);
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Room Requests</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="container">

    <!-- PAGE HEADER -->
    <div style="margin-bottom: 40px;">
        <h1>My Room Requests</h1>
        <p>Track the status of rooms you’ve applied for</p>
    </div>

    <?php if (empty($requests)): ?>

        <div class="card">
            <h2>No Requests Yet</h2>
            <p>You haven’t submitted any room requests.</p>
        </div>

    <?php else: ?>

        <div class="results-grid">

            <?php foreach ($requests as $req): ?>
                <div class="room-card">

                    <!-- HEADER -->
                    <div class="room-header">
                        <h3>
                            <?= htmlspecialchars($req['StreetName']) ?>
                        </h3>

                        <span class="price">
                            <?= strtoupper(htmlspecialchars($req['Status'])) ?>
                        </span>
                    </div>

                    <!-- LOCATION -->
                    <p class="city">
                        <?= htmlspecialchars($req['City']) ?>
                    </p>

                    <!-- META -->
                    <div class="room-meta">
                        <span>🏠 Owner Name: <?= htmlspecialchars($req['FName'] . ' ' . $req['LName']) ?></span>
                        <span>🚪 Room <?= $req['RoomNo'] ?></span>
                    </div>

                    <p class="small">
                        Requested on <?= htmlspecialchars($req['RequestDate']) ?>
                    </p>

                    <!-- ACTION -->
                    <?php if ($req['Status'] === 'pending'): ?>
                        <form method="post" action="cancelRequest.php" style="margin-top: 15px;">
                            <input type="hidden" name="RequestID" value="<?= $req['RequestID'] ?>">
                            <button class="btn" type="submit">Cancel Request</button>
                        </form>
                    <?php endif; ?>

                </div>
            <?php endforeach; ?>

        </div>

    <?php endif; ?>

    <div class="small" style="margin-top: 40px;">
        <a href="student_dashboard.php">← Back to Dashboard</a>
    </div>

</div>

</body>
</html>
