<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['person_id'])) {
    return;
}

$name = $_SESSION['name'] ?? 'User';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/style.css">
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

</body>
</html>


