<?php
require_once '../config/database.php';
require_once '../includes/header.php';

$stmt = $conn->prepare("SELECT * FROM article WHERE status = 'published' ORDER BY created_at DESC");
$stmt->execute();
$result = $stmt->get_result();
$articles = $result->fetch_all(MYSQLI_ASSOC);

$headline = array_shift($articles);
$otherArticles = $articles;

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
        background-color: #1a1a1a;
        border-radius: 16px;
        padding: 40px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
        border: 1px solid #333333;
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
        position: sticky;
        top: 40px;
    }

    .headline-article {
        background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
        border-radius: 16px;
        margin-bottom: 60px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
        border: 1px solid #333333;
        padding: 0;
        transition: all 0.3s ease;
        position: relative;
    }

    .headline-article::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 6px;
        background: linear-gradient(90deg, #ffffff, #cccccc);
        border-radius: 16px 16px 0 0;
    }

    .headline-article:hover {
        transform: translateY(-6px);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.7);
    }

    .headline-article img {
        width: 100%;
        height: 450px;
        object-fit: cover;
        display: block;
        transition: transform 0.3s ease;
        position: relative;
        border-bottom: 1px solid #333333;
    }

    .headline-article:hover img {
        transform: scale(1.02);
    }

    .headline-content {
        padding: 40px;
        background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
    }

    .headline-title {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 2.5rem;
        font-weight: 700;
        color: #ffffff;
        line-height: 1.1;
        margin-bottom: 20px;
        text-decoration: none;
        transition: color 0.2s ease;
        letter-spacing: -0.02em;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }

    .headline-title:hover {
        color: #cccccc;
        text-decoration: none;
    }

    .headline-title:focus {
        outline: 3px solid rgba(255, 255, 255, 0.5);
        outline-offset: 2px;
        border-radius: 4px;
    }

    .headline-meta {
        font-family: 'Inter', sans-serif;
        font-size: 0.9rem;
        color: #cccccc;
        margin-bottom: 24px;
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        align-items: center;
    }

    .headline-meta span {
        display: flex;
        align-items: center;
        padding: 10px 18px;
        background-color: #2a2a2a;
        border-radius: 12px;
        font-weight: 500;
        font-size: 0.85rem;
        border: 1px solid #404040;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
    }

    .headline-meta span:hover {
        background-color: #ffffff;
        color: #000000;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(255, 255, 255, 0.2);
    }

    .headline-excerpt {
        font-size: 1.1rem;
        color: #e0e0e0;
        line-height: 1.7;
        margin-bottom: 0;
        text-align: justify;
        font-family: 'Playfair Display', Georgia, serif;
    }

    .article-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 40px;
        margin-top: 60px;
    }

    .article-card {
        background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
        border: 1px solid #333333;
        padding: 0;
        transition: all 0.3s ease;
        position: relative;
    }

    .article-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: #ffffff;
        transform: scaleX(0);
        transition: transform 0.3s ease;
        transform-origin: left;
    }

    .article-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 6px 24px rgba(0, 0, 0, 0.7);
    }

    .article-card:hover::before {
        transform: scaleX(1);
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
        transform: scale(1.03);
    }

    .article-card-content {
        padding: 32px;
        background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
    }

    .article-title {
        font-family: 'Inter', sans-serif;
        font-size: 1.3rem;
        font-weight: 600;
        color: #ffffff;
        line-height: 1.3;
        margin-bottom: 16px;
        text-decoration: none;
        display: block;
        transition: color 0.2s ease;
        letter-spacing: -0.01em;
    }

    .article-title:hover {
        color: #cccccc;
        text-decoration: none;
    }

    .article-title:focus {
        outline: 2px solid #ffffff;
        outline-offset: 2px;
        border-radius: 4px;
    }

    .article-meta {
        font-family: 'Inter', sans-serif;
        font-size: 0.85rem;
        color: #cccccc;
        margin-bottom: 20px;
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
    }

    .article-meta span {
        padding: 8px 16px;
        background-color: #2a2a2a;
        border-radius: 8px;
        font-weight: 500;
        border: 1px solid #404040;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
    }

    .article-meta span:hover {
        background-color: #ffffff;
        color: #000000;
        transform: translateY(-1px);
    }

    .article-excerpt {
        font-size: 0.95rem;
        color: #e0e0e0;
        line-height: 1.6;
        font-family: 'Playfair Display', Georgia, serif;
        text-align: justify;
    }

    .no-articles {
        text-align: center;
        padding: 100px 32px;
        background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
        border: 1px solid #333333;
    }

    .no-articles p {
        font-size: 1.2rem;
        color: #cccccc;
        font-weight: 400;
        font-family: 'Playfair Display', Georgia, serif;
    }

    .no-articles::before {
        content: "📝";
        font-size: 3.5rem;
        display: block;
        margin-bottom: 20px;
        opacity: 0.4;
    }

    /* Content section title styling */
    .content-section-title {
        font-family: 'Inter', sans-serif;
        font-size: 1.8rem;
        font-weight: 600;
        color: #ffffff;
        margin-bottom: 40px;
        padding-bottom: 16px;
        border-bottom: 3px solid #ffffff;
        display: inline-block;
        letter-spacing: -0.01em;
        position: relative;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }

    .content-section-title::after {
        content: '';
        position: absolute;
        bottom: -3px;
        left: 0;
        width: 40%;
        height: 3px;
        background-color: #cccccc;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .main-container {
            flex-direction: column;
            gap: 30px;
            padding: 20px;
        }

        .sidebar-container {
            position: static;
            margin-top: 30px;
        }

        .main-content {
            padding: 30px;
        }

        .headline-title {
            font-size: 2rem;
        }

        .headline-content {
            padding: 30px;
        }

        .article-grid {
            grid-template-columns: 1fr;
            gap: 30px;
            margin-top: 40px;
        }

        .headline-meta {
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
        }

        .headline-meta span,
        .article-meta span {
            width: 100%;
            justify-content: center;
        }
    }

    /* Additional interactive elements */
    .article-card,
    .headline-article {
        cursor: pointer;
    }

    .article-card:focus-within,
    .headline-article:focus-within {
        outline: 3px solid rgba(255, 255, 255, 0.3);
        outline-offset: 2px;
    }

    /* Smooth scroll behavior */
    html {
        scroll-behavior: smooth;
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

    /* Loading animation placeholder */
    @keyframes shimmer {
        0% {
            background-position: -200px 0;
        }

        100% {
            background-position: calc(200px + 100%) 0;
        }
    }

    .loading-placeholder {
        background: linear-gradient(90deg, #2a2a2a 0%, #404040 50%, #2a2a2a 100%);
        background-size: 200px 100%;
        animation: shimmer 1.5s infinite;
    }
</style>

<div class="main-container">
    <div class="main-content">
        <?php if (empty($articles) && !$headline): ?>
            <div class="no-articles">
                <p>Belum ada artikel yang tersedia.</p>
            </div>
        <?php else: ?>

            <?php if ($headline): ?>
                <article class="headline-article">
                    <?php if ($headline['picture']): ?>
                        <img src="../assets/images/<?= htmlspecialchars($headline['picture']) ?>" alt="<?= htmlspecialchars($headline['title']) ?>">
                    <?php endif; ?>

                    <div class="headline-content">
                        <h1><a href="artikel.php?id=<?= $headline['id'] ?>" class="headline-title">
                                <?= htmlspecialchars($headline['title']) ?>
                            </a></h1>

                        <div class="headline-meta">
                            <span>📅 <?= date('F j, Y', strtotime($headline['created_at'])) ?></span>
                            <?php
                            $headlineAuthors = getAuthors($conn, $headline['id']);
                            if (!empty($headlineAuthors)):
                            ?>
                                <span>✍️ <?= implode(', ', $headlineAuthors) ?></span>
                            <?php endif; ?>
                            <?php
                            $headlineCategories = getCategories($conn, $headline['id']);
                            if (!empty($headlineCategories)):
                            ?>
                                <span>🏷️ <?= implode(', ', $headlineCategories) ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="headline-excerpt">
                            <?= substr(strip_tags($headline['content']), 0, 200) ?>...
                        </div>
                    </div>
                </article>
            <?php endif; ?>

            <?php if (!empty($otherArticles)): ?>
                <h2 class="content-section-title">More Articles</h2>
                <div class="article-grid">
                    <?php foreach ($otherArticles as $article): ?>
                        <article class="article-card">
                            <?php if ($article['picture']): ?>
                                <img src="../assets/images/<?= htmlspecialchars($article['picture']) ?>" alt="<?= htmlspecialchars($article['title']) ?>">
                            <?php endif; ?>

                            <div class="article-card-content">
                                <h3><a href="artikel.php?id=<?= $article['id'] ?>" class="article-title">
                                        <?= htmlspecialchars($article['title']) ?>
                                    </a></h3>

                                <div class="article-meta">
                                    <span>📅 <?= date('M j, Y', strtotime($article['created_at'])) ?></span>
                                    <?php
                                    $articleAuthors = getAuthors($conn, $article['id']);
                                    if (!empty($articleAuthors)):
                                    ?>
                                        <span>✍️ <?= implode(', ', $articleAuthors) ?></span>
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

        <?php endif; ?>
    </div>

    <div class="sidebar-container">
        <?php require_once '../includes/sidebar.php'; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>