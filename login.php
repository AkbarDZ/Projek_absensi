<!-- login.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Login - Attendance App</title>
</head>
<body>
    <h2>Login</h2>

    <?php if (isset($_GET['error'])): ?>
        <p style="color: red;"><?php echo htmlspecialchars($_GET['error']); ?></p>
    <?php endif; ?>

    <form method="POST" action="auth_login.php">
        <label for="identifier">Name or Email:</label><br>
        <input type="text" id="identifier" name="identifier" required><br><br>

        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br><br>

        <button type="submit">Login</button>
    </form>
</body>
</html>
