<?php
require_once '../auth.php';
require_once '../../config/database.php';

$result = $conn->query("SELECT * FROM category ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Kategori - AlyNews</title>
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
            max-width: 1200px;
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

        .action-buttons {
            margin-bottom: 30px;
            text-align: center;
        }

        .add-button {
            display: inline-block;
            padding: 16px 24px;
            background: linear-gradient(135deg, #ffffff 0%, #e0e0e0 100%);
            color: #000000;
            border: 2px solid #ffffff;
            border-radius: 12px;
            font-family: 'Inter', sans-serif;
            font-size: 1rem;
            font-weight: 600;
            letter-spacing: 0.02em;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(255, 255, 255, 0.2);
        }

        .add-button:hover {
            background: linear-gradient(135deg, #000000 0%, #333333 100%);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 255, 255, 0.3);
        }

        .table-container {
            background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
            border: 1px solid #333333;
            border-radius: 16px;
            padding: 30px;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.4);
            position: relative;
            overflow: hidden;
            margin-bottom: 25px;
        }

        .table-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #ffffff 0%, #cccccc 100%);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: transparent;
        }

        th {
            background: linear-gradient(135deg, #333333 0%, #404040 100%);
            color: #ffffff;
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.02em;
            padding: 16px 15px;
            text-align: left;
            border-bottom: 2px solid #ffffff;
            position: relative;
        }

        th:first-child {
            border-top-left-radius: 8px;
        }

        th:last-child {
            border-top-right-radius: 8px;
        }

        td {
            padding: 16px 15px;
            border-bottom: 1px solid #404040;
            font-family: 'Inter', sans-serif;
            font-size: 0.95rem;
            color: #ffffff;
            background: transparent;
            transition: background-color 0.3s ease;
        }

        td:first-child {
            font-weight: 600;
            color: #cccccc;
            width: 80px;
            text-align: center;
        }

        tr:hover td {
            background-color: rgba(255, 255, 255, 0.05);
        }

        .action-links {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .action-links a {
            color: #ffffff;
            text-decoration: none;
            font-family: 'Inter', sans-serif;
            font-size: 0.85rem;
            font-weight: 500;
            padding: 8px 12px;
            border-radius: 6px;
            transition: all 0.3s ease;
            border: 1px solid transparent;
        }

        .action-links a:hover {
            background-color: rgba(255, 255, 255, 0.1);
            border-color: #ffffff;
        }

        .action-links a.delete {
            color: #ff6b6b;
        }

        .action-links a.delete:hover {
            background-color: rgba(255, 107, 107, 0.1);
            border-color: #ff6b6b;
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

            .table-container {
                padding: 20px;
                overflow-x: auto;
            }

            table {
                min-width: 600px;
            }

            th,
            td {
                padding: 12px 10px;
                font-size: 0.85rem;
            }

            .action-links {
                flex-direction: column;
                gap: 8px;
            }

            .action-links a {
                font-size: 0.8rem;
                padding: 6px 10px;
            }

            .header {
                margin-bottom: 30px;
                padding-bottom: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="site-title">AlyNews</div>
        </div>

        <h2>Data Kategori</h2>

        <div class="action-buttons">
            <a href="create.php" class="add-button">+ Tambah Kategori</a>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Deskripsi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['description']) ?></td>
                            <td class="action-links">
                                <a href="edit.php?id=<?= $row['id'] ?>">Edit</a>
                                <span style="color: #666666;">|</span>
                                <a href="delete.php?id=<?= $row['id'] ?>" class="delete" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="navigation">
            <a href="../index.php" class="back-link">Kembali ke Dashboard</a>
        </div>
    </div>
</body>

</html>