<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - UB LRC-DIMS</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f4;
            font-family: Arial, sans-serif;
        }
        .login-card {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
            border-top: 5px solid #800000; /* UB Maroon */
        }
        .btn-login {
            width: 100%; padding: 12px; background-color: #800000;
            color: white; border: none; border-radius: 4px; cursor: pointer;
            font-size: 1rem; margin-top: 1rem;
        }
        .btn-login:hover { background-color: #600000; }
    </style>
</head>
<body>

    <div class="login-card">
        <h2 style="color: #800000;">UB LRC-DIMS</h2>
        <p>Discussion Integrated Management System</p>
        <form action="pages/dashboard.php" method="POST">
            <div style="text-align: left; margin-bottom: 15px;">
                <label>UB Email</label>
                <input type="email" style="width: 100%; padding: 10px;" placeholder="2201765@ub.edu.ph" required>
            </div>
            <div style="text-align: left; margin-bottom: 15px;">
                <label>Password</label>
                <input type="password" style="width: 100%; padding: 10px;" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn-login">Sign In</button>
        </form>
    </div>

</body>
</html>