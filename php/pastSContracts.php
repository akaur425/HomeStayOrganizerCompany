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

    // Past contracts (EndDate in the past)
    $sql = "
        SELECT c.OwnerID, h.StreetNumber, h.StreetName, h.City, c.StartDate, c.EndDate,
               p.FName AS OwnerFName, p.LName AS OwnerLName
        FROM Contract c
        JOIN Person p ON c.OwnerID = p.PersonID
        JOIN House h ON h.OwnerID = c.OwnerID 
        WHERE c.StudentID = :sid
        AND c.EndDate IS NOT NULL
        ORDER BY c.EndDate DESC
    ";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':sid' => $studentID]);
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
        <p>Previous housing agreements you have completed</p>
    </div>

    <?php if (empty($contracts)): ?>

        <div class="card">
            <h2>No Past Contracts</h2>
            <p>You don’t have any completed housing contracts yet.</p>
        </div>

    <?php else: ?>

        <div class="results-grid">

            <?php foreach ($contracts as $c): ?>
                <div class="room-card">

                    <!-- HEADER -->
                    <div class="room-header">
                        <h3>
                            <?= htmlspecialchars($c['StreetNumber'] . ' ' . $c['StreetName']) ?>
                        </h3>
                        <span class="price">
                            Completed
                        </span>
                    </div>

                    <!-- LOCATION -->
                    <p class="city"><?= htmlspecialchars($c['City']) ?></p>

                    <!-- META -->
                    <div class="room-meta">
                        <span>
                            👤 <?= htmlspecialchars($c['OwnerFName'] . ' ' . $c['OwnerLName']) ?>
                        </span>
                        <span>
                            📅 <?= htmlspecialchars($c['StartDate']) ?>
                        </span>
                    </div>

                    <p class="small">
                        Ended on <?= htmlspecialchars($c['EndDate']) ?>
                    </p>

                </div>
            <?php endforeach; ?>

        </div>

    <?php endif; ?>

    <div class="small" style="margin-top: 40px;">
        <a href="../student_dashboard.php">← Back to Dashboard</a>
    </div>

</div>
</body>
</html>
