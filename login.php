<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Attendance App</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #c850c0, #4158d0);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-box {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0px 0px 20px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
            font-weight: 600;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
            background: #f5f5f5;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #d63384;
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
        }

        button:hover {
            background-color: #c21868;
        }

        .remember {
            display: flex;
            align-items: center;
            margin: 10px 0;
            font-size: 14px;
        }

        .social-login {
            margin-top: 20px;
        }

        .social-login button {
            margin: 5px;
            padding: 10px 20px;
            background: white;
            border: 1px solid #ccc;
            color: #444;
            border-radius: 6px;
            cursor: pointer;
        }

        .signup-link {
            margin-top: 20px;
            font-size: 14px;
        }

        .error-message {
            color: red;
            font-size: 14px;
            margin-bottom: 10px;
        }

    </style>
</head>
<body>

    <div class="login-box">
        <h2>LOGIN</h2>

        <?php if (isset($_GET['error'])): ?>
            <div class="error-message"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>

        <form method="POST" action="auth_login.php">
            <input type="text" name="identifier" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <div class="remember">
                <input type="checkbox" id="remember"> <label for="remember" style="margin-left: 8px;">Remember me</label>
            </div>

            <button type="submit">LOGIN</button>
        </form>

        <div class="social-login">
            <p>Or login with</p>
            <button>Facebook</button>
            <button>Google</button>
        </div>

        <div class="signup-link">
            Not a member? <a href="#">Sign up now</a>
        </div>
    </div>

</body>
</html>