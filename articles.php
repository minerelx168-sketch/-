<?php
declare(strict_types=1);
require __DIR__ . '/includes/layout.php';
$articles = require __DIR__ . '/data/articles.php';

usort($articles, fn($a, $b) => strcmp($b['date'], $a['date']));

layout_head(
    'Articles · imeicheck',
    'Guides and explainers about IMEI numbers, blacklist checks, and buying used phones safely.'
);
?>
    <section class="page-hero">
        <div class="container">
            <h1>Articles &amp; guides</h1>
            <p class="lede">
                Plain-English walkthroughs of what an IMEI is, how to check
                one safely, and how to avoid getting stuck with a stolen phone.
            </p>
        </div>
    </section>

    <section class="articles-list">
        <div class="container">
            <?php foreach ($articles as $a): ?>
                <a class="article-card" href="/article.php?slug=<?= urlencode($a['slug']) ?>">
                    <div class="article-card-meta">
                        <span class="pill pill-tag"><?= htmlspecialchars($a['tag'], ENT_QUOTES, 'UTF-8') ?></span>
                        <time><?= htmlspecialchars($a['date'], ENT_QUOTES, 'UTF-8') ?></time>
                    </div>
                    <h2><?= htmlspecialchars($a['title'], ENT_QUOTES, 'UTF-8') ?></h2>
                    <p><?= htmlspecialchars($a['excerpt'], ENT_QUOTES, 'UTF-8') ?></p>
                    <span class="article-link">Read article &rarr;</span>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
<?php layout_foot(); ?>
