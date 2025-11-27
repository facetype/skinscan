<?php

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Account - SkinScan</title>
    <link rel="stylesheet" href="css/RegisterForm.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

<div class="wrapper">
    <h1>Create Account</h1>

    <p id="error-message" style="color: #ff8080; text-align:center; margin-top:10px;"></p>
    <p id="success-message" style="color: #80ff80; text-align:center; margin-top:10px;"></p>

    <form id="registerForm">
        <div class="input-group">
            <input type="text" id="username" placeholder="Choose a username" required>
            <i class="fa fa-user input-icon"></i>
        </div>

        <div class="input-group">
            <input type="password" id="password" placeholder="Choose a password" required>
            <i class="fa fa-lock input-icon"></i>
        </div>

        <button type="submit" id="registerBtn">Register</button>

        <button type="button" class="register-button"
                onclick="window.location.href='/~270445/skinscan/login.html'">
            Back to Login
        </button>
    </form>
</div>


<script>
document.getElementById("registerForm").addEventListener("submit", async function (e) {
    e.preventDefault();

    const username = document.getElementById("username").value.trim();
    const password = document.getElementById("password").value.trim();
    const errorBox = document.getElementById("error-message");
    const successBox = document.getElementById("success-message");
    const button = document.getElementById("registerBtn");

    errorBox.textContent = "";
    successBox.textContent = "";

    button.disabled = true;
    button.textContent = "Creating...";

    try {
        const res = await fetch("/~270445/src/api/registerApi.php", {
            method: "POST",
            headers: {"Content-Type": "application/json"},
            body: JSON.stringify({username, password})
        });

        const data = await res.json();

        if (data.success) {
            successBox.textContent = "Account created! Redirecting...";
            setTimeout(() => {
                window.location.href = "/~270445/skinscan/login.html";
            }, 1500);
        } else {
            errorBox.textContent = data.error || "Registration failed.";
        }
    } catch (err) {
        errorBox.textContent = "Network error. Please try again.";
    }

    button.disabled = false;
    button.textContent = "Register";
});
</script>

</body>
</html>
