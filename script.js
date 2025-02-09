let generatedOTP = '';

function checkID() {
    const id = document.getElementById('id').value;
    const 
    // Insert your connection script here
    // Example:
    // const connection = new YourDatabaseConnection();
    // const query = `SELECT name FROM users WHERE id = '${id}'`;

    // Simulated database response
    const databaseResponse = {
        success: true,
        name: "John Doe" // Simulate finding a user
    };

    if (databaseResponse.success) {
        document.getElementById('name').value = databaseResponse.name;
        document.getElementById('detailsSection').style.display = 'block';
    } else {
        alert("ID not found");
        document.getElementById('detailsSection').style.display = 'none';
    }
}

function validateEmail() {
    const email = document.getElementById('email').value;
    const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;

    if (emailPattern.test(email)) {
        sendOTP(email);
    } else {
        alert("Please enter a valid email address.");
    }
}

function sendOTP(email) {
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
          if (data.success) {
              alert("OTP sent to your email");
              document.getElementById('otpSection').style.display = 'block';
          } else {
              alert("Failed to send OTP");
          }
      });
}

function verifyOTP() {
    const enteredOTP = document.getElementById('otp').value;

    if (enteredOTP === generatedOTP) {
        alert("OTP verified");
        document.getElementById('otpSection').style.display = 'none';
        document.getElementById('registerBtn').disabled = false;
    } else {
        alert("Invalid OTP");
    }
}