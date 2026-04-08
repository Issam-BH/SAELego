<!DOCTYPE html>
<html lang="<?= LanguageService::getCurrentLanguage() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bricks App</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600&family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-gradient: linear-gradient(90deg, #F06292 0%, #7B1FA2 100%);
            --accent-green: #33FF77;
            --accent-green-hover: #2ce66b;
            --text-color: #333;
            --bg-color: #F4F4F4;
            --card-radius: 16px;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        h1, h2, h3, h4, h5, h6, .navbar-brand {
            font-family: 'Fredoka', sans-serif;
            font-weight: 600;
        }

        .navbar-custom {
            background: var(--primary-gradient);
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            padding: 15px 0;
        }
        .navbar-brand {
            font-size: 1.5rem;
            color: white !important;
            letter-spacing: 1px;
        }
        .nav-link {
            color: rgba(255,255,255,0.9) !important;
            font-weight: 500;
            transition: transform 0.2s;
        }
        .nav-link:hover {
            transform: translateY(-2px);
            color: #fff !important;
        }

        .main-container {
            flex: 1;
            padding-top: 40px;
            padding-bottom: 40px;
        }

        .card, .bg-white.rounded {
            border: none;
            border-radius: var(--card-radius);
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        }
        .card-header {
            background-color: white;
            border-bottom: 1px solid #f0f0f0;
            border-radius: var(--card-radius) var(--card-radius) 0 0 !important;
            padding: 20px;
        }

        .btn {
            border-radius: 8px;
            font-weight: 600;
            font-family: 'Fredoka', sans-serif;
            transition: all 0.2s;
        }
        
        .btn-primary, .btn-success {
            background-color: var(--accent-green);
            border: none;
            color: #222; 
            box-shadow: 0 4px 6px rgba(51, 255, 119, 0.3);
        }
        
        .btn-primary:hover, .btn-success:hover, .btn-primary:active, .btn-success:active {
            background-color: var(--accent-green-hover);
            color: #000;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(51, 255, 119, 0.4);
        }

        .btn-outline-primary {
            color: #7B1FA2;
            border-color: #7B1FA2;
        }
        .btn-outline-primary:hover {
            background-color: #7B1FA2;
            color: white;
            border-color: #7B1FA2;
        }

        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e0e0e0;
            padding: 12px;
        }
        .form-control:focus {
            border-color: #F06292;
            box-shadow: 0 0 0 4px rgba(240, 98, 146, 0.1);
        }

        .language-selector a {
            background: rgba(255,255,255,0.2);
            color: white;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .language-selector a.active {
            background: white;
            color: #7B1FA2;
        }

        footer {
            background: white;
            margin-top: auto;
            border-top: none;
            box-shadow: 0 -4px 20px rgba(0,0,0,0.02);
            padding: 30px 0;
            font-size: 0.9rem;
            color: #888;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container">
        <a class="navbar-brand" href="index.php?page=home">
            <i class="bi bi-grid-3x3-gap-fill me-2"></i>IMG2BRICKS
        </a>
        <button class="navbar-toggler text-white border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <i class="bi bi-list fs-1"></i>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="navbar-nav ms-auto align-items-center">
                
                <div class="nav-item me-3">
                    <div class="language-selector">
                        <?php 
                        $currentLang = LanguageService::getCurrentLanguage();
                        $currentPage = $_GET['page'] ?? 'home';
                        foreach (LanguageService::getSupportedLanguages() as $lang): 
                        ?>
                            <a href="?page=<?= htmlspecialchars($currentPage) ?>&lang=<?= $lang ?>" 
                               class="<?= ($currentLang === $lang) ? 'active' : '' ?>">
                                <?= strtoupper($lang) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <?php if (class_exists('UserSession') && UserSession::isAuthenticated()): ?>
                    <?php
                    require_once __DIR__ . '/../config/database.php';
                    $db_header = Database::getInstance();
                    
                    $session_id = $_SESSION['user']['id'] ?? $_SESSION['user']['user_id'] ?? 0;
                    
                    $stmt_pts = $db_header->prepare("SELECT coins FROM users WHERE user_id = :id");
                    $stmt_pts->execute(['id' => $session_id]);
                    $current_points = $stmt_pts->fetchColumn() ?: 0;
                    ?>
                    <span style="background: #FFD700; color: #5a3d00; padding: 6px 15px; border-radius: 20px; font-weight: bold; margin-right: 15px; font-size: 0.95rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        🪙 <?= $current_points ?> coins
                    </span>
                    <span class="nav-link me-2">👋 <?= htmlspecialchars($_SESSION['user']['username'] ?? 'Member') ?></span>
                    <a class="nav-link" href="<?= LanguageService::getURLWithLanguage('history') ?>"><?= Translator::get('my_orders') ?></a>
                    <a class="nav-link" href="<?= LanguageService::getURLWithLanguage('coupons') ?>">Coupons</a>
                    <a class="nav-link" href="<?= LanguageService::getURLWithLanguage('profile') ?>"><?= Translator::get('my_profile') ?></a>
                    <a class="btn btn-sm btn-light text-primary ms-2 fw-bold" href="<?= LanguageService::getURLWithLanguage('logout') ?>" style="border-radius: 20px; padding: 5px 15px;"><?= Translator::get('logout') ?></a>
                <?php else: ?>
                    <a class="nav-link" href="<?= LanguageService::getURLWithLanguage('login') ?>"><?= Translator::get('login') ?></a>
                    <a class="btn btn-light text-primary ms-2 fw-bold" href="<?= LanguageService::getURLWithLanguage('register') ?>" style="border-radius: 20px;"><?= Translator::get('register') ?></a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<div class="container main-container">
    <?= $content ?>
</div>

<footer>
    <div class="container text-center">
        <p class="mb-0">&copy; <?= date('Y') ?> IMG2BRICKS. Turn photos into fun.</p>
        <div class="d-flex justify-content-center gap-3 mt-2">
            <?php
            // Make url
            $game_url = "http://localhost:3001/";

            // If user LogIn we can see his id
            if (class_exists('UserSession') && UserSession::isAuthenticated()) {
                // Надійно отримуємо ID з сесії
                $session_id = $_SESSION['user']['id'] ?? $_SESSION['user']['user_id'] ?? null;
                
                if ($session_id) {
                    $game_url .= "?fidelityId=" . $session_id;
                }
            }
            ?>
                <a href="index.php?page=games" class="footer-btn purple" style="
                    display: inline-flex;
                    align-items: center;
                    gap: 6px;
                    color: #9C27B0;
                    text-decoration: none;
                    font-size: 0.85rem;
                    font-weight: 600;
                    font-family: 'Fredoka', sans-serif;
                    padding: 6px 14px;
                    border-radius: 20px;
                    border: 1.5px solid rgba(156,39,176,0.3);
                    transition: all 0.2s;
                "> Espace Jeux </a> 
            
            <a href="https://github.com/Issam-BH/SAELego" target="_blank" rel="noopener noreferrer" style="
                display: inline-flex;
                align-items: center;
                gap: 6px;
                color: #333;
                text-decoration: none;
                font-size: 0.85rem;
                font-weight: 600;
                font-family: 'Fredoka', sans-serif;
                padding: 6px 14px;
                border-radius: 20px;
                border: 1.5px solid rgba(51,51,51,0.3);
                transition: all 0.2s;
            ">
                <i class="bi bi-github"></i> GitHub
            </a>
        </div>
    </div>
</footer>
<?php if (class_exists('UserSession') && UserSession::isAuthenticated()): ?>
<script>
    if (window.AndroidInterface) {
        const appUserId = "<?= $_SESSION['user']['id'] ?? $_SESSION['user']['user_id'] ?? '' ?>";
        if (appUserId !== "") {
            window.AndroidInterface.saveUserId(appUserId);
        }
    }
</script>
<?php endif; ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>