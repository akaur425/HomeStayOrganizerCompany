<?php
session_start();
require 'php/config.php';

try {
    $conn = new PDO(
        "mysql:host=$servername;dbname=$dbname;charset=utf8",
        $username,
        $password
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->prepare("
        SELECT DISTINCT
            c.ContractID,
            h.StreetNumber,
            h.StreetName,
            h.City,
            p.fname AS owner_fname,
            p.lname AS owner_lname
        FROM Contract c
        JOIN Student_RoomHouse sr ON sr.StudentID = c.StudentID
        JOIN House h ON h.HouseID = sr.HouseID
        JOIN Person p ON p.PersonID = c.OwnerID
        WHERE c.StudentID = :studentID
    ");
    $stmt->execute([':studentID' => $_SESSION['person_id']]);
    $contracts = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Insert Operations</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .table-fields {
            display: none;
        }
    </style>
</head>

<body>

<div class="container">

    <div style="margin-bottom: 40px;">
        <h1>Insert for Students</h1>
        <p>Choose what information you’d like to add to your profile</p>
    </div>

    <div class="card">

        <form method="post" action="php/insertStudentData.php">

            <!-- TABLE SELECT -->
            <div class="form-group">
                <label>Select Table</label>
                <div class="select-wrapper">
                    <select id="tableSelect" name="table" required>
                        <option value="">-- Select Table --</option>
                        <option value="PersonWritesFeedback">Leave Feedback</option>
                        <option value="StudentHobbies">Add Hobby</option>
                        <option value="StudentInterests">Add Interest</option>
                    </select>
                </div>
            </div>

            <!-- PERSON WRITES FEEDBACK -->
            <div class="table-fields" id="PersonWritesFeedback">

                <div class="form-group">
                    <label>Where did you stay?</label>
                    <div class="select-wrapper">
                        <select name="ContractID">
                            <option value="">-- Select Contract --</option>
                            <?php foreach ($contracts as $c): ?>
                                <option value="<?= $c['ContractID'] ?>">
                                    <?= $c['StreetNumber'] . ' ' . $c['StreetName'] . ', ' . $c['City'] ?>
                                    — Owner: <?= $c['owner_fname'] . ' ' . $c['owner_lname'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Rating</label>
                    <input type="number" step="0.1" name="Rating" placeholder="e.g. 4.5">
                </div>

                <div class="form-group">
                    <label>Feedback</label>
                    <textarea name="FeedbackText" placeholder="Share your experience"></textarea>
                </div>

                <div class="form-group">
                    <label>Date</label>
                    <input type="date" name="FeedbackDate">
                </div>
            </div>

            <!-- STUDENT HOBBIES -->
            <div class="table-fields" id="StudentHobbies">
                <div class="form-group">
                    <label>Hobby</label>
                    <input type="text" name="Hobby" placeholder="e.g. Gym, Reading">
                </div>
            </div>

            <!-- STUDENT INTERESTS -->
            <div class="table-fields" id="StudentInterests">
                <div class="form-group">
                    <label>Interest</label>
                    <input type="text" name="Interest" placeholder="e.g. AI, Music">
                </div>
            </div>

            <button type="submit" class="btn">Insert Data</button>

        </form>
    </div>

    <div class="small">
        <a href="student_dashboard.php">← Back to Dashboard</a>
    </div>

</div>

<script>
const tableSelect = document.getElementById('tableSelect');

tableSelect.addEventListener('change', () => {
    document.querySelectorAll('.table-fields').forEach(div => {
        div.style.display = 'none';
    });

    if (tableSelect.value) {
        document.getElementById(tableSelect.value).style.display = 'block';
    }
});
</script>

</body>
</html>
