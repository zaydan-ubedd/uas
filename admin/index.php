<?php
require_once 'auth.php';
require_once '../config/database.php';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <title>Dashboard Admin - AlyNews</title>
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
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        header {
            background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
            border-top: 3px solid #ffffff;
            border-bottom: 3px solid #ffffff;
            padding: 40px 0;
            margin-bottom: 60px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(10px);
        }

        h1 {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 3rem;
            font-weight: 700;
            text-align: center;
            color: #ffffff;
            letter-spacing: -0.02em;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
            margin-bottom: 15px;
        }

        .subtitle {
            font-family: 'Inter', sans-serif;
            font-size: 1rem;
            text-align: center;
            color: #cccccc;
            font-weight: 400;
            letter-spacing: 0.02em;
            text-transform: uppercase;
        }

        .dashboard-nav {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
            margin: 60px 0;
        }

        .nav-card {
            background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
            border: 1px solid #333333;
            border-radius: 16px;
            padding: 40px 35px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
            position: relative;
            overflow: hidden;
        }

        .nav-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #ffffff 0%, #cccccc 100%);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .nav-card:hover {
            background: linear-gradient(135deg, #2a2a2a 0%, #333333 100%);
            border-color: #ffffff;
            transform: translateY(-8px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.4);
        }

        .nav-card:hover::before {
            transform: scaleX(1);
        }

        .nav-card a {
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .nav-card h3 {
            font-family: 'Inter', sans-serif;
            font-size: 1.4rem;
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #ffffff;
            letter-spacing: -0.01em;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .nav-card p {
            font-family: 'Inter', sans-serif;
            color: #e0e0e0;
            font-size: 0.95rem;
            line-height: 1.6;
            font-weight: 400;
        }

        .nav-card:hover h3 {
            color: #ffffff;
            border-bottom-color: #ffffff;
        }

        .nav-card:hover p {
            color: #f0f0f0;
        }

        .logout-section {
            margin-top: 80px;
            padding-top: 40px;
            border-top: 1px solid #333333;
            text-align: center;
        }

        .logout-btn {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #ffffff 0%, #e0e0e0 100%);
            color: #000000;
            text-decoration: none;
            padding: 16px 40px;
            font-size: 0.9rem;
            font-weight: 600;
            letter-spacing: 0.02em;
            border: 2px solid #ffffff;
            border-radius: 12px;
            transition: all 0.3s ease;
            display: inline-block;
            box-shadow: 0 4px 12px rgba(255, 255, 255, 0.2);
        }

        .logout-btn:hover {
            background: linear-gradient(135deg, #000000 0%, #333333 100%);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 255, 255, 0.3);
        }

        footer {
            margin-top: 100px;
            padding: 40px 0;
            background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
            border-top: 2px solid #ffffff;
            text-align: center;
            backdrop-filter: blur(10px);
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.3);
        }

        .footer-text {
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
            color: #cccccc;
            font-weight: 400;
            letter-spacing: 0.02em;
        }

        /* Focus and accessibility improvements */
        *:focus {
            outline: 2px solid #ffffff;
            outline-offset: 2px;
            border-radius: 4px;
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

        /* Selection styling */
        ::selection {
            background-color: #ffffff;
            color: #000000;
        }

        @media (max-width: 768px) {
            h1 {
                font-size: 2.5rem;
            }

            .dashboard-nav {
                grid-template-columns: 1fr;
                gap: 30px;
                margin: 40px 0;
            }

            .nav-card {
                padding: 30px 25px;
            }

            .nav-card h3 {
                font-size: 1.2rem;
            }

            header {
                padding: 30px 0;
                margin-bottom: 40px;
            }
        }
    </style>
</head>

<body>
    <header>
        <div class="container">
            <h1>Dashboard Admin</h1>
            <div class="subtitle">WartaNesia Management Portal</div>
        </div>
    </header>

    <div class="container">
        <div class="dashboard-nav">
            <div class="nav-card">
                <a href="artikel/list.php">
                    <h3>Kelola Artikel</h3>
                    <p>Manage and organize all articles, create new content, edit existing posts, and maintain your publication's editorial standards with comprehensive content management tools.</p>
                </a>
            </div>

            <div class="nav-card">
                <a href="kategori/list.php">
                    <h3>Kelola Kategori</h3>
                    <p>Organize content categories, create new sections, and maintain the taxonomical structure of your publication for better content organization and user experience.</p>
                </a>
            </div>

            <div class="nav-card">
                <a href="penulis/list.php">
                    <h3>Kelola Penulis</h3>
                    <p>Manage writer profiles, author information, and contributor access to maintain editorial quality, accountability, and streamlined content creation workflow.</p>
                </a>
            </div>
        </div>

        <div class="logout-section">
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </div>

    <footer>
        <div class="container">
            <div class="footer-text">WartaNesia Admin Dashboard System</div>
        </div>
    </footer>
</body>

</html>