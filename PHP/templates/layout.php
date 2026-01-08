<!DOCTYPE html>
<html lang="<?= LanguageService::getCurrentLanguage() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lego App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .main-container {
            flex: 1;
        }
        .language-selector {
            display: flex;
            gap: 8px;
        }
        .language-selector a {
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 0.85rem;
            text-decoration: none;
        }
        .language-selector a.active {
            background-color: rgba(255,255,255,0.3);
            font-weight: bold;
        }
        .language-selector a:hover {
            background-color: rgba(255,255,255,0.2);
        }
    </style>
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
    <div class="container">
        <a class="navbar-brand" href="index.php?page=home">🧱 Lego App</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="navbar-nav ms-auto">
                <!-- Language Selector -->
                <div class="nav-link">
                    <div class="language-selector">
                        <?php 
                        $currentLang = LanguageService::getCurrentLanguage();
                        $currentPage = $_GET['page'] ?? 'home';
                        // Conserver tous les paramètres GET existants sauf 'lang'
                        $params = $_GET;
                        foreach (LanguageService::getSupportedLanguages() as $lang): 
                        ?>
                            <a href="?page=<?= htmlspecialchars($currentPage) ?>&lang=<?= $lang ?>" 
                               class="<?= ($currentLang === $lang) ? 'active' : '' ?>"
                               style="color: white;">
                                <?= ($lang === 'en') ? 'EN' : 'FR' ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <?php if (class_exists('UserSession') && UserSession::isAuthenticated()): ?>
                    <span class="nav-link text-white"><?= Translator::get('hello') ?>, <?= htmlspecialchars($_SESSION['user']['username'] ?? 'Member') ?></span>
                    <a class="nav-link" href="<?= LanguageService::getURLWithLanguage('history') ?>"><?= Translator::get('my_orders') ?></a>
                    <a class="nav-link" href="<?= LanguageService::getURLWithLanguage('profile') ?>"><?= Translator::get('my_profile') ?></a>
                    <a class="nav-link" href="<?= LanguageService::getURLWithLanguage('logout') ?>"><?= Translator::get('logout') ?></a>
                <?php else: ?>
                    <a class="nav-link" href="<?= LanguageService::getURLWithLanguage('login') ?>"><?= Translator::get('login') ?></a>
                    <a class="nav-link" href="<?= LanguageService::getURLWithLanguage('register') ?>"><?= Translator::get('register') ?></a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<div class="container bg-white p-4 rounded shadow-sm main-container">
    <?= $content ?>
</div>

<footer style="text-align:center; padding:20px; margin-top:50px; border-top:1px solid #ccc;">
    <p>&copy; <?= date('Y') ?> SaeLego. All rights reserved.</p>
</footer>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>