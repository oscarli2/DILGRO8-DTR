<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles-login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.css">
</head>
<body>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <div class="login-container">    
    <img src="https://drive.google.com/thumbnail?id=1rNAtss3r0fr-LtCZ9amzXejiw7RITArH" width="40%" style="display: block; margin-left: auto; margin-right: auto; margin-bottom:30px;">
        <h1>Attendance Viewer System</h1>
        <h6>DILG Region 8 | RICTU</h6>
        <form method="POST" action="login.php">
            <label for="userid">User ID:</label>
            <input type="text" id="userid" name="userid" required>  

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>  

            <input type="submit" id="login" value="Login">
            <input type="button" id="reg" value="Register" onclick="location.href='register.php';">
        </form>
        <?php

        session_start();

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            include 'db_connection.php';
            $userid = $_POST['userid'];
            $password = $_POST['password'];
            
            try {
                // Prepare the SQL statement
                $stmt = $conn->prepare("SELECT * FROM Userinfo WHERE Userid = :userid");
                $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
                $stmt->execute();
                
                // Fetch the user record
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (is_null($user['Pwd'])){ 
                    exit;
                } else {
                    // Check if user record exists and verify password
                    if ($user && $user['Pwd'] === $password) {
                        $_SESSION['userid'] = $userid;
                        $_SESSION['admin'] = $user['admingroupid'];
                        $_SESSION['email'] = $user['Address']; // Assuming the email is stored in the 'Email' field
                        echo '<script>swal("Success!", "Login successful!", "success").then(() => {
                            window.location.href = "index.php";
                        });</script>';
                    } else {
                        echo '<script>swal("Error!", "Username or Password is incorrect!", "error");</script>';
                    }
                }
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        }
        ?>
    </div>
</body>
</html>
