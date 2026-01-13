<?php
session_start();
require 'php/config.php';

// Check if owner is logged in
if (!isset($_SESSION['person_id'])) {
    die("You must be logged in as an owner.");
}

$ownerID = $_SESSION['person_id'];

try {
    $conn = new PDO(
        "mysql:host=$servername;dbname=$dbname;charset=utf8",
        $username,
        $password
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "
        SELECT R.RequestID, R.StudentID, R.HouseID, H.StreetNumber, H.StreetName, H.City,
               R.RoomNo, R.Status, R.RequestDate,
               SP.FName AS StudentFName, SP.LName AS StudentLName,
               ROUND(AVG(F.Rating), 1) AS AvgRating,
               COUNT(F.Rating) AS RatingCount
        FROM Request R
        JOIN House H ON R.HouseID = H.HouseID
        JOIN Person SP ON R.StudentID = SP.PersonID
        LEFT JOIN PersonWritesFeedback F ON F.PersonID = R.StudentID
        WHERE H.OwnerID = :ownerID
        GROUP BY R.RequestID
        ORDER BY R.RequestDate ASC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute([':ownerID' => $ownerID]);
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Room Requests</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<div class="container">

    <div style="margin-bottom:40px;">
        <h1>Room Requests</h1>
        <p>Review and manage student room requests</p>
    </div>

    <?php if (empty($requests)): ?>
        <div class="card center">
            <p>No requests found.</p>
        </div>
    <?php else: ?>

        <div class="results-grid">

            <?php foreach ($requests as $req): ?>
                <div class="room-card">

                    <div class="room-header">
                        <h3>
                            <?= htmlspecialchars($req['StudentFName'] . ' ' . $req['StudentLName']) ?>
                        </h3>
                        <span class="price">
                            <?= strtoupper($req['Status']) ?>
                        </span>
                    </div>

                    <p class="city">
                        <?= $req['StreetNumber'] . ' ' . $req['StreetName'] . ', ' . $req['City'] ?>
                    </p>

                    <div class="room-meta">
                        <span>Room #<?= $req['RoomNo'] ?></span>
                        <span><?= $req['RequestDate'] ?></span>
                    </div>

                    <div class="room-meta">
                        <?php if ($req['RatingCount'] > 0): ?>
                            <span>
                                ⭐ <?= $req['AvgRating'] ?>/5
                                (<?= $req['RatingCount'] ?> reviews)
                            </span>
                        <?php else: ?>
                            <span>No reviews yet</span>
                        <?php endif; ?>
                    </div>

                    <?php if ($req['Status'] === 'pending'): ?>
                        <form method="post" action="approveRequest.php">
                            <input type="hidden" name="RequestID" value="<?= $req['RequestID'] ?>">
                            <button type="submit" class="btn request-btn">
                                Approve Request
                            </button>
                        </form>

                        <form method="post" action="rejectRequest.php" style="margin-top:10px;">
                            <input type="hidden" name="RequestID" value="<?= $req['RequestID'] ?>">
                            <button type="submit" class="btn request-btn"
                                style="background: linear-gradient(135deg, #ef4444, #f87171);">
                                Reject Request
                            </button>
                        </form>
                    <?php else: ?>
                        <p class="small">
                            Request already <?= htmlspecialchars($req['Status']) ?>
                        </p>
                    <?php endif; ?>

                </div>
            <?php endforeach; ?>

        </div>

    <?php endif; ?>

    <div class="small">
        <a href="owner_dashboard.php">← Back to Dashboard</a>
    </div>

</div>

</body>
</html>
