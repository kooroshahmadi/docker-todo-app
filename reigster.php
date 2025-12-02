<?php
require 'db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['username'];
    // SECURE PASSWORDS: Hash them!
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $user, $pass);

    if ($stmt->execute()) {
        header("Location: login.php");
    } else {
        $error = "Username already exists!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background: #FFEFBD; }
        .box { background: white; padding: 40px; border-radius: 20px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 300px; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box;}
        button { width: 100%; padding: 10px; background: #6750A4; color: white; border: none; border-radius: 20px; cursor: pointer; }
        h2 { text-align: center; color: #6750A4; margin-top: 0; }
        .link { text-align: center; margin-top: 10px; font-size: 14px; }
    </style>
</head>
<body>
    <div class="box">
        <h2>Create Account</h2>
        <?php if(isset($error)) echo "<p style='color:red;text-align:center'>$error</p>"; ?>
        <form method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Sign Up</button>
        </form>
        <div class="link"><a href="login.php">Already have an account? Login</a></div>
    </div>
</body>
</html>
