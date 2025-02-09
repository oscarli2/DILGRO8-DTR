<?php
include 'db_connection.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Page</title>
    <link rel="stylesheet" href="styles-reg.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.css">
    <style>
        /* Loading overlay and spinner styles */
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7); /* Semi-transparent black */
            z-index: 9999; /* Place above everything else */
            display: none; /* Hidden by default */
            justify-content: center; /* Center horizontally */
            align-items: center; /* Center vertically */
        }

        .loader {
            border: 16px solid #f3f3f3; /* Light grey */
            border-top: 16px solid #3498db; /* Blue */
            border-radius: 50%;
            width: 120px;
            height: 120px;
            animation: spin 2s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body onload="hideLoader()">
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <div class="container">  
    <a href="login.php"><i class="fa-solid fa-arrow-left"> Back</i></a>     
    <img src="https://drive.google.com/thumbnail?id=1rNAtss3r0fr-LtCZ9amzXejiw7RITArH" width="30%" style="display: block; margin-left: auto; margin-right: auto;">
        <h2><i class="fas fa-user"></i> Registration Page</h2>
        <div class="overlay" id="overlay">
            <div class="loader" id="loader"></div> <!-- Loading spinner -->
        </div>
        <form id="registrationForm" method="POST" action="">
            <div class="input-group">
                <label for="id">ID:</label>
                <input type="text" id="id" name="id" required>
                <button type="button" onclick="checkID()">Check ID</button>
            </div>
            <div id="detailsSection" style="display: none;">
                <div class="input-group" id="nameDisplay">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" readonly>
                </div>
                <div class="input-group">
                    <label for="password">Desired Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="input-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                    <button type="button" id="sendOtpButton" onclick="validateEmail()">Send OTP</button>
                </div>
                <div class="input-group" id="otpSection" style="display: none;">
                    <label for="otp">Enter OTP:</label>
                    <input type="text" id="otp" name="otp" required>
                    <button type="button" onclick="verifyOTP()">Verify & Register</button>
                </div>
            </div>
            
        </form>
        <center><p>Lost your ID Number? <a href='https://docs.google.com/spreadsheets/d/e/2PACX-1vQUM25yrfwYdz-GdKUlizvpjkcIslCUOjsZVgHmA4zEHzYiHc9Es55x3vwO6q-HVw/pubhtml?gid=368642593&single=true' target="_blank" rel="noopener noreferrer">Click me!</a></p>
    </div>
    <script>
        // Function to hide loader on page load
        function hideLoader() {
            document.getElementById('overlay').style.display = 'none';
        }

        let generatedOTP = '';

        function checkID() {
            const id = document.getElementById('id').value;
            fetch('validate_id.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id: id })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    swal({
                        title: "Success! We found your data!",
                        text: "Please enter your desired Password and Email Address",
                        icon: "success"
                    });
                    document.getElementById('name').value = data.name;
                    document.getElementById('detailsSection').style.display = 'block';
                } else if (data.existing) {
                    swal({
                        title: "Existing Account",
                        text: "You have already registered your account.\nPlease contact RICTU for a reset.",
                        icon: "error"
                    });
                    document.getElementById('detailsSection').style.display = 'none';
                }
                else {
                    swal({
                        title: "ID not found",
                        text: "The entered ID does not exist in the database.",
                        icon: "error"
                    });
                    document.getElementById('detailsSection').style.display = 'none';
                }
            });
        }

        function validateEmail() {
            const email = document.getElementById('email').value;
            const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;

            if (emailPattern.test(email)) {
                document.getElementById('overlay').style.display = 'flex'; // Show loading overlay and spinner

                // Check if the email already exists in the database
                fetch('check_email.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ email: email })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.exists) {
                        document.getElementById('overlay').style.display = 'none'; // Hide loading overlay and spinner
                        swal({
                            title: "Email Already Exists",
                            text: "The entered email address is already registered. Please use a different email.",
                            icon: "error"
                        });
                    } else {
                        // Generate OTP and send to the provided email
                        generatedOTP = Math.floor(100000 + Math.random() * 900000).toString();
                        
                        fetch('send_otp.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                email: email,
                                otp: generatedOTP
                            })
                        }).then(response => response.json())
                          .then(data => {
                              document.getElementById('overlay').style.display = 'none'; // Hide loading overlay and spinner
                              if (data.success) {
                                  swal({
                                      title: "OTP Sent",
                                      text: "An OTP has been sent to your email.",
                                      icon: "success"
                                  });
                                  document.getElementById('otpSection').style.display = 'block';
                                  document.getElementById('sendOtpButton').style.display = 'none'; // Hide Send OTP button
                              } else {
                                  swal({
                                      title: "Failed to Send OTP",
                                      text: "There was an error sending the OTP. Please try again.",
                                      icon: "error"
                                  });
                              }
                          });
                    }
                });
            } else {
                swal({
                    title: "Invalid Email",
                    text: "Please enter a valid email address.",
                    icon: "error"
                });
            }
        }

        function verifyOTP() {
            const enteredOTP = document.getElementById('otp').value;

            if (enteredOTP === generatedOTP) {
                document.getElementById('otpSection').style.display = 'none';
                updateUserDetails();
            } else {
                swal({
                    title: "Invalid OTP",
                    text: "The entered OTP is incorrect. Please try again.",
                    icon: "error"
                });
            }
        }

        function updateUserDetails() {
            const id = document.getElementById('id').value;
            const password = document.getElementById('password').value;
            const email = document.getElementById('email').value;

            fetch('update_user.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    id: id,
                    password: password,
                    email: email
                })
            }).then(response => response.json())
              .then(data => {
                  if (data.success) {
                      swal({
                          title: "Registration Complete",
                          text: "Registration Complete. Redirecting to login page...",
                          icon: "success"
                      }).then(() => {
                          window.location.href = 'login.php';
                      });
                  } else {
                      swal({
                          title: "Registration Failed",
                          text: "There was an error with the Registration. Please try again.",
                          icon: "error"
                      });
                  }
              });
        }
    </script>
</body>
</html>
