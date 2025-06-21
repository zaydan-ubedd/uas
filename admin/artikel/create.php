<?php
require_once '../auth.php';
require_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title   = $_POST['title'];
    $date    = $_POST['date'];
    $content = $_POST['content'];
    $status  = $_POST['status'];
    $slug    = strtolower(str_replace(' ', '-', $title));
    $picture = null;
    if (isset($_FILES['picture']) && $_FILES['picture']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['picture']['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['picture']['tmp_name'], '../../assets/images/' . $filename);
        $picture = $filename;
    }

    $stmt = $conn->prepare("INSERT INTO article (title, slug, date, content, status, picture) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $title, $slug, $date, $content, $status, $picture);
    $stmt->execute();

    $article_id = $conn->insert_id;

    if (!empty($_POST['author_ids'])) {
        foreach ($_POST['author_ids'] as $author_id) {
            $stmt = $conn->prepare("INSERT INTO article_author (article_id, author_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $article_id, $author_id);
            $stmt->execute();
        }
    }

    if (!empty($_POST['category_ids'])) {
        foreach ($_POST['category_ids'] as $cat_id) {
            $stmt = $conn->prepare("INSERT INTO article_category (article_id, category_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $article_id, $cat_id);
            $stmt->execute();
        }
    }

    header("Location: list.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Artikel - AlyNews</title>
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
            margin-bottom: 25px;
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

        .form-section {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #333333;
        }

        .form-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        label {
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.02em;
            color: #ffffff;
            display: block;
            margin-bottom: 8px;
        }

        .required {
            color: #ff6b6b;
            margin-left: 4px;
        }

        input[type="text"],
        input[type="date"],
        input[type="file"],
        textarea,
        select {
            font-family: 'Inter', sans-serif;
            width: 100%;
            padding: 14px 16px;
            border: 1px solid #404040;
            border-radius: 8px;
            background-color: #1a1a1a;
            color: #ffffff;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        input[type="text"]:focus,
        input[type="date"]:focus,
        input[type="file"]:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: #ffffff;
            background-color: #2a2a2a;
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.1);
        }

        textarea {
            resize: vertical;
            min-height: 150px;
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
        }

        select[multiple] {
            height: 120px;
            padding: 8px;
        }

        select option {
            padding: 8px 12px;
            font-family: 'Inter', sans-serif;
            background-color: #1a1a1a;
            color: #ffffff;
            border: none;
        }

        select option:checked {
            background-color: #ffffff;
            color: #000000;
        }

        input[type="file"] {
            padding: 12px;
            cursor: pointer;
        }

        input[type="file"]::-webkit-file-upload-button {
            background: linear-gradient(135deg, #ffffff 0%, #e0e0e0 100%);
            color: #000000;
            border: none;
            border-radius: 6px;
            padding: 8px 16px;
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            margin-right: 12px;
            transition: all 0.3s ease;
        }

        input[type="file"]::-webkit-file-upload-button:hover {
            background: linear-gradient(135deg, #000000 0%, #333333 100%);
            color: #ffffff;
        }

        .submit-button {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #ffffff 0%, #e0e0e0 100%);
            color: #000000;
            padding: 16px 32px;
            border: 2px solid #ffffff;
            border-radius: 12px;
            text-transform: uppercase;
            letter-spacing: 0.02em;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(255, 255, 255, 0.2);
            display: block;
            margin: 0 auto;
        }

        .submit-button:hover {
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

        /* Placeholder styling for better contrast */
        input::placeholder,
        textarea::placeholder {
            color: #888888;
        }

        /* Multi-select helper text */
        .helper-text {
            font-size: 0.8rem;
            color: #cccccc;
            margin-top: 4px;
            font-style: italic;
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
                padding: 25px;
            }

            .form-section {
                margin-bottom: 25px;
                padding-bottom: 15px;
            }

            input[type="text"],
            input[type="date"],
            input[type="file"],
            textarea,
            select {
                padding: 12px 14px;
                font-size: 0.95rem;
            }

            .submit-button {
                padding: 14px 28px;
                font-size: 0.85rem;
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

        <h2>Tambah Artikel</h2>

        <?php
        $authors = $conn->query("SELECT * FROM author");
        $categories = $conn->query("SELECT * FROM category");
        ?>

        <div class="form-container">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-section">
                    <label>Judul<span class="required">*</span></label>
                    <input type="text" name="title" maxlength="255" required placeholder="Masukkan judul artikel">
                </div>

                <div class="form-section">
                    <label>Upload Gambar</label>
                    <input type="file" name="picture" accept="image/*">
                    <div class="helper-text">Format: JPG, PNG, GIF (Maksimal 5MB)</div>
                </div>

                <div class="form-section">
                    <label>Tanggal<span class="required">*</span></label>
                    <input type="date" name="date" value="<?= date('Y-m-d') ?>" required>
                </div>

                <div class="form-section">
                    <label>Isi Artikel<span class="required">*</span></label>
                    <textarea name="content" rows="8" required placeholder="Tulis isi artikel di sini..."></textarea>
                </div>

                <div class="form-section">
                    <label>Pilih Penulis<span class="required">*</span></label>
                    <select name="author_ids[]" multiple required>
                        <?php while ($a = $authors->fetch_assoc()): ?>
                            <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['nickname']) ?></option>
                        <?php endwhile; ?>
                    </select>
                    <div class="helper-text">Tahan Ctrl (Windows) atau Cmd (Mac) untuk memilih beberapa penulis</div>
                </div>

                <div class="form-section">
                    <label>Pilih Kategori<span class="required">*</span></label>
                    <select name="category_ids[]" multiple required>
                        <?php while ($c = $categories->fetch_assoc()): ?>
                            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                    <div class="helper-text">Tahan Ctrl (Windows) atau Cmd (Mac) untuk memilih beberapa kategori</div>
                </div>

                <div class="form-section">
                    <label>Status</label>
                    <select name="status">
                        <option value="published">Published</option>
                        <option value="draft">Draft</option>
                    </select>
                </div>

                <button type="submit" class="submit-button">Simpan Artikel</button>
            </form>
        </div>

        <div class="navigation">
            <a href="list.php" class="back-link">Kembali ke Daftar Artikel</a>
        </div>
    </div>
</body>

</html>