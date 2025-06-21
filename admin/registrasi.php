<?php
require_once '../config/database.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm'];

    if ($password !== $confirm) {
        $error = "Konfirmasi password tidak sama.";
    } else {
        // Cek apakah username sudah ada
        $stmt = $conn->prepare("SELECT id FROM admin WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Username sudah terdaftar.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);

            $insert = $conn->prepare("INSERT INTO admin (username, password) VALUES (?, ?)");
            $insert->bind_param("ss", $username, $hashed);

            if ($insert->execute()) {
                $success = "Registrasi berhasil. Silakan login.";
            } else {
                $error = "Gagal registrasi.";
            }

            $insert->close();
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <title>Registrasi Admin - AlyNews</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background-color: #0a0a0a;
            color: #ffffff;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-image:
                radial-gradient(circle at 20% 80%, rgba(255, 255, 255, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.05) 0%, transparent 50%);
        }

        .register-container {
            width: 100%;
            max-width: 520px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .register-header {
            text-align: center;
            margin-bottom: 50px;
            padding-bottom: 40px;
            border-bottom: 2px solid #ffffff;
        }

        .site-title {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 2.8rem;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: -0.02em;
            margin-bottom: 15px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .register-subtitle {
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
            color: #cccccc;
            font-weight: 400;
            letter-spacing: 0.02em;
            text-transform: uppercase;
        }

        h2 {
            font-family: 'Inter', sans-serif;
            font-size: 1.8rem;
            font-weight: 600;
            color: #ffffff;
            text-align: center;
            margin-bottom: 40px;
            letter-spacing: -0.01em;
        }

        .success-message {
            background: linear-gradient(135deg, #1a2a1a 0%, #2a3a2a 100%);
            border: 1px solid #4ade80;
            color: #86efac;
            padding: 20px 25px;
            margin-bottom: 30px;
            font-family: 'Inter', sans-serif;
            font-size: 0.95rem;
            font-weight: 500;
            text-align: center;
            border-radius: 12px;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 12px rgba(74, 222, 128, 0.1);
        }

        .success-message a {
            color: #22d3ee;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .success-message a:hover {
            color: #67e8f9;
            text-decoration: underline;
            text-underline-offset: 3px;
        }

        .error-message {
            background: linear-gradient(135deg, #2a1a1a 0%, #3a2a2a 100%);
            border: 1px solid #ff6b6b;
            color: #ff9999;
            padding: 18px 20px;
            margin-bottom: 30px;
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
            font-weight: 500;
            text-align: center;
            border-radius: 12px;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 12px rgba(255, 107, 107, 0.1);
        }

        .register-form {
            background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
            border: 1px solid #333333;
            border-radius: 16px;
            padding: 50px 40px;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.4);
            position: relative;
            overflow: hidden;
        }

        .register-form::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #ffffff 0%, #cccccc 100%);
        }

        .form-group {
            margin-bottom: 30px;
        }

        label {
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
            font-weight: 600;
            color: #ffffff;
            display: block;
            margin-bottom: 12px;
            letter-spacing: 0.02em;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 16px 18px;
            border: 1px solid #404040;
            border-radius: 12px;
            background-color: #2a2a2a;
            font-family: 'Inter', sans-serif;
            font-size: 1rem;
            color: #ffffff;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #ffffff;
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.1);
            background-color: #333333;
        }

        input[type="text"]::placeholder,
        input[type="password"]::placeholder {
            color: #cccccc;
            font-weight: 400;
        }

        .register-btn {
            width: 100%;
            padding: 16px 20px;
            background: linear-gradient(135deg, #ffffff 0%, #e0e0e0 100%);
            color: #000000;
            border: 2px solid #ffffff;
            border-radius: 12px;
            font-family: 'Inter', sans-serif;
            font-size: 1rem;
            font-weight: 600;
            letter-spacing: 0.02em;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 15px;
            box-shadow: 0 4px 12px rgba(255, 255, 255, 0.2);
        }

        .register-btn:hover {
            background: linear-gradient(135deg, #000000 0%, #333333 100%);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 255, 255, 0.3);
        }

        .register-btn:active {
            transform: translateY(0);
        }

        .form-footer {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid #333333;
            text-align: center;
        }

        .login-link {
            font-family: 'Inter', sans-serif;
            color: #ffffff;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .login-link:hover {
            color: #cccccc;
            text-decoration: underline;
            text-decoration-color: #cccccc;
            text-underline-offset: 3px;
        }

        .password-hint {
            font-family: 'Inter', sans-serif;
            font-size: 0.8rem;
            color: #999999;
            margin-top: 8px;
            line-height: 1.4;
            font-weight: 400;
        }

        /* Focus and accessibility improvements */
        *:focus {
            outline: 2px solid #ffffff;
            outline-offset: 2px;
            border-radius: 4px;
        }

        /* Selection styling */
        ::selection {
            background-color: #ffffff;
            color: #000000;
        }

        /* Scrollbar styling */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #1a1a1a;
        }

        ::-webkit-scrollbar-thumb {
            background: #404040;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #666666;
        }

        @media (max-width: 768px) {
            .register-container {
                padding: 0 15px;
            }

            .site-title {
                font-size: 2.2rem;
            }

            h2 {
                font-size: 1.5rem;
            }

            .register-form {
                padding: 40px 30px;
            }

            .register-header {
                margin-bottom: 40px;
                padding-bottom: 30px;
            }
        }
    </style>
</head>

<body>
    <div class="register-container">
        <div class="register-header">
            <div class="site-title">WartaNesia</div>
            <div class="register-subtitle">Admin Portal</div>
        </div>

        <h2>Registrasi Admin</h2>

        <?php if ($success): ?>
            <div class="success-message">
                <?php echo htmlspecialchars($success); ?>
                <br><br>
                <a href="login.php">Login di sini</a>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (!$success): ?>
            <form method="POST" action="" class="register-form">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" placeholder="Masukkan username" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" placeholder="Masukkan password" required>
                    <div class="password-hint">
                        Gunakan password yang kuat dengan minimal 8 karakter termasuk huruf, angka, dan karakter khusus.
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirm">Ulangi Password</label>
                    <input type="password" name="confirm" id="confirm" placeholder="Konfirmasi password" required>
                </div>

                <button type="submit" class="register-btn">Daftar</button>

                <div class="form-footer">
                    <a href="login.php" class="login-link">Already have an account? Login here</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
</body>

</html>