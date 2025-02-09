<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $otp = $_POST['otp'];

    if ($otp == $_SESSION['otp']) {
        $_SESSION['logged_in'] = true;
        header("Location: index.php");
        exit;
    } else {
        echo '<script>swal("Error!", "Invalid OTP. Please try again.", "error");</script>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <link rel="stylesheet" href="styles-login.css">
</head>
<body>
    <div class="verify-container">
        <h1>Verify OTP</h1>
        <form method="POST" action="verify_otp.php">
            <label for="otp">OTP:</label>
            <input type="text" id="otp" name="otp" required>  
            <input type="submit" value="Verify OTP">
        </form>
    </div>
</body>
</html>
