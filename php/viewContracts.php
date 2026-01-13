<?php
session_start();
require 'config.php';

// Make sure owner is logged in
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
        SELECT c.StudentID, c.StartDate, c.EndDate,
               p.FName AS StudentFName, p.LName AS StudentLName
        FROM Contract c
        JOIN Person p ON c.StudentID = p.PersonID
        WHERE c.OwnerID = :oid
        AND (c.EndDate IS NULL OR c.EndDate > CURDATE())
        ORDER BY c.StartDate DESC
    ";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':oid' => $ownerID]);
    $contracts = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Current Contracts</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>

<div class="container">

    <div style="margin-bottom:40px;">
        <h1>Current Contracts</h1>
        <p>Students currently staying at your property</p>
    </div>

    <?php if (empty($contracts)): ?>
        <div class="card center">
            <p>No active contracts at the moment.</p>
        </div>
    <?php else: ?>

        <div class="results-grid">
            <?php foreach ($contracts as $c): ?>
                <div class="room-card">

                    <div class="room-header">
                        <h3>
                            <?= htmlspecialchars($c['StudentFName'] . ' ' . $c['StudentLName']) ?>
                        </h3>
                        <span class="price">ACTIVE</span>
                    </div>

                    <p class="city">Student</p>

                    <div class="room-meta">
                        <span>Start: <?= $c['StartDate'] ?></span>
                        <span>
                            End:
                            <?= $c['EndDate'] ? $c['EndDate'] : 'Ongoing' ?>
                        </span>
                    </div>

                </div>
            <?php endforeach; ?>
        </div>

    <?php endif; ?>

    <div class="small">
        <a href="../owner_dashboard.php">← Back to Dashboard</a>
    </div>

</div>

</body>
</html>
