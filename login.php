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
        <h1>Attendance Viewer Sytem</h1>
        <h6>DILG Region 8 | RICTU</h6>
        <form method="POST" action="login.php">
            <label for="userid">User ID:</label>
            <input type="text" id="userid" name="userid" required>  

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>  

            <input type="submit" id="login" value="Login">
            <input type="button" id="reg" value="Register" onclick="register();">
        </form>
        <?php
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
                        session_start();
                        $_SESSION['userid'] = $userid;
                        $_SESSION['admin'] = $user['admingroupid'];
                        header("Location: index.php");
                        exit;
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
    <script>
        function loginFailed(){
            swal("Error!", "Registration failed: ", "error"); 
        }
        function register() {
            swal({
                title: "Registration",
                text: "Enter your UserID and new password for your account",
                content: {
                    element: "div",
                    attributes: {
                        innerHTML: `
                            <form id="registrationForm">
                                <input type="text" id="username" name="username" placeholder="UserID" required>
                                <input type="password" id="password" name="password" placeholder="Password" required>
                            </form>
                            <font color="red" size="2"><i>(Please ask RICTU for your UserID)</i></font>
                        `
                    }
                },
                buttons: {
                    cancel: true,
                    confirm: {
                        text: "Register",
                        value: true,
                        visible: true,
                        className: "btn-primary",
                        closeModal: false
                    }
                }
            }).then((value) => {
                if (value) {
                    var form = document.getElementById("registrationForm");
                    var formData = new FormData(form);
                    var username = formData.get("username");
                    var password = formData.get("password");

                    var xhttp = new XMLHttpRequest();
                    xhttp.onreadystatechange = function() {
                        if (this.readyState == 4 && this.status == 200) {
                            var response = JSON.parse(this.responseText);
                            if (response.success) {
                                swal("Success!", "Registration successful!", "success").then(() => {
                                    window.location.href = 'login.php';
                                });
                            } else {
                                swal("Error!", "Registration failed: " + response.message, "error");
                            }
                        }
                    };
                    xhttp.open("POST", "register_handler.php", true);
                    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                    xhttp.send("userid=" + username + "&password=" + password);
                }
            });
        }
    </script>
</body>
</html>
