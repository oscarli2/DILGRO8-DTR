<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
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
        input[type="email"] {
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
        <h1>Forgot Password</h1>
        <p>Please enter your registered email address to reset your password.</p>
        <form id="forgotPasswordForm" method="POST">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <input type="submit" value="Send Reset Link">
        </form>
    </div>
    <script>
        document.getElementById('forgotPasswordForm').addEventListener('submit', function(event) {
            event.preventDefault();
            var email = document.getElementById('email').value;

            console.log('Sending request to forgot_password_handler.php with email:', email);

            var xhr = new XMLHttpRequest();
            xhr.open("POST", "forgot_password_handler.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    console.log('Response from forgot_password_handler.php:', response);

                    if (response.success) {
                        swal({
                            title: "Confirm",
                            text: "Is this your name: " + response.name + "?",
                            icon: "warning",
                            buttons: true,
                            dangerMode: true,
                        }).then((willProceed) => {
                            if (willProceed) {
                                console.log('User confirmed the name. Sending request to send_reset_link.php.');

                                var confirmXhr = new XMLHttpRequest();
                                confirmXhr.open("POST", "send_reset_link.php", true);
                                confirmXhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                                confirmXhr.onreadystatechange = function () {
                                    if (confirmXhr.readyState === 4 && confirmXhr.status === 200) {
                                        var confirmResponse = JSON.parse(confirmXhr.responseText);
                                        console.log('Response from send_reset_link.php:', confirmResponse);

                                        if (confirmResponse.success) {
                                            swal("Success!", "Password reset link has been sent to your email.", "success").then(() => {
                                                window.location.href = "reset_password_handler.php";
                                            });
                                        } else {
                                            swal("Error!", "Failed to send reset link. " + confirmResponse.message, "error");
                                        }
                                    }
                                };
                                confirmXhr.send("email=" + encodeURIComponent(email));
                            } else {
                                swal("Cancelled", "Please re-enter your email address.", "error");
                            }
                        });
                    } else {
                        swal("Error!", "Email not found!", "error");
                    }
                }
            };
            xhr.send("email=" + encodeURIComponent(email));
        });
    </script>
</body>
</html>
