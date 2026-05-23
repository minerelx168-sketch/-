<?php
declare(strict_types=1);
require __DIR__ . '/includes/layout.php';
require __DIR__ . '/includes/auth.php';

// If already logged in, send them home.
if (auth_user()) {
    header('Location: /dashboard.php');
    exit;
}

$next = auth_safe_next($_GET['next'] ?? null);
$startUrl = '/api/auth/google/start.php?next=' . urlencode($next);

layout_head('Sign in · imeihub', 'Sign in to imeihub with your Google account.');
?>
    <section class="auth-shell">
        <div class="container container--narrow">
            <div class="auth-card">
                <div class="auth-card-head">
                    <h1>Sign in</h1>
                    <p>Use your Google account to access top-ups and paid lookups.</p>
                </div>

                <a class="btn-google" href="<?= htmlspecialchars($startUrl, ENT_QUOTES, 'UTF-8') ?>">
                    <svg width="20" height="20" viewBox="0 0 18 18" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path fill="#EA4335" d="M9 3.48c1.7 0 3.22.59 4.42 1.74l3.3-3.3C14.78.86 12.13 0 9 0 5.48 0 2.44 2.02.96 4.96l3.84 2.98C5.52 5.45 7.05 3.48 9 3.48z"/>
                        <path fill="#4285F4" d="M17.64 9.2c0-.63-.06-1.25-.17-1.83H9v3.47h4.84c-.21 1.13-.85 2.08-1.81 2.72l2.94 2.28c1.72-1.58 2.67-3.92 2.67-6.64z"/>
                        <path fill="#FBBC05" d="M4.79 10.66c-.18-.55-.29-1.13-.29-1.66s.1-1.11.29-1.66L.96 4.36C.35 5.57 0 6.95 0 9c0 2.05.35 3.43.96 4.64l3.83-2.98z"/>
                        <path fill="#34A853" d="M9 18c2.43 0 4.47-.8 5.96-2.18l-2.94-2.28c-.83.56-1.93.88-3.02.88-1.95 0-3.48-1.97-4.21-3.96L.96 13.44C2.44 16.39 5.48 18 9 18z"/>
                    </svg>
                    <span>Continue with Google</span>
                </a>

                <p class="auth-fineprint">
                    By signing in you agree to our
                    <a href="/privacy.php">Privacy Policy</a>.
                </p>
            </div>
        </div>
    </section>
<?php layout_foot(); ?>
