<?php
require_once '../config/database.php';
require_once '../includes/header.php';

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM article WHERE id = ? AND status = 'published'");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) die("Artikel tidak ditemukan");

$authors = [];
$auth = $conn->query("SELECT a.nickname FROM article_author aa JOIN author a ON aa.author_id = a.id WHERE aa.article_id = $id");
while ($a = $auth->fetch_assoc()) $authors[] = $a['nickname'];

$categories = [];
$cat = $conn->query("SELECT c.name FROM article_category ac JOIN category c ON ac.category_id = c.id WHERE ac.article_id = $id");
while ($c = $cat->fetch_assoc()) $categories[] = $c['name'];
?>

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
        font-size: 16px;
        min-height: 100vh;
    }

    .article-container {
        max-width: 900px;
        margin: 0 auto;
        background-color: #1a1a1a;
        padding: 0;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
        overflow: hidden;
        border: 1px solid #333333;
    }

    .article-header {
        margin-bottom: 40px;
        padding: 60px 60px 0;
        border-bottom: 1px solid #333333;
        padding-bottom: 40px;
        background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
    }

    .article-title {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 3rem;
        font-weight: 700;
        color: #ffffff;
        line-height: 1.1;
        margin-bottom: 32px;
        word-wrap: break-word;
        letter-spacing: -0.02em;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }

    .article-meta {
        font-family: 'Inter', sans-serif;
        font-size: 0.9rem;
        color: #cccccc;
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        align-items: center;
    }

    .meta-item {
        display: flex;
        align-items: center;
        font-weight: 500;
        padding: 10px 18px;
        background-color: #2a2a2a;
        border-radius: 12px;
        font-size: 0.85rem;
        border: 1px solid #404040;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
    }

    .meta-item:hover {
        background-color: #ffffff;
        color: #000000;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(255, 255, 255, 0.2);
    }

    .meta-item strong {
        color: #ffffff;
        margin-right: 8px;
        font-weight: 600;
    }

    .meta-item:hover strong {
        color: #000000;
    }

    .article-image {
        width: 100%;
        height: auto;
        max-height: 600px;
        object-fit: cover;
        margin-bottom: 32px;
        border-radius: 16px;
        border: 1px solid #333333;
        transition: transform 0.3s ease;
        position: relative;
    }

    .article-image:hover {
        transform: scale(1.02);
    }

    .article-image::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(to bottom, transparent 0%, rgba(0, 0, 0, 0.3) 100%);
        border-radius: 16px;
        pointer-events: none;
    }

    .article-content {
        font-size: 1.1rem;
        line-height: 1.8;
        color: #e0e0e0;
        margin-bottom: 50px;
        padding: 0 60px;
        font-family: 'Playfair Display', Georgia, serif;
        font-weight: 400;
    }

    .article-content p {
        margin-bottom: 28px;
        text-align: justify;
        color: #e0e0e0;
    }

    .article-content h2 {
        font-family: 'Inter', sans-serif;
        font-size: 1.8rem;
        font-weight: 600;
        color: #ffffff;
        margin: 50px 0 24px 0;
        letter-spacing: -0.01em;
        line-height: 1.2;
        border-left: 4px solid #ffffff;
        padding-left: 20px;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }

    .article-content h3 {
        font-family: 'Inter', sans-serif;
        font-size: 1.4rem;
        font-weight: 600;
        color: #ffffff;
        margin: 40px 0 20px 0;
        letter-spacing: -0.01em;
        line-height: 1.3;
    }

    .article-content ul,
    .article-content ol {
        margin: 24px 0;
        padding-left: 32px;
    }

    .article-content li {
        margin-bottom: 12px;
        color: #e0e0e0;
    }

    .article-content blockquote {
        border-left: 4px solid #ffffff;
        padding-left: 32px;
        margin: 40px 0;
        font-style: italic;
        color: #e0e0e0;
        background-color: #2a2a2a;
        padding: 32px 0 32px 32px;
        border-radius: 0 16px 16px 0;
        font-size: 1.2rem;
        line-height: 1.7;
        position: relative;
        box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.3);
    }

    .article-content blockquote::before {
        content: '"';
        font-size: 4rem;
        color: #ffffff;
        position: absolute;
        left: -10px;
        top: -15px;
        font-family: 'Playfair Display', serif;
        opacity: 0.3;
    }

    .article-navigation {
        padding: 40px 60px;
        border-top: 1px solid #333333;
        margin-top: 50px;
        background: linear-gradient(135deg, #2a2a2a 0%, #1a1a1a 100%);
        border-radius: 0 0 16px 16px;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        color: #ffffff;
        text-decoration: none;
        font-weight: 500;
        font-size: 0.95rem;
        font-family: 'Inter', sans-serif;
        padding: 16px 28px;
        background: linear-gradient(135deg, #000000 0%, #333333 100%);
        border-radius: 12px;
        transition: all 0.3s ease;
        border: 2px solid #ffffff;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    }

    .back-link:hover {
        background: linear-gradient(135deg, #ffffff 0%, #f0f0f0 100%);
        color: #000000;
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(255, 255, 255, 0.2);
    }

    .back-link:focus {
        outline: 3px solid rgba(255, 255, 255, 0.5);
        outline-offset: 2px;
    }

    .back-link::before {
        content: "←";
        margin-right: 12px;
        font-size: 1.2rem;
        transition: transform 0.3s ease;
    }

    .back-link:hover::before {
        transform: translateX(-4px);
    }

    .main-container {
        display: flex;
        max-width: 1400px;
        margin: 0 auto;
        gap: 50px;
        padding: 40px 20px;
        background-color: #0a0a0a;
        min-height: 100vh;
    }

    .main-content {
        flex: 2;
    }

    .sidebar-container {
        flex: 1;
        min-width: 320px;
        background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
        border-radius: 16px;
        padding: 32px;
        border: 1px solid #333333;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        height: fit-content;
        backdrop-filter: blur(10px);
    }

    @media (max-width: 768px) {
        .main-container {
            flex-direction: column;
            gap: 30px;
            padding: 20px;
        }

        .sidebar-container {
            margin-top: 30px;
            border-top: 2px solid #ffffff;
            border-radius: 16px 16px 0 0;
        }

        .article-title {
            font-size: 2.2rem;
        }

        .article-header,
        .article-content,
        .article-navigation {
            padding-left: 30px;
            padding-right: 30px;
        }

        .article-meta {
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
        }

        .meta-item {
            width: 100%;
            justify-content: center;
        }
    }

    /* Focus and accessibility improvements */
    .article-container:focus-within {
        box-shadow: 0 6px 24px rgba(255, 255, 255, 0.1);
    }

    .article-content a {
        color: #ffffff;
        text-decoration: underline;
        text-decoration-color: rgba(255, 255, 255, 0.5);
        text-underline-offset: 3px;
        transition: all 0.3s ease;
        font-weight: 500;
    }

    .article-content a:hover {
        color: #cccccc;
        text-decoration-color: #cccccc;
    }

    .article-content a:focus {
        outline: 2px solid #ffffff;
        outline-offset: 2px;
        border-radius: 2px;
    }

    /* Additional dark theme enhancements */
    ::selection {
        background-color: #ffffff;
        color: #000000;
    }

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
</style>

<div class="main-container">
    <div class="main-content">
        <article class="article-container">
            <header class="article-header">
                <h1 class="article-title"><?= htmlspecialchars($data['title']) ?></h1>

                <div class="article-meta">
                    <span class="meta-item">
                        <strong>📅</strong> <?= date('F j, Y', strtotime($data['created_at'])) ?>
                    </span>
                    <?php if (!empty($authors)): ?>
                        <span class="meta-item">
                            <strong>✍️</strong> <?= implode(', ', $authors) ?>
                        </span>
                    <?php endif; ?>
                    <?php if (!empty($categories)): ?>
                        <span class="meta-item">
                            <strong>🏷️</strong> <?= implode(', ', $categories) ?>
                        </span>
                    <?php endif; ?>
                </div>
            </header>

            <?php if ($data['picture']): ?>
                <div style="padding: 0 60px;">
                    <img src="../assets/images/<?= htmlspecialchars($data['picture']) ?>"
                        alt="<?= htmlspecialchars($data['title']) ?>"
                        class="article-image">
                </div>
            <?php endif; ?>

            <div class="article-content">
                <?= $data['content'] ?>
            </div>

            <nav class="article-navigation">
                <a href="index.php" class="back-link">Back to Home</a>
            </nav>
        </article>
    </div>

    <div class="sidebar-container">
        <?php require_once '../includes/sidebar.php'; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>