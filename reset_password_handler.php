<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $new_password = $_POST['password'];
    $email = $_POST['email'];

     // Calculate the expiration time (1 hour from now)
    $expiry_time = date('Y-m-d H:i:s', strtotime('+1 hour'));
     // Store the token in the database with an expiration time
    $stmt = $conn->prepare("UPDATE Userinfo SET Remark = :token, Birthday = :expiry_time WHERE Address = :email");
    $stmt->bindParam(':token', $token, PDO::PARAM_STR);
    $stmt->bindParam(':expiry_time', $expiry_time, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();

    if ($email) {
        // Update the user's password in the database
        $stmt = $conn->prepare("UPDATE Userinfo SET Pwd = :password, Remark = NULL, Birthday = NULL WHERE Remark = :token");
        $stmt->bindParam(':password', $new_password, PDO::PARAM_STR);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->execute();

        echo '<script>
            alert("Password has been reset! You may now login!");
            window.location.href = "login.php";</script>';
    } else {
        echo 'alert("Error! Invalid or Expired Token!");</script>';
    }
}
?>
