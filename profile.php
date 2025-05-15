<?php
session_start();
include 'db_connection.php';
// Database connection

// Check if user is logged in
if (!isset($_SESSION['userid'])) {
    header("Location: index.php");
    exit();
}

// Fetch user details
$userid = $_SESSION['userid'];
$query = "SELECT u.Userid, u.Name, u.Pwd, u.Address, d.DeptName 
          FROM dbo.Userinfo u 
          LEFT JOIN dbo.Dept d ON u.Deptid = d.Deptid 
          WHERE u.Userid = :userid";
$stmt = $conn->prepare($query);
$stmt->execute([':userid' => $userid]);
$user = $stmt->fetch();

// Update user details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $password = trim($_POST['password']);
    $email = trim($_POST['email']);
    $deptid = trim($_POST['deptid']);

    // Error handling
    $errors = [];
    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Hashing the password

        $updateQuery = "UPDATE dbo.Userinfo 
                        SET Name = :name, Pwd = :password, Address = :email, Deptid = :deptid 
                        WHERE Userid = :userid";
        $updateStmt = $pdo->prepare($updateQuery);
        $updateStmt->execute([
            ':name' => $name,
            ':password' => $hashedPassword,
            ':email' => $email,
            ':deptid' => $deptid,
            ':userid' => $userid
        ]);

        echo "<p>Profile updated successfully!</p>";
    } else {
        foreach ($errors as $error) {
            echo "<p style='color: red;'>$error</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        form { max-width: 400px; margin: auto; }
        label { display: block; margin-top: 10px; }
        input, select { width: 100%; padding: 8px; margin-top: 5px; }
        button { margin-top: 15px; padding: 10px 20px; background-color: #4CAF50; color: white; border: none; cursor: pointer; }
        button:hover { background-color: #45a049; }
    </style>
</head>
<body>
    <h1>User Profile</h1>
    <form method="POST">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['Name']); ?>" required>

        <label for="department">Department:</label>
        <select id="department" name="deptid" required>
            <?php
            // Fetch all departments
            $deptQuery = "SELECT Deptid, DeptName FROM dbo.Dept";
            $deptStmt = $pdo->query($deptQuery);
            while ($dept = $deptStmt->fetch()) {
                $selected = ($dept['DeptName'] === $user['DeptName']) ? "selected" : "";
                echo "<option value='{$dept['Deptid']}' $selected>{$dept['DeptName']}</option>";
            }
            ?>
        </select>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['Address']); ?>" required>

        <button type="submit">Update Profile</button>
    </form>
</body>
</html>
