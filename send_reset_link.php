<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Check if the email exists in the database
    $stmt = $conn->prepare("SELECT * FROM Userinfo WHERE Address = :email");
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Generate a unique password reset token
        $token = bin2hex(random_bytes(50));
        $reset_link = "localhost/DILGRO8-DTR/reset_password.php?token=" . $token;

        // Calculate the expiration time (1 hour from now)
        $expiry_time = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Store the token in the database with an expiration time
        $stmt = $conn->prepare("UPDATE Userinfo SET reset_token = :token, reset_token_expiry = :expiry_time WHERE Address = :email");
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->bindParam(':expiry_time', $expiry_time, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute;
        // Send the password reset link to the user's email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'dilgr8rictu@gmail.com'; // Your Gmail address
            $mail->Password   = 'hbdt fufg qpkf zjzd'; // Your Gmail password
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            //Recipients
            $mail->setFrom('dilgr8rictu@gmail.com', 'RICTU R8');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'DILG R8 Attendance Viewer System | Password Reset Request';
            $mail->Body    = 'Click on this link to reset your password: <a href="' . $reset_link . '">Reset Password</a>';

            $mail->send();
            echo json_encode(array('success' => true));
        } catch (Exception $e) {
            echo json_encode(array('success' => false, 'message' => $mail->ErrorInfo));
        }
    } else {
        echo json_encode(array('success' => false, 'message' => 'Email not found!'));
    }
}
?>
