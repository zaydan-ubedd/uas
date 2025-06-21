<?php
require_once '../auth.php';
require_once '../../config/database.php';

$id = $_GET['id'];

$sql = "SELECT * FROM article WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    die("Artikel tidak ditemukan");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title   = $_POST['title'];
    $date    = $_POST['date'];
    $content = $_POST['content'];
    $status  = $_POST['status'];
    $slug    = strtolower(str_replace(' ', '-', $title));

    $picture = $data['picture']; // default gambar lama
    if (isset($_FILES['picture']) && $_FILES['picture']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['picture']['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $ext;
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array(strtolower($ext), $allowed)) {
            die("File gambar tidak valid.");
        }
        move_uploaded_file($_FILES['picture']['tmp_name'], '../../assets/images/' . $filename);
        $picture = $filename;
    }

    // Update artikel
    $update = $conn->prepare("UPDATE article SET title=?, slug=?, date=?, content=?, status=?, picture=? WHERE id=?");
    $update->bind_param("ssssssi", $title, $slug, $date, $content, $status, $picture, $id);
    $update->execute();

    // Hapus relasi lama
    $conn->query("DELETE FROM article_author WHERE article_id = $id");
    $conn->query("DELETE FROM article_category WHERE article_id = $id");

    // Tambah relasi baru (penulis)
    if (isset($_POST['author_ids'])) {
        foreach ($_POST['author_ids'] as $author_id) {
            $stmt = $conn->prepare("INSERT INTO article_author (article_id, author_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $id, $author_id);
            $stmt->execute();
        }
    }

    // Tambah relasi baru (kategori)
    if (isset($_POST['category_ids'])) {
        foreach ($_POST['category_ids'] as $cat_id) {
            $stmt = $conn->prepare("INSERT INTO article_category (article_id, category_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $id, $cat_id);
            $stmt->execute();
        }
    }

    header("Location: list.php");
    exit;
}

// Ambil data untuk form
$authors = $conn->query("SELECT * FROM author");
$categories = $conn->query("SELECT * FROM category");

$article_authors = [];
$article_categories = [];

$res1 = $conn->query("SELECT author_id FROM article_author WHERE article_id = $id");
while ($row = $res1->fetch_assoc()) $article_authors[] = $row['author_id'];

$res2 = $conn->query("SELECT category_id FROM article_category WHERE article_id = $id");
while ($row = $res2->fetch_assoc()) $article_categories[] = $row['category_id'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <title>Edit Artikel - AlyNews</title>
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
            padding: 50px 40px;
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

        .form-group {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #333333;
        }

        .form-group:last-child {
            border-bottom: none;
            margin-bottom: 20px;
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

        .required {
            color: #ff6b6b;
        }

        input[type="text"],
        input[type="date"],
        input[type="file"],
        textarea,
        select {
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
        input[type="date"]:focus,
        input[type="file"]:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: #ffffff;
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.1);
            background-color: #333333;
        }

        textarea {
            resize: vertical;
            min-height: 150px;
            font-family: 'Inter', sans-serif;
        }

        select[multiple] {
            height: 120px;
        }

        select option {
            padding: 8px;
            background-color: #2a2a2a;
            color: #ffffff;
            border: none;
        }

        select option:checked {
            background-color: #ffffff;
            color: #000000;
        }

        .current-image {
            background: linear-gradient(135deg, #2a2a2a 0%, #3a3a3a 100%);
            border: 1px solid #404040;
            border-radius: 12px;
            padding: 20px;
            margin-top: 15px;
            backdrop-filter: blur(10px);
        }

        .image-info {
            font-family: 'Inter', sans-serif;
            font-size: 0.85rem;
            font-weight: 600;
            color: #cccccc;
            margin-bottom: 15px;
            letter-spacing: 0.02em;
        }

        .current-image img {
            display: block;
            margin: 10px 0;
            border: 1px solid #404040;
            border-radius: 8px;
            max-width: 150px;
            height: auto;
        }

        .submit-btn {
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

        .submit-btn:hover {
            background: linear-gradient(135deg, #000000 0%, #333333 100%);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 255, 255, 0.3);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .navigation {
            background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
            border: 1px solid #333333;
            border-radius: 12px;
            padding: 20px 25px;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
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
                padding: 40px 30px;
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

        <h2>Edit Artikel</h2>

        <form method="POST" enctype="multipart/form-data" class="form-container">
            <div class="form-group">
                <label>Judul <span class="required">*</span></label>
                <input type="text" name="title" value="<?= htmlspecialchars($data['title']) ?>" placeholder="Masukkan judul artikel" required>
            </div>

            <div class="form-group">
                <label>Upload Gambar Baru (kosongkan jika tidak diubah)</label>
                <input type="file" name="picture">

                <?php if ($data['picture']): ?>
                    <div class="current-image">
                        <div class="image-info">Gambar Saat Ini:</div>
                        <img src="../../assets/images/<?= $data['picture'] ?>" width="150">
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Tanggal <span class="required">*</span></label>
                <input type="text" name="date" value="<?= $data['date'] ?>" placeholder="YYYY-MM-DD" required>
            </div>

            <div class="form-group">
                <label>Isi Artikel</label>
                <textarea name="content" rows="8" cols="60" placeholder="Masukkan isi artikel..."><?= htmlspecialchars($data['content']) ?></textarea>
            </div>

            <div class="form-group">
                <label>Pilih Penulis <span class="required">*</span></label>
                <select name="author_ids[]" multiple required>
                    <?php while ($a = $authors->fetch_assoc()): ?>
                        <option value="<?= $a['id'] ?>" <?= in_array($a['id'], $article_authors) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($a['nickname']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Pilih Kategori <span class="required">*</span></label>
                <select name="category_ids[]" multiple required>
                    <?php while ($c = $categories->fetch_assoc()): ?>
                        <option value="<?= $c['id'] ?>" <?= in_array($c['id'], $article_categories) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status">
                    <option value="published" <?= $data['status'] === 'published' ? 'selected' : '' ?>>Published</option>
                    <option value="draft" <?= $data['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
                </select>
            </div>

            <button type="submit" class="submit-btn">Simpan Perubahan</button>
        </form>

        <div class="navigation">
            <a href="list.php" class="back-link">Kembali</a>
        </div>
    </div>
</body>

</html>