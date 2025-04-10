<!DOCTYPE html>
<html lang="en">
<head>

    <title>Parent Registeration</title>
    <link rel="stylesheet" href="parent.css">
    <script>
        
        function handleRegistration() {
            const username = document.getElementById("username").value;
            const email = document.getElementById("email").value;
            const password = document.getElementById("password").value;

            if (username && email && password) {
                alert("Registration Successful!"); 
                window.location.href = "dashboard.php";
                return false; 
            } else {
                alert("Please fill in all fields!"); 
                return false; 
            }
        }
    </script>
</head>
<body>
    <div class="register-container">
        <div class="register-box">
            <div class="register-header">
                <img src="mohit.png" alt="Logo" class="logo">
                <h2>Register as a Parent</h2>
            </div>
            <form id="register-form" onsubmit="return handleRegistration();">
                <div class="form-group">
                    <label for="username">Username :</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="email">Email Address :</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password :</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="register-btn">Register</button>
            </form>

            <p class="login-link">Already Registered? <a href="parentlogin.php">Login Now</a></p>
        </div>
    </div>
</body>
</html>
