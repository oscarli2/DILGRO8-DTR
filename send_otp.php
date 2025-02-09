<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require 'PHPMailer/src/Exception.php';
    require 'PHPMailer/src/PHPMailer.php';
    require 'PHPMailer/src/SMTP.php';

$data = json_decode(file_get_contents('php://input'), true);
$email = $data['email'];
$otp = $data['otp'];

$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'dilgr8rictu@gmail.com'; // Your Gmail address
    $mail->Password   = 'hbdt fufg qpkf zjzd'; // Your Gmail password
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    //Recipients
    $mail->setFrom('dilgr8rictu@gmail.com', 'RICTU R8');
    $mail->addAddress($data['email']);

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'DILG R8 Attendance Viewer System | Your OTP Code';
    $mail->Body    = 'Thank you for registering! Your OTP code is ' . $otp;

    $mail->send();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $mail->ErrorInfo]);
}
?>
