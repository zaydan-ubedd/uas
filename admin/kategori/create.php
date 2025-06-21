<?php
require_once '../auth.php';
require_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];

    $stmt = $conn->prepare("INSERT INTO category (name, description) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $description);
    $stmt->execute();

    header("Location: list.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kategori - AlyNews</title>
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
            padding: 40px 20px;
            background-image:
                radial-gradient(circle at 20% 80%, rgba(255, 255, 255, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.05) 0%, transparent 50%);
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 30px;
            border-bottom: 2px solid #ffffff;
        }

        .site-title {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 2.5rem;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: -0.02em;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        h2 {
            font-family: 'Inter', sans-serif;
            font-size: 2.2rem;
            font-weight: 600;
            color: #ffffff;
            text-align: center;
            margin-bottom: 40px;
            letter-spacing: -0.01em;
        }

        .form-container {
            background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
            border: 1px solid #333333;
            border-radius: 16px;
            padding: 40px;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.4);
            position: relative;
            overflow: hidden;
            margin-bottom: 30px;
        }

        .form-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #ffffff 0%, #cccccc 100%);
        }

        .form-title {
            font-family: 'Inter', sans-serif;
            font-size: 1.8rem;
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 30px;
            letter-spacing: -0.01em;
            text-align: center;
        }

        label {
            font-family: 'Inter', sans-serif;
            font-size: 0.95rem;
            color: #cccccc;
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            letter-spacing: 0.01em;
        }

        input[type="text"] {
            width: 100%;
            padding: 16px 15px;
            border: 1px solid #404040;
            border-radius: 8px;
            font-family: 'Inter', sans-serif;
            font-size: 1rem;
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.05);
            margin-bottom: 20px;
            transition: all 0.3s ease;
            backdrop-filter: blur(5px);
        }

        input[type="text"]:focus {
            outline: none;
            border-color: #ffffff;
            border-width: 2px;
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.2);
            background-color: rgba(255, 255, 255, 0.08);
        }

        input[type="text"]::placeholder {
            color: #666666;
        }

        button[type="submit"] {
            width: 100%;
            background: linear-gradient(135deg, #ffffff 0%, #e0e0e0 100%);
            color: #000000;
            font-family: 'Inter', sans-serif;
            font-size: 1rem;
            font-weight: 600;
            letter-spacing: 0.02em;
            padding: 16px 24px;
            border: 2px solid #ffffff;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(255, 255, 255, 0.2);
        }

        button[type="submit"]:hover {
            background: linear-gradient(135deg, #000000 0%, #333333 100%);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 255, 255, 0.3);
        }

        .navigation {
            background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
            border: 1px solid #333333;
            border-radius: 12px;
            padding: 20px 25px;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        .back-link {
            font-family: 'Inter', sans-serif;
            color: #ffffff;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .back-link:hover {
            color: #cccccc;
            text-decoration: underline;
            text-decoration-color: #cccccc;
            text-underline-offset: 3px;
        }

        .back-link::before {
            content: '←';
            font-size: 1.1rem;
            transition: transform 0.3s ease;
        }

        .back-link:hover::before {
            transform: translateX(-3px);
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
            body {
                padding: 20px 15px;
            }

            .site-title {
                font-size: 2rem;
            }

            h2 {
                font-size: 1.8rem;
            }

            .form-container {
                padding: 30px 25px;
            }

            .form-title {
                font-size: 1.5rem;
            }

            input[type="text"] {
                padding: 14px 12px;
                font-size: 0.95rem;
            }

            button[type="submit"] {
                padding: 14px 20px;
                font-size: 0.95rem;
            }

            .header {
                margin-bottom: 30px;
                padding-bottom: 20px;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 0 15px;
            }

            .site-title {
                font-size: 1.8rem;
            }

            h2 {
                font-size: 1.6rem;
            }

            .form-container {
                padding: 25px 20px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="site-title">AlyNews</div>
        </div>

        <h2>Tambah Kategori</h2>

        <div class="form-container">
            <h3 class="form-title">Form Kategori Baru</h3>
            <form method="POST">
                <label for="name">Nama Kategori:</label>
                <input type="text" id="name" name="name" required>

                <label for="description">Deskripsi:</label>
                <input type="text" id="description" name="description" required>

                <button type="submit">Simpan Kategori</button>
            </form>
        </div>

        <div class="navigation">
            <a href="list.php" class="back-link">Kembali ke Daftar</a>
        </div>
    </div>
</body>

</html>