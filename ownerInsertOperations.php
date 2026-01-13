<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require 'php/config.php';

try {
    $conn = new PDO(
        "mysql:host=$servername;dbname=$dbname;charset=utf8",
        $username,
        $password
    );

    $stmt = $conn->prepare("
    SELECT HouseID, StreetNumber, StreetName, City
    FROM House
    WHERE OwnerID = :oid
");
    $stmt->execute([
        ':oid' => $_SESSION['person_id']
    ]);

    $houses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $conn->prepare("
        SELECT 
    c.ContractID,
    h.StreetNumber,
    h.StreetName,
    h.City,
    p.fname AS student_fname,
    p.lname AS student_lname
FROM Contract c
JOIN House h ON c.OwnerID = h.OwnerID
JOIN Person p ON c.StudentID = p.PersonID
WHERE c.OwnerID = :ownerID;

    ");
    $stmt->execute([':ownerID' => $_SESSION['person_id']]);

    $contracts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Owner Insert Operations</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .table-fields {
            display: none;
        }
    </style>
</head>

<body>
    <div class="container">

    <div style="margin-bottom:40px;">
        <h1>Owner Insert Operations</h1>
        <p>Add houses, rooms, or leave feedback for students</p>
    </div>

    <div class="card">

        <form method="post" action="php/insertOwnerData.php">

            <!-- TABLE SELECT -->
            <div class="form-group">
                <label>Select Action</label>
                <div class="select-wrapper">
                    <select id="tableSelect" name="table" required>
                        <option value="">-- Select --</option>
                        <option value="House">Add House</option>
                        <option value="RoomHouse">Add Room</option>
                        <option value="PersonWritesFeedback">Leave Feedback</option>
                    </select>
                </div>
            </div>

            <!-- HOUSE -->
            <div class="table-fields" id="House">
                <div class="form-group">
                    <label>Street Number</label>
                    <input type="number" name="StreetNumber">
                </div>

                <div class="form-group">
                    <label>Street Name</label>
                    <input type="text" name="StreetName">
                </div>

                <div class="form-group">
                    <label>City</label>
                    <input type="text" name="City">
                </div>

                <div class="form-group">
                    <label>Zip Code</label>
                    <input type="text" name="ZipCode">
                </div>

                <div class="form-group">
                    <label>Pets Allowed</label>
                    <select name="IsPets">
                        <option value="0">No</option>
                        <option value="1">Yes</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Smoking Allowed</label>
                    <select name="IsSmoking">
                        <option value="0">No</option>
                        <option value="1">Yes</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Available Rooms</label>
                    <input type="number" name="NoOfAvailableRooms">
                </div>

                <div class="form-group">
                    <label>Total Rooms</label>
                    <input type="number" name="TotalNumberOfRooms">
                </div>
            </div>

            <!-- ROOM -->
            <div class="table-fields" id="RoomHouse">
                <div class="form-group">
                    <label>Room Number</label>
                    <input type="number" name="RoomNumber">
                </div>

                <div class="form-group">
                    <label>Select House</label>
                    <select name="HouseID">
                        <option value="">-- Select Your House --</option>
                        <?php foreach ($houses as $h): ?>
                            <option value="<?= $h['HouseID'] ?>">
                                <?= $h['StreetNumber'] . ' ' . $h['StreetName'] . ', ' . $h['City'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Rent</label>
                    <input type="number" step="0.01" name="Rent">
                </div>

                <div class="form-group">
                    <label>Room Size</label>
                    <input type="number" name="Size">
                </div>

                <div class="form-group">
                    <label>Available?</label>
                    <select name="Availability">
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Start Date</label>
                    <input type="date" name="StartDate">
                </div>

                <div class="form-group">
                    <label>End Date</label>
                    <input type="date" name="EndDate">
                </div>
            </div>

            <!-- FEEDBACK -->
            <div class="table-fields" id="PersonWritesFeedback">
                <div class="form-group">
                    <label>Select Contract</label>
                    <select name="ContractID">
                        <option value="">-- Select Contract --</option>
                        <?php foreach ($contracts as $c): ?>
                            <option value="<?= $c['ContractID'] ?>">
                                <?= $c['StreetNumber'] . ' ' . $c['StreetName'] . ', ' . $c['City'] ?>
                                — Student: <?= $c['student_fname'] . ' ' . $c['student_lname'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Rating</label>
                    <input type="number" step="0.1" name="Rating">
                </div>

                <div class="form-group">
                    <label>Feedback</label>
                    <textarea name="FeedbackText"></textarea>
                </div>

                <div class="form-group">
                    <label>Date</label>
                    <input type="date" name="FeedbackDate">
                </div>
            </div>

            <button type="submit" class="btn">Insert Data</button>

        </form>
    </div>

    <div class="small">
        <a href="owner_dashboard.php">← Back to Dashboard</a>
    </div>

</div>

<script>
const tableSelect = document.getElementById('tableSelect');

tableSelect.addEventListener('change', () => {
    document.querySelectorAll('.table-fields').forEach(div => div.style.display = 'none');
    if (tableSelect.value) {
        document.getElementById(tableSelect.value).style.display = 'block';
    }
});
</script>

</body>

</html>