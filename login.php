    <?php
    session_start();

    if (isset($_SESSION['username'])) {
        header("Location: index.php");
        exit;
    }

    $error = "";
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $valid_user = "Tindahan ni Lola";
        $valid_pass = "LolaLola";

        if ($username === $valid_user && $password === $valid_pass) {
            $_SESSION['username'] = $username;
            header("Location: index.php");
            exit;
        } else {
            $error = "Incorrect username or password!";
        }
    }
    ?>

    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Tindahan ni Lola</title>
    <style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Montserrat:wght@700;900&family=Roboto:wght@400;500&display=swap');

:root {
  --green-dark: #2e8b57;
  --green: #3cb371;
  --green-light: #d8ffe0;
  --bg: linear-gradient(135deg, #f6fff8 0%, #e8f5e8 100%);
  --card-bg: #ffffff;
  --accent: #bdf2cb;
  --shadow: rgba(0, 0, 0, 0.1);
  --shadow-hover: rgba(60, 179, 113, 0.3);
}

body {
  font-family: 'Poppins', sans-serif;
  background: var(--bg);
  color: #333;
  margin: 0;
  padding: 0;
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  overflow: hidden;
}

body::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="%23bdf2cb" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="%23bdf2cb" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="%23bdf2cb" opacity="0.05"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
  pointer-events: none;
  z-index: -1;
}

.login-card {
  background: var(--card-bg);
  border-radius: 24px;
  padding: 40px;
  box-shadow: 0 20px 40px var(--shadow);
  width: 100%;
  max-width: 380px;
  position: relative;
  overflow: hidden;
  animation: slideUp 0.6s ease-out;
  border: 1px solid rgba(60, 179, 113, 0.1);
}

.login-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 6px;
  background: linear-gradient(90deg, var(--green-dark), var(--green));
}

.login-card h4 {
  font-family: 'Montserrat', sans-serif;
  font-weight: 900;
  font-size: 1.8rem;
  color: var(--green-dark);
  text-align: center;
  margin-bottom: 8px;
  letter-spacing: 0.5px;
}

.login-card h6 {
  font-family: 'Roboto', sans-serif;
  font-weight: 500;
  font-size: 0.95rem;
  color: #666;
  text-align: center;
  margin-bottom: 30px;
  opacity: 0.8;
}

.login-card .form-control {
  border: 2px solid #e9ecef;
  border-radius: 12px;
  padding: 14px 16px;
  font-size: 0.95rem;
  font-family: 'Poppins', sans-serif;
  transition: all 0.3s ease;
  background: #fafafa;
  margin-bottom: 20px;
  width: 100%;
  box-sizing: border-box;
}

.login-card .form-control:focus {
  border-color: var(--green);
  box-shadow: 0 0 0 0.2rem rgba(60, 179, 113, 0.15);
  background: #fff;
  transform: translateY(-1px);
  outline: none;
}

.login-card .btn {
  background: linear-gradient(135deg, var(--green-dark), var(--green));
  border: none;
  border-radius: 12px;
  padding: 14px;
  font-size: 1rem;
  font-weight: 600;
  font-family: 'Poppins', sans-serif;
  color: #fff;
  width: 100%;
  transition: all 0.3s ease;
  box-shadow: 0 4px 15px var(--shadow);
  text-transform: uppercase;
  letter-spacing: 0.5px;
  cursor: pointer;
}

.login-card .btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px var(--shadow-hover);
  background: linear-gradient(135deg, var(--green), var(--green-dark));
}

.alert {
  border-radius: 12px;
  border: none;
  background: linear-gradient(135deg, #f8d7da, #f5c6cb);
  color: #721c24;
  font-weight: 500;
  text-align: center;
  padding: 12px;
  margin-bottom: 20px;
  box-shadow: 0 4px 15px rgba(220, 53, 69, 0.15);
  font-family: 'Poppins', sans-serif;
  font-size: 0.9rem;
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@media (max-width: 480px) {
  .login-card {
    margin: 20px;
    padding: 30px 25px;
  }
  
  .login-card h4 {
    font-size: 1.6rem;
  }
}
    </style>
</head>
<body>

<div class="login-card">
    <h4>🛒 Tindahan ni Lola</h4>
    <h6>Inventory Login</h6>

    <?php if ($error): ?>
        <div class="alert"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="username" class="form-control" placeholder="Username" required>
        <input type="password" name="password" class="form-control" placeholder="Password" required>
        <button type="submit" class="btn">Login</button>
    </form>
</div>

</body>
</html>