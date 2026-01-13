<?php
session_start();
require 'config.php';

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

    // Past contracts (EndDate in the past)
    $sql = "
        SELECT c.StudentID, c.StartDate, c.EndDate,
               p.FName AS StudentFName, p.LName AS StudentLName
        FROM Contract c
        JOIN Person p ON c.StudentID = p.PersonID
        WHERE c.OwnerID = :oid
        AND c.EndDate IS NOT NULL
        ORDER BY c.EndDate DESC
    ";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':oid' => $ownerID]);
    $contracts = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Past Contracts</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="container">

    <!-- PAGE HEADER -->
    <div style="margin-bottom: 40px;">
        <h1>Past Contracts</h1>
        <p>Students who previously stayed with you</p>
    </div>

    <?php if (empty($contracts)): ?>

        <div class="card">
            <h2>No Past Contracts</h2>
            <p>You don’t have any completed contracts yet.</p>
        </div>

    <?php else: ?>

        <div class="results-grid">

            <?php foreach ($contracts as $c): ?>
                <div class="room-card">

                    <!-- HEADER -->
                    <div class="room-header">
                        <h3>
                            <?= htmlspecialchars($c['StudentFName'] . ' ' . $c['StudentLName']) ?>
                        </h3>
                        <span class="price">Completed</span>
                    </div>

                    <!-- META -->
                    <div class="room-meta">
                        <span>
                            📅 Start: <?= htmlspecialchars($c['StartDate']) ?>
                        </span>
                        <span>
                            📅 End: <?= htmlspecialchars($c['EndDate']) ?>
                        </span>
                    </div>

                </div>
                <br>
            <?php endforeach; ?>

        </div>

    <?php endif; ?>

    <div class="small" style="margin-top: 40px;">
        <a href="../owner_dashboard.php">← Back to Dashboard</a>
    </div>

</div>
</body>
</html>
