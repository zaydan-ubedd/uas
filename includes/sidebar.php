<?php
$kategoriQ = $conn->query("SELECT id, name FROM category ORDER BY name ASC");

$currentDir = dirname($_SERVER['SCRIPT_NAME']);
$basePath = '';

if (strpos($currentDir, '/public') !== false) {
    $basePath = './';
} else {
    $basePath = '../public/';
}

// Deteksi apakah sedang di halaman artikel
$isArticlePage = (basename($_SERVER['SCRIPT_NAME']) == 'artikel.php');
$currentArticleId = null;
$currentArticleCategories = [];

if ($isArticlePage && isset($_GET['id'])) {
    $currentArticleId = (int)$_GET['id'];

    $catQuery = $conn->query("SELECT c.id, c.name FROM article_category ac JOIN category c ON ac.category_id = c.id WHERE ac.article_id = $currentArticleId");
    while ($cat = $catQuery->fetch_assoc()) {
        $currentArticleCategories[] = $cat['id'];
    }
}
?>

<div class="sidebar">
    <h3>🔍 Cari Artikel</h3>
    <form method="GET" action="<?= $basePath ?>search.php">
        <input type="text" name="q" placeholder="Cari judul artikel..." value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>">
        <button type="submit">Cari Sekarang</button>
    </form>

    <?php if ($isArticlePage && !empty($currentArticleCategories)): ?>
        <h3>📖 Artikel Terkait</h3>
        <div class="related-articles">
            <?php
            $categoryIds = implode(',', $currentArticleCategories);
            $relatedQuery = $conn->query("
                SELECT DISTINCT a.id, a.title, a.picture, a.created_at 
                FROM article a 
                JOIN article_category ac ON a.id = ac.article_id 
                WHERE ac.category_id IN ($categoryIds) 
                AND a.id != $currentArticleId 
                AND a.status = 'published' 
                ORDER BY a.created_at DESC 
                LIMIT 5
            ");

            if ($relatedQuery && $relatedQuery->num_rows > 0):
            ?>
                <?php while ($related = $relatedQuery->fetch_assoc()): ?>
                    <div class="related-article-item">
                        <?php if ($related['picture']): ?>
                            <div class="related-article-image">
                                <img src="../assets/images/<?= htmlspecialchars($related['picture']) ?>"
                                    alt="<?= htmlspecialchars($related['title']) ?>">
                            </div>
                        <?php endif; ?>
                        <div class="related-article-content">
                            <h4><a href="artikel.php?id=<?= $related['id'] ?>">
                                    <?= htmlspecialchars($related['title']) ?>
                                </a></h4>
                            <small style="color: #cccccc; font-size: 0.8rem;">
                                <?= date('j M Y', strtotime($related['created_at'])) ?>
                            </small>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="text-align: center; padding: 20px; color: #cccccc; font-style: italic;">
                    <p>Tidak ada artikel terkait yang ditemukan.</p>
                </div>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <h3>🏷️ Kategori</h3>
        <ul>
            <?php if ($kategoriQ && $kategoriQ->num_rows > 0): ?>
                <?php while ($k = $kategoriQ->fetch_assoc()): ?>
                    <li><a href="<?= $basePath ?>kategori.php?id=<?= $k['id'] ?>">
                            <?= htmlspecialchars($k['name']) ?>
                        </a></li>
                <?php endwhile; ?>
            <?php else: ?>
                <li style="color: #cccccc; font-style: italic; padding: 14px 0;">
                    Tidak ada kategori tersedia
                </li>
            <?php endif; ?>
        </ul>

        <h3>ℹ️ Tentang Kami</h3>
        <p>WartaNesia adalah platform berita yang dirancang untuk generasi modern yang aktif dan kritis. Kami menyajikan konten agama & spiritualitas, gaya hidup & travel dan politik. Melalui tampilan yang sederhana namun elegan, CitraNews bertujuan menjadi media yang tak hanya menyampaikan berita, tapi juga mendorong pembaca untuk berpikir dan bertindak.</p>
    <?php endif; ?>
</div>