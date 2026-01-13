<!doctype html>
<html>

<head>
    <title>Insert a Record</title>
    <link rel="stylesheet" href="../css/style.css" />
</head>

<body>
    <div class="container">
<?php
session_start();
require 'config.php';

/* ---------------- CONNECT ---------------- */
try {
    $conn = new PDO(
        "mysql:host=$servername;dbname=$dbname;charset=utf8",
        $username,
        $password
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("<p style='color:red'>DB Connection Failed: " . $e->getMessage() . "</p>");
}

/* ---------------- ROUTE BY TABLE ---------------- */
if (!isset($_POST['table'])) {
    die("<p style='color:red'>No table selected.</p>");
}

$table = $_POST['table'];

try {
    /* -------- SCHOOL -------- */

    /* -------- FEEDBACK -------- */

    /*
    select contractID FROM Contract Join Person ON Contract.OwnerID = Person.PersonID
    Where Person.FName = $_POST['OName'];
    */
    if ($table === "PersonWritesFeedback") {
        $sql = "INSERT INTO PersonWritesFeedback
                (PersonID, ContractID, Rating, CommentText, CommentDate)
                VALUES (:pid, :cid, :r, :t, :d)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':pid' => $_SESSION['person_id'],
            ':cid' => $_POST['ContractID'],
            ':r'   => $_POST['Rating'],
            ':t'   => $_POST['FeedbackText'],
            ':d'   => $_POST['FeedbackDate']
        ]);
    }

    /* -------- HOBBIES -------- */
    elseif ($table === "StudentHobbies") {
        $sql = "INSERT INTO StudentHobbies (StudentID, Hobbies)
                VALUES (:sid, :h)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':sid' => $_SESSION['person_id'],
            ':h'   => $_POST['Hobby']
        ]);
    }

    /* -------- INTERESTS -------- */
    elseif ($table === "StudentInterests") {
        $sql = "INSERT INTO StudentInterests (StudentID, Interests)
                VALUES (:sid, :i)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':sid' => $_SESSION['person_id'],
            ':i'   => $_POST['Interest']
        ]);
    }

    else {
        throw new Exception("Unknown table selected.");
    }

    echo "
        <div class='card'>
            <h2>Success 🎉</h2>
            <p>Your information was added successfully.</p>

            <div style='margin-top: 20px; display: flex; gap: 16px; flex-wrap: wrap;'>
                <a href='../insertStudentOperations.php' class='btn'>Insert More</a>
                <a href='../student_dashboard.php' class='btn' style='background: #e5e7eb; color: #0f172a;'>Back to Dashboard</a>
            </div>
        </div>
    ";

} catch (Exception $e) {
    echo "<p style='color:red'>Insert failed: " . $e->getMessage() . "</p>
     <div style='margin-top: 20px; display: flex; gap: 16px; flex-wrap: wrap;'>
                
                <a href='../student_dashboard.php' class='btn' style='background: #e5e7eb; color: #0f172a;'>Back to Dashboard</a>
            </div>";
}

$conn = null;
?>
</div>
</body>
</html>