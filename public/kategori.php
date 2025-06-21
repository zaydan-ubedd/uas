<?php
require_once '../config/database.php';
require_once '../includes/header.php';

// Get category ID from URL
$categoryId = $_GET['id'] ?? 0;

// Get category information
$stmt = $conn->prepare("SELECT name FROM category WHERE id = ?");
$stmt->bind_param("i", $categoryId);
$stmt->execute();
$categoryResult = $stmt->get_result();
$category = $categoryResult->fetch_assoc();

if (!$category) {
    die("Kategori tidak ditemukan");
}

// Get articles in this category
$stmt = $conn->prepare("
    SELECT DISTINCT a.* 
    FROM article a 
    JOIN article_category ac ON a.id = ac.article_id 
    WHERE ac.category_id = ? AND a.status = 'published' 
    ORDER BY a.created_at DESC
");
$stmt->bind_param("i", $categoryId);
$stmt->execute();
$result = $stmt->get_result();
$articles = $result->fetch_all(MYSQLI_ASSOC);

function getAuthors($conn, $articleId)
{
    $authors = [];
    $auth = $conn->query("SELECT a.nickname FROM article_author aa JOIN author a ON aa.author_id = a.id WHERE aa.article_id = $articleId");
    while ($a = $auth->fetch_assoc()) $authors[] = $a['nickname'];
    return $authors;
}

function getCategories($conn, $articleId)
{
    $categories = [];
    $cat = $conn->query("SELECT c.name FROM article_category ac JOIN category c ON ac.category_id = c.id WHERE ac.article_id = $articleId");
    while ($c = $cat->fetch_assoc()) $categories[] = $c['name'];
    return $categories;
}
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

    .category-container {
        background-color: #1a1a1a;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
        overflow: hidden;
        border: 1px solid #333333;
    }

    .category-header {
        padding: 60px 60px 40px;
        border-bottom: 1px solid #333333;
        background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
    }

    .category-title {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 3rem;
        font-weight: 700;
        color: #ffffff;
        line-height: 1.1;
        margin-bottom: 24px;
        letter-spacing: -0.02em;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }

    .category-name {
        color: #ffffff;
        font-weight: 700;
    }

    .category-info {
        font-family: 'Inter', sans-serif;
        font-size: 0.9rem;
        color: #cccccc;
        display: flex;
        align-items: center;
        font-weight: 500;
        padding: 10px 18px;
        background-color: #2a2a2a;
        border-radius: 12px;
        border: 1px solid #404040;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
        width: fit-content;
    }

    .category-info:hover {
        background-color: #ffffff;
        color: #000000;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(255, 255, 255, 0.2);
    }

    .category-content {
        padding: 40px 60px;
    }

    .content-section-title {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 2rem;
        font-weight: 600;
        color: #ffffff;
        margin-bottom: 32px;
        letter-spacing: -0.01em;
        line-height: 1.2;
        border-left: 4px solid #ffffff;
        padding-left: 20px;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }

    .article-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 32px;
        margin-bottom: 40px;
    }

    .article-card {
        background: linear-gradient(135deg, #2a2a2a 0%, #1a1a1a 100%);
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid #333333;
        transition: all 0.3s ease;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.3);
        backdrop-filter: blur(10px);
    }

    .article-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.5);
        border-color: #ffffff;
    }

    .article-card img {
        width: 100%;
        height: 220px;
        object-fit: cover;
        display: block;
        transition: transform 0.3s ease;
        border-bottom: 1px solid #333333;
    }

    .article-card:hover img {
        transform: scale(1.05);
    }

    .article-card-content {
        padding: 24px;
    }

    .article-title {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 1.4rem;
        font-weight: 600;
        color: #ffffff;
        line-height: 1.3;
        margin-bottom: 16px;
        text-decoration: none;
        display: block;
        transition: all 0.3s ease;
        letter-spacing: -0.01em;
    }

    .article-title:hover {
        color: #cccccc;
        text-shadow: 0 2px 8px rgba(255, 255, 255, 0.3);
    }

    .article-meta {
        font-family: 'Inter', sans-serif;
        font-size: 0.85rem;
        color: #cccccc;
        margin-bottom: 16px;
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        align-items: center;
    }

    .meta-item {
        display: flex;
        align-items: center;
        font-weight: 500;
        padding: 6px 12px;
        background-color: #333333;
        border-radius: 8px;
        font-size: 0.8rem;
        border: 1px solid #404040;
        transition: all 0.3s ease;
    }

    .meta-item:hover {
        background-color: #ffffff;
        color: #000000;
        transform: translateY(-1px);
    }

    .article-excerpt {
        font-size: 0.95rem;
        color: #e0e0e0;
        line-height: 1.6;
        font-family: 'Inter', sans-serif;
        font-weight: 400;
        text-align: justify;
    }

    .no-articles {
        text-align: center;
        padding: 80px 40px;
        background: linear-gradient(135deg, #2a2a2a 0%, #1a1a1a 100%);
        border-radius: 16px;
        border: 1px solid #333333;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    }

    .no-articles-icon {
        font-size: 3rem;
        color: #666666;
        margin-bottom: 24px;
    }

    .no-articles-title {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 2rem;
        font-weight: 600;
        color: #ffffff;
        margin-bottom: 16px;
        letter-spacing: -0.01em;
    }

    .no-articles-text {
        color: #cccccc;
        font-size: 1.1rem;
        line-height: 1.6;
        font-family: 'Inter', sans-serif;
    }

    .category-navigation {
        padding: 40px 60px;
        border-top: 1px solid #333333;
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

    /* Responsive Design */
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

        .category-title {
            font-size: 2.2rem;
        }

        .category-header,
        .category-content,
        .category-navigation {
            padding-left: 30px;
            padding-right: 30px;
        }

        .article-grid {
            grid-template-columns: 1fr;
            gap: 24px;
        }

        .article-meta {
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
        }

        .meta-item {
            width: 100%;
            justify-content: center;
        }

        .content-section-title {
            font-size: 1.6rem;
        }
    }

    /* Focus and accessibility improvements */
    .category-container:focus-within {
        box-shadow: 0 6px 24px rgba(255, 255, 255, 0.1);
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
        <div class="category-container">
            <header class="category-header">
                <h1 class="category-title">
                    <span class="category-name"><?= htmlspecialchars($category['name']) ?></span>
                </h1>
                <div class="category-info">
                    📚 <?= count($articles) ?> Articles Found
                </div>
            </header>

            <div class="category-content">
                <?php if (empty($articles)): ?>
                    <div class="no-articles">
                        <div class="no-articles-icon">📄</div>
                        <h2 class="no-articles-title">No Articles Yet</h2>
                        <p class="no-articles-text">
                            No articles are available in the "<strong><?= htmlspecialchars($category['name']) ?></strong>" category yet.
                        </p>
                    </div>
                <?php else: ?>
                    <h2 class="content-section-title">Category Articles</h2>
                    <div class="article-grid">
                        <?php foreach ($articles as $article): ?>
                            <article class="article-card">
                                <?php if ($article['picture']): ?>
                                    <img src="../assets/images/<?= htmlspecialchars($article['picture']) ?>" alt="<?= htmlspecialchars($article['title']) ?>">
                                <?php endif; ?>

                                <div class="article-card-content">
                                    <h3><a href="artikel.php?id=<?= $article['id'] ?>" class="article-title">
                                            <?= htmlspecialchars($article['title']) ?>
                                        </a></h3>

                                    <div class="article-meta">
                                        <span class="meta-item">
                                            📅 <?= date('M j, Y', strtotime($article['created_at'])) ?>
                                        </span>
                                        <?php
                                        $articleAuthors = getAuthors($conn, $article['id']);
                                        if (!empty($articleAuthors)):
                                        ?>
                                            <span class="meta-item">
                                                ✍️ <?= implode(', ', $articleAuthors) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="article-excerpt">
                                        <?= substr(strip_tags($article['content']), 0, 150) ?>...
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <nav class="category-navigation">
                <a href="index.php" class="back-link">Back to Home</a>
            </nav>
        </div>
    </div>

    <div class="sidebar-container">
        <?php require_once '../includes/sidebar.php'; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>