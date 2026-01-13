<?php
session_start();
require 'config.php';

try {
    $conn = new PDO(
        "mysql:host=$servername;dbname=$dbname;charset=utf8",
        $username,
        $password
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    /* -------------------------------
       CASE 1: Search by CITY only
       ------------------------------- */
    if (isset($_POST['cityName'])&& !isset($_POST['maxPrice'])) {

        $city = $_POST['cityName'];

        $sql = "
            SELECT p.FName, p.LName, h.OwnerID, r.RoomNo, r.Price, r.Size, h.HouseID, h.City, h.StreetName, h.StreetNumber, ROUND(AVG(F.Rating), 1) AS AvgRating,
    COUNT(F.Rating) AS RatingCount
            FROM RoomHouse r
            JOIN House h ON r.HouseID = h.HouseID
            JOIN Person p On p.PersonID = h.OwnerID
            LEFT JOIN PersonWritesFeedback F 
ON F.PersonID = h.OwnerID
            WHERE h.City = :city AND r.isAvailable = 1
            GROUP BY  h.OwnerID, r.RoomNo, r.Price, r.Size, h.HouseID, h.City, h.StreetName, h.StreetNumber
        ";

        $stmt = $conn->prepare($sql);
        $stmt->execute([':city' => $city]);
        $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ------------------------------------------
       CASE 2: Search by PRICE + SMOKE + PETS
       ------------------------------------------ */
    elseif (isset($_POST['maxPrice'], $_POST['smoke'], $_POST['pets'])) {

        $maxPrice = $_POST['maxPrice'];
        $smoke = (int) $_POST['smoke'];
        $pets = (int) $_POST['pets'];

        $sql = "
            SELECT p.FName, p.LName, h.OwnerID, r.RoomNo, r.Price, r.Size, h.HouseID, h.StreetName, h.StreetNumber, h.City, ROUND(AVG(F.Rating), 1) AS AvgRating,
    COUNT(F.Rating) AS RatingCount
            FROM RoomHouse r
            JOIN House h ON r.HouseID = h.HouseID
            JOIN Person p On p.PersonID = h.OwnerID
            LEFT JOIN PersonWritesFeedback F 
ON F.PersonID = h.OwnerID
            WHERE r.Price <= :maxPrice
              AND h.isSmoking = :smoke
              AND h.isPets = :pets
              AND r.isAvailable = 1
              GROUP BY h.OwnerID, r.RoomNo, r.Price, r.Size, h.HouseID, h.StreetName, h.StreetNumber, h.City
        ";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':maxPrice' => $maxPrice,
            ':smoke' => $smoke,
            ':pets' => $pets
        ]);

        $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ---------------------------------------
       CASE 3: Cheapest room in a city
       --------------------------------------- */
    elseif (isset($_POST['cityN'])) {

        $cityN = $_POST['cityN'];

        $sql = "
            SELECT  p.FName, p.LName, H.OwnerID, RH.RoomNo, RH.Size, RH.HouseID, RH.Price, H.City, H.StreetNumber, H.StreetName, ROUND(AVG(F.Rating), 1) AS AvgRating,
    COUNT(F.Rating) AS RatingCount
FROM RoomHouse RH
JOIN House H ON RH.HouseID = H.HouseID
JOIN Person p On p.PersonID = H.OwnerID
LEFT JOIN PersonWritesFeedback F 
ON F.PersonID = H.OwnerID
WHERE H.City = :cityN 
AND RH.isAvailable = 1
AND RH.Price = (SELECT MIN(Price) FROM RoomHouse JOIN House ON RoomHouse.HouseID = House.HouseID WHERE House.City = :cityN)
GROUP BY H.OwnerID, RH.RoomNo, RH.Size, RH.HouseID, RH.Price, H.City, H.StreetNumber, H.StreetName
        ";

        $stmt = $conn->prepare($sql);
        $stmt->execute([':cityN' => $cityN]);
        $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    else {
        die("Invalid request.");
    }

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Room Results</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="container">

    <h1 style="text-align:center; margin-bottom:40px;">Available Rooms</h1>

    <?php if (empty($rooms)): ?>
        <p>No rooms found.</p>
    <?php else: ?>

        <div class="results-grid">

            <?php foreach ($rooms as $room): ?>
                <div class="room-card">

                    <div class="room-header">
                        <h3><?= $room['StreetNumber'] ?> <?= $room['StreetName'] ?></h3>
                        <span>Owner: <?= htmlspecialchars($room['FName'] . ' ' . $room['LName']) ?></span>
                        <br>
                        <span class="price">$<?= $room['Price'] ?>/mo</span>
                    </div>

                    <p class="city"><?= htmlspecialchars($room['City']) ?></p>


                    <div class="room-meta">
                        <span>📐 <?= $room['Size'] ?> sqft</span>
                        <span>
                            ⭐ 
                            <?php if ($room['RatingCount'] > 0): ?>
                                <?= $room['AvgRating'] ?> (<?= $room['RatingCount'] ?>)
                            <?php else: ?>
                                No reviews
                            <?php endif; ?>
                        </span>
                    </div>

                    <form method="post" action="confirmRoom.php">
                        <input type="hidden" name="HouseID" value="<?= $room['HouseID'] ?>">
                        <input type="hidden" name="RoomNo" value="<?= $room['RoomNo'] ?>">
                        <input type="hidden" name="OwnerID" value="<?= $room['OwnerID'] ?>">

                        <button class="btn request-btn">Request Room</button>
                    </form>

                </div>
            <?php endforeach; ?>

        </div>

    <?php endif; ?>

    <a href="../requestRoom.html">← Back</a>

</div>

</body>

</html>
