<?php
require 'php/config.php';

try {
    $conn = new PDO(
        "mysql:host=$servername;dbname=$dbname;charset=utf8",
        $username,
        $password
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->query("
        SELECT SchoolID, SchoolName
        FROM School
        ORDER BY SchoolName
    ");

    $schools = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>HomeStay Organizer Company Registration</h1>

        <!-- Grid container -->
        <div class="grid-2">

            <!-- Student Registration Card -->
            <div class="card">
                <h2>Student Registration</h2>
                <form action="php/registerStudent.php" method="post">
                    <input type="hidden" name="role" value="student">

                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" name="firstName" required>
                    </div>

                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" name="lastName" required>
                    </div>

                    <div class="form-group select-wrapper">
                        <label for="student-gender">Gender</label>
                        <select name="gender" id="student-gender" required>
                            <option value="">--Select Gender--</option>
                            <option value="M">Male</option>
                            <option value="F">Female</option>
                            <option value="O">Other</option>
                        </select>
                    </div>

                    <div class="form-group select-wrapper">
                        <label>Select Your School</label>
                        <select name="SchoolID" required>
                            <option value="">-- Select School --</option>
                            <?php foreach ($schools as $school): ?>
                                <option value="<?= $school['SchoolID'] ?>">
                                    <?= htmlspecialchars($school['SchoolName']) ?> (<?= $school['SchoolID'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Date of Birth</label>
                        <input type="date" name="dob" required>
                    </div>

                    <div class="form-group select-wrapper">
                        <label for="student-nationality">Nationality</label>
                        <select name="nationality" id="student-nationality" required>
                            <option value="">--Select Nationality--</option>
                            <option value="CA">Canada</option>
                            <option value="IN">India</option>
                            <option value="US">America</option>
                            <option value="AU">Australia</option>
                            <option value="FR">France</option>
                            <option value="DE">Germany</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Duration Of Studies In Years</label>
                        <input type="number" name="duration" required>
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label>Create a Password</label>
                        <input type="password" name="password" required>
                    </div>

                    <button type="submit" class="btn">Register as a Student</button>
                </form>
            </div>

            <!-- Owner Registration Card -->
            <div class="card">
                <h2>Owner Registration</h2>
                <form action="php/registerOwner.php" method="post">
                    <input type="hidden" name="role" value="owner">

                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" name="firstName" required>
                    </div>

                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" name="lastName" required>
                    </div>

                    <div class="form-group select-wrapper">
                        <label for="owner-gender">Gender</label>
                        <select name="gender" id="owner-gender" required>
                            <option value="">--Select Gender--</option>
                            <option value="M">Male</option>
                            <option value="F">Female</option>
                            <option value="O">Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Date of Birth</label>
                        <input type="date" name="dob" required>
                    </div>

                    <div class="form-group">
                        <label>Occupation</label>
                        <input type="text" name="occupation" required>
                    </div>

                    <div class="form-group">
                        <label>Family Size</label>
                        <input type="number" name="fSize">
                    </div>

                    <div class="form-group">
                        <label>Language Spoken</label>
                        <input type="text" name="Language">
                    </div>

                    <div class="form-group">
                        <label>Biography</label>
                        <textarea name="bio" placeholder="Tell us about yourself..."></textarea>
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label>Create a Password</label>
                        <input type="password" name="password" required>
                    </div>

                    <button type="submit" class="btn">Register as an Owner</button>
                </form>
            </div>

        </div>

        <br>
        <a href="index.html">Back to Home</a>
    </div>
</body>
</html>
