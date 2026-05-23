<?php
declare(strict_types=1);
require __DIR__ . '/includes/layout.php';
require __DIR__ . '/includes/markdown.php';

$articles = require __DIR__ . '/data/articles.php';
$slug = isset($_GET['slug']) ? preg_replace('/[^a-z0-9-]+/i', '', (string) $_GET['slug']) : '';

$article = null;
foreach ($articles as $a) {
    if ($a['slug'] === $slug) { $article = $a; break; }
}

if (!$article) {
    http_response_code(404);
    layout_head('Article not found · imeicheck');
    ?>
    <section class="page-hero">
        <div class="container">
            <h1>Article not found</h1>
            <p class="lede">
                <a href="/articles.php">Browse all articles &rarr;</a>
            </p>
        </div>
    </section>
    <?php
    layout_foot();
    return;
}

layout_head(
    htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8') . ' · imeicheck',
    $article['excerpt']
);
?>
    <article class="article-page">
        <header class="page-hero">
            <div class="container">
                <p class="breadcrumbs">
                    <a href="/articles.php">Articles</a> &rsaquo;
                    <?= htmlspecialchars($article['tag'], ENT_QUOTES, 'UTF-8') ?>
                </p>
                <h1><?= htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8') ?></h1>
                <p class="lede"><?= htmlspecialchars($article['excerpt'], ENT_QUOTES, 'UTF-8') ?></p>
                <p class="article-date">
                    Published <?= htmlspecialchars($article['date'], ENT_QUOTES, 'UTF-8') ?>
                </p>
            </div>
        </header>

        <section class="article-body">
            <div class="container container--prose">
                <?= md_render((string) $article['body']) ?>

                <hr>
                <p class="article-cta">
                    Ready to try it? <a href="/">Run a free IMEI check &rarr;</a>
                </p>
            </div>
        </section>
    </article>
<?php layout_foot(); ?>
