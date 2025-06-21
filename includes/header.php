<?php
require_once '../config/database.php';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WartaNesia</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
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

        .main-header {
            background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
            border-bottom: 2px solid #ffffff;
            padding: 0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(10px);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar-brand {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 2.8rem;
            font-weight: 700;
            color: #ffffff !important;
            text-decoration: none;
            letter-spacing: -0.02em;
            padding: 24px 0;
            transition: all 0.3s ease;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .navbar-brand:hover {
            color: #cccccc !important;
            text-decoration: none;
            transform: scale(1.02);
        }

        .navbar {
            padding: 0;
            border-bottom: 1px solid #333333;
        }

        .navbar-nav .nav-link {
            color: #ffffff !important;
            font-family: 'Inter', sans-serif;
            font-weight: 500;
            font-size: 0.9rem;
            padding: 16px 24px !important;
            margin: 0 4px;
            letter-spacing: 0.02em;
            border-radius: 12px;
            transition: all 0.3s ease;
            position: relative;
            backdrop-filter: blur(10px);
        }

        .navbar-nav .nav-link:hover {
            color: #000000 !important;
            background: linear-gradient(135deg, #ffffff 0%, #f0f0f0 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 255, 255, 0.2);
        }

        .navbar-nav .nav-link.active {
            color: #000000 !important;
            background: linear-gradient(135deg, #ffffff 0%, #e0e0e0 100%);
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(255, 255, 255, 0.3);
        }

        .navbar-nav .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: 8px;
            left: 50%;
            transform: translateX(-50%);
            width: 6px;
            height: 6px;
            background-color: #000000;
            border-radius: 50%;
        }

        .sidebar {
            background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
            border: 1px solid #333333;
            border-radius: 16px;
            padding: 32px;
            margin-bottom: 32px;
            height: fit-content;
            position: sticky;
            top: 32px;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        .sidebar h3 {
            color: #ffffff;
            font-family: 'Inter', sans-serif;
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid #ffffff;
            letter-spacing: -0.01em;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .sidebar form {
            margin-bottom: 40px;
        }

        .sidebar input[type="text"] {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid #404040;
            border-radius: 12px;
            margin-bottom: 16px;
            font-size: 0.9rem;
            font-family: 'Inter', sans-serif;
            background-color: #2a2a2a;
            transition: all 0.3s ease;
            color: #ffffff;
        }

        .sidebar input[type="text"]:focus {
            outline: none;
            border-color: #ffffff;
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.1);
            background-color: #333333;
        }

        .sidebar input[type="text"]::placeholder {
            color: #cccccc;
        }

        .sidebar button {
            width: 100%;
            padding: 14px 20px;
            background: linear-gradient(135deg, #ffffff 0%, #e0e0e0 100%);
            color: #000000;
            border: 2px solid #ffffff;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
            letter-spacing: 0.02em;
            box-shadow: 0 4px 12px rgba(255, 255, 255, 0.2);
        }

        .sidebar button:hover {
            background: linear-gradient(135deg, #000000 0%, #333333 100%);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 255, 255, 0.3);
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
            margin-bottom: 40px;
        }

        .sidebar li {
            margin-bottom: 0;
            border-bottom: 1px solid #333333;
        }

        .sidebar li:last-child {
            border-bottom: none;
        }

        .sidebar li a {
            display: block;
            padding: 14px 0;
            color: #ffffff;
            text-decoration: none;
            font-weight: 400;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .sidebar li a:hover {
            color: #cccccc;
            text-decoration: underline;
            text-decoration-color: #cccccc;
            text-underline-offset: 3px;
            padding-left: 8px;
        }

        .sidebar p {
            color: #e0e0e0;
            line-height: 1.6;
            margin-bottom: 0;
            font-size: 0.9rem;
            padding: 20px;
            background-color: #2a2a2a;
            border: 1px solid #404040;
            border-radius: 12px;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .main-container {
            display: flex;
            gap: 50px;
            max-width: 1400px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .main-content {
            flex: 2;
        }

        .sidebar-container {
            flex: 1;
            max-width: 350px;
        }

        .main-footer {
            background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
            border-top: 2px solid #ffffff;
            margin-top: 60px;
            padding: 40px 0;
            backdrop-filter: blur(10px);
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.3);
        }

        .footer-content {
            text-align: center;
            color: #cccccc;
            font-size: 0.9rem;
            font-weight: 400;
            font-family: 'Inter', sans-serif;
            letter-spacing: 0.02em;
        }

        .footer-content p {
            margin: 0;
            padding: 20px 0;
            border: none;
            border-radius: 0;
            background-color: transparent;
        }

        .content-area {
            min-height: 60vh;
            padding: 60px 0;
        }

        .demo-card {
            background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
            border: 1px solid #333333;
            border-radius: 16px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        .demo-card h2 {
            color: #ffffff;
            margin-bottom: 20px;
            font-weight: 600;
            font-family: 'Playfair Display', Georgia, serif;
        }

        .demo-card p {
            color: #e0e0e0;
            line-height: 1.6;
        }

        .related-articles {
            margin-top: 20px;
        }

        .related-article-item {
            display: flex;
            margin-bottom: 24px;
            padding: 16px;
            border-bottom: 1px solid #333333;
            transition: all 0.3s ease;
            border-radius: 12px;
        }

        .related-article-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        .related-article-item:hover {
            background-color: #2a2a2a;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        .related-article-image {
            flex: 0 0 90px;
            margin-right: 16px;
        }

        .related-article-image img {
            width: 90px;
            height: 70px;
            object-fit: cover;
            border-radius: 8px;
            transition: transform 0.3s ease;
            border: 1px solid #333333;
        }

        .related-article-image img:hover {
            transform: scale(1.05);
        }

        .related-article-content {
            flex: 1;
        }

        .related-article-content h4 {
            margin: 0 0 8px 0;
            font-size: 0.9rem;
            line-height: 1.4;
            font-weight: 500;
        }

        .related-article-content h4 a {
            color: #ffffff;
            text-decoration: none;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            transition: color 0.3s ease;
        }

        .related-article-content h4 a:hover {
            color: #cccccc;
            text-decoration: underline;
            text-decoration-color: #cccccc;
            text-underline-offset: 3px;
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

        /* Responsive Design */
        @media (max-width: 768px) {
            .main-container {
                flex-direction: column;
                gap: 30px;
                padding: 20px;
            }

            .sidebar-container {
                max-width: 100%;
            }

            .navbar-nav .nav-link {
                padding: 12px 16px !important;
                font-size: 0.8rem;
                margin: 2px;
            }

            .navbar-brand {
                font-size: 2.2rem;
                padding: 16px 0;
            }

            .sidebar {
                padding: 24px;
                margin-top: 30px;
            }
        }
    </style>
</head>

<body>
    <header class="main-header">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-dark">
                <div class="container-fluid px-0">
                    <a class="navbar-brand" href="/">WartaNesia</a>

                    <div class="navbar-nav ms-auto">
                        <a class="nav-link active" href="/">Beranda</a>
                        <a class="nav-link" href="/tentang">Tentang</a>
                        <a class="nav-link" href="/kontak">Kontak</a>
                    </div>
                </div>
            </nav>
        </div>
    </header>