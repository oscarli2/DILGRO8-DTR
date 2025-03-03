<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="styles-login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.css">
    <style>
        /* Basic resets and universal styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: #f4f4f4;
        }

        /* Container styling */
        .login-container {
            background: #fff;
            padding: 20px 40px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        /* Heading styling */
        .login-container h1 {
            margin-bottom: 20px;
            color: #333;
        }

        .login-container p {
            margin-bottom: 20px;
            color: #555;
        }

        /* Form styling */
        form {
            display: flex;
            flex-direction: column;
        }

        /* Label styling */
        label {
            margin: 10px 0 5px;
            text-align: left;
            color: #555;
        }

        /* Input styling */
        input[type="password"] {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 16px;
        }

        /* Button styling */
        input[type="submit"] {
            padding: 10px;
            border: none;
            border-radius: 4px;
            background: #5cb85c;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        input[type="submit"]:hover {
            background: #4cae4c;
        }

        /* Link styling */
        a {
            text-decoration: none;
            color: #0275d8;
            font-size: 14px;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <div class="login-container">
        <h1>Reset Password</h1>
        <form method="POST" action="reset_password_handler.php">
            <input type="hidden" name="token" value="<?php echo $_GET['token']; ?>">
            <label for="password">New Password:</label>
            <input type="password" id="password" name="password" required>
            <input type="submit" value="Reset Password">
        </form>
    <div>
    <?php
        
        include 'db_connection.php';
        $stmt = $conn->prepare("SELECT * FROM Userinfo WHERE Address = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $user1 = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user1) {
            $email = $user1['email'];
        }
    ?>
</body>
</html>
