<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare('SELECT * FROM users WHERE username = ? AND password = ?');
    $stmt->bind_param('ss', $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['username'] = $username;

        if ($username == 'admin') {
            header('Location: admin_dashboard.php');
        } else {
            header('Location: dashboard.php');
        }
        exit;
    } else {
        $error = 'Invalid login credentials';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Car Rental Login</title>
</head>
<body>
    <h2>Login</h2>
    <form method="post" action="index.php">
        <label>Username:</label>
        <input type="text" name="username" required><br>
        <label>Password:</label>
        <input type="password" name="password" required><br>
        <button type="submit">Login</button>
    </form>
    <?php if (isset($error)) echo "<p>$error</p>"; ?>
</body>
</html>
