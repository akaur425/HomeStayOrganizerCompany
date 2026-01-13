<?php
session_start();

if (!isset($_SESSION['person_id'])) {
    header("Location: index.html");
    exit;
}
$name = $_SESSION['name'] ?? 'User';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .topbar {
    position: fixed;
    top: 0;
    right: 0;
    left: 0;

    height: 64px;
    background: rgba(255, 255, 255, 0.85);
    backdrop-filter: blur(10px);
    border-bottom: 1px solid var(--border);

    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 20px;

    padding: 0 32px;
    z-index: 1000;
}

.topbar-user {
    font-weight: 500;
    color: var(--text-main);
}

.logout-btn {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
    border: none;
    border-radius: 10px;
    padding: 8px 14px;
    cursor: pointer;
    font-size: 0.85rem;
    transition: transform 0.2s ease, opacity 0.2s ease;
}

.logout-btn:hover {
    transform: translateY(-1px);
    opacity: 0.95;
}

.container {
    padding-top: 96px;
}

    </style>
</head>
<body>
<div class="topbar">
    <div class="topbar-user">
        👤 <?= htmlspecialchars($name) ?>
    </div>

    <form action="logout.php" method="post">
        <button type="submit" class="logout-btn">Logout</button>
    </form>
</div>
<div class="container">
    <!-- Header -->
    <div style="margin-bottom: 40px;">
        <h1>Student Dashboard</h1>
        <p>Manage your room, requests, and contracts in one place</p>
    </div>

    <!-- MAIN ACTIONS -->
    <h2 style="margin-bottom: 15px;">Quick Actions</h2>
    <div class="grid-2">

        <a href="requestRoom.html" class="card action-card">
            <h3>🏠 Request a Room</h3>
            <p>Browse available rooms and send a request</p>
        </a>

        <a href="leaveRoom.html" class="card action-card">
            <h3>🚪 Leave a Room</h3>
            <p>End your current stay and notify the owner</p>
        </a>

    </div>

    <!-- REQUESTS -->
    <h2 style="margin: 50px 0 15px;">Requests</h2>
    <div class="grid-2">

        <a href="insertStudentOperations.php" class="card action-card">
            <h3>✍️ Student Operations</h3>
            <p>Submit or update student-related actions</p>
        </a>

        <a href="viewRequests.php" class="card action-card">
            <h3>📩 View Requests</h3>
            <p>Check the status of your room requests</p>
        </a>

    </div>

    <!-- CONTRACTS -->
    <h2 style="margin: 50px 0 15px;">Contracts</h2>
    <div class="grid-2">

        <a href="php/viewSContracts.php" class="card action-card">
            <h3>📄 Current Contracts</h3>
            <p>View your active housing contracts</p>
        </a>

        <a href="php/pastSContracts.php" class="card action-card">
            <h3>🕘 Past Contracts</h3>
            <p>Review your previous stays and history</p>
        </a>

    </div>
</div>

</body>
</html>
