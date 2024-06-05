<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.css">
</head>
<body>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script>
        function register() {
            swal({
                title: "Registration",
                text: "Enter your username and password",
                content: {
                    element: "div",
                    attributes: {
                        innerHTML: `
                            <form id="registrationForm">
                                <input type="text" id="username" name="username" placeholder="Username" required>
                                <input type="password" id="password" name="password" placeholder="Password" required>
                            </form>
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

        document.addEventListener("DOMContentLoaded", function() {
            register();
        });
    </script>
</body>
</html>
