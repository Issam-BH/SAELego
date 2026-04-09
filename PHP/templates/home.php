<?php
ob_start();
?>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600&family=Poppins:wght@400;600&display=swap');

    :root {
        --primary-gradient: linear-gradient(90deg, #F06292 0%, #7B1FA2 100%);
        --accent-green: #33FF77;
        --text-color: #333;
        --bg-color: #F4F4F4;
        --panel-pink: #E8A8B8;
        --panel-grey: #E0E0E0;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background-color: var(--bg-color);
        margin: 0;
        padding: 0;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    header {
        background: var(--primary-gradient);
        padding: 15px 40px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    .brand-logo {
        color: white;
        font-family: 'Fredoka', sans-serif;
        font-size: 1.8rem;
        font-weight: 600;
        text-decoration: none;
        letter-spacing: 1px;
    }

    .auth-buttons {
        display: flex;
        gap: 15px;
        align-items: center;
    }

    .btn-header {
        text-decoration: none;
        padding: 8px 20px;
        border-radius: 30px;
        font-weight: 600;
        font-size: 0.9rem;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .btn-login {
        border: 2px solid white;
        color: white;
        background: transparent;
    }

    .btn-register {
        background-color: #33FF77; 
        color: #222;
        border: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    
    .btn-logout {
        background-color: #ff4757; /* Rouge */
        color: white;
        border: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    .btn-header:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    
    .user-greeting {
        color: white;
        font-weight: 600;
        margin-right: 10px;
        font-family: 'Fredoka', sans-serif;
    }
    
    .main-wrapper {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        padding: 40px;
        box-sizing: border-box;
    }

    .content-container {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 80px; 
        max-width: 1200px;
        width: 100%;
        flex-wrap: wrap;
    }

    .hero-panel {
        background-color: var(--panel-pink);
        border: 2px solid #D88EA0;
        border-radius: 20px;
        padding: 40px;
        width: 400px;
        text-align: center;
        transform: rotate(-3deg);
        box-shadow: 10px 10px 0px rgba(0,0,0,0.05);
        color: white;
    }

    .hero-panel h1 {
        font-family: 'Fredoka', sans-serif;
        font-size: 2rem;
        line-height: 1.3;
        margin-bottom: 30px;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
    }

    .transformation-viz {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 15px;
    }

    .viz-img {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        background-color: #ddd;
    }

    .arrow-red {
        font-size: 3rem;
        color: #FF3333;
        font-weight: bold;
        text-shadow: 2px 2px 0 rgba(0,0,0,0.1);
    }

    .upload-panel {
        background-color: var(--panel-grey);
        padding: 50px;
        width: 450px;
        text-align: center;
        border-radius: 5px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }

    .upload-panel h2 {
        color: #E91E63;
        font-family: 'Fredoka', sans-serif;
        font-size: 1.8rem;
        margin-bottom: 40px;
        line-height: 1.4;
    }

    .file-input-group {
        display: flex;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        margin-bottom: 30px;
    }

    .file-label-btn {
        background-color: var(--accent-green);
        color: black;
        font-weight: bold;
        padding: 15px 25px;
        cursor: pointer;
        font-family: 'Fredoka', sans-serif;
        border: 1px solid #333;
        display: flex;
        align-items: center;
    }

    .file-name-display {
        background: white;
        flex-grow: 1;
        padding: 15px;
        border: 1px solid #333;
        border-left: none;
        text-align: left;
        font-weight: 600;
        color: black;
        display: flex;
        align-items: center;
    }

    #userImage { display: none; }

    .btn-submit {
        background-color: var(--accent-green);
        color: white;
        font-family: 'Fredoka', sans-serif;
        font-size: 1.2rem;
        font-weight: 600;
        padding: 15px 0;
        width: 60%;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        transition: background 0.2s, transform 0.1s;
    }

    .btn-submit:hover {
        background-color: #2ce66b;
        transform: translateY(-2px);
    }
    
    @media (max-width: 900px) {
        .content-container { flex-direction: column; gap: 40px; }
        .hero-panel { transform: rotate(0deg); width: 90%; }
        .upload-panel { width: 90%; }
    }

    .upload-panel {
    transition: all 0.3s ease;
    border: 3px dashed transparent;
}

    .upload-panel.dragover {
        border-color: #9C27B0;
        background-color: rgba(156, 39, 176, 0.05);
        transform: scale(1.02);
    }

    /* Style ajouté uniquement pour le footer */
    footer {
        background: white;
        margin-top: auto;
        box-shadow: 0 -4px 20px rgba(0,0,0,0.02);
        padding: 30px 0;
        text-align: center;
        font-size: 0.9rem;
        color: #888;
    }
    .footer-links {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-top: 10px;
    }
    .footer-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        font-size: 0.85rem;
        font-weight: 600;
        font-family: 'Fredoka', sans-serif;
        padding: 6px 14px;
        border-radius: 20px;
        transition: all 0.2s;
    }
    .footer-btn.purple {
        color: #9C27B0;
        border: 1.5px solid rgba(156,39,176,0.3);
    }
    .footer-btn.dark {
        color: #333;
        border: 1.5px solid rgba(51,51,51,0.3);
    }
    
</style>

<header>
    <a href="index.php?page=home" class="brand-logo">IMG2BRICKS</a>
    
    <div class="auth-buttons">
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
            <span class="user-greeting">Salut, <?= htmlspecialchars($_SESSION['user']['username'] ?? 'Fan de Lego') ?> !</span>
            
            <a href="index.php?page=history" class="btn-header btn-login">
                Mes Commandes
            </a>
            
            <a href="index.php?page=profile" class="btn-header btn-login">
                Compte
            </a>
            
            <a href="index.php?page=logout" class="btn-header btn-logout">
                Déconnexion
            </a>
            
        <?php else: ?>
            <a href="index.php?page=login" class="btn-header btn-login">Connexion</a>
            <a href="index.php?page=register" class="btn-header btn-register">S'inscrire</a>
        <?php endif; ?>
    </div>
</header>

<div class="main-wrapper">
    <div class="content-container">
        
        <div class="hero-panel">
            <h1>Transformez vos images en tableau de briques</h1>
            <div class="transformation-viz">
                <img src="assets/img/demo-original.jpg" alt="Original" class="viz-img">
                <div class="arrow-red">➜</div>
                <img src="assets/img/demo-pixelart.svg" alt="Pixel Art" class="viz-img" style="image-rendering: pixelated;">
            </div>
        </div>

        <div class="upload-panel" id="drop-zone">
            <h2>Lachez votre image ici et laissez la magie opérer</h2>
            <form action="index.php?page=upload" method="POST" enctype="multipart/form-data">
                <div class="file-input-group">
                    <label for="userImage" class="file-label-btn">Parcourir</label>
                    <span class="file-name-display" id="fileName">Aucun fichier choisi</span>
                    <input type="file" name="userImage" id="userImage" accept=".jpg,.jpeg,.png,.webp" required onchange="updateFileName()">
                </div>
                <button type="submit" class="btn-submit">Envoyer</button>
            </form>
        </div>

    </div>
</div>

<footer>
    <p style="margin: 0;">&copy; <?= date('Y') ?> IMG2BRICKS. Turn photos into fun.</p>
    <div class="footer-links">
        <a href="index.php?page=games" class="footer-btn purple">
            <i class="bi bi-controller"></i> Espace Jeux
        </a>
        <a href="https://github.com/Issam-BH/SAELego" target="_blank" class="footer-btn dark">
            <i class="bi bi-github"></i> GitHub
        </a>
        <a href="apk/LegoStore_v1.apk" class="footer-btn dark" style="color: #3DDC84; border-color: #3DDC84;" download>
            <i class="bi bi-android2"></i> App Android
        </a>
    </div>
</footer>
<script>
    function updateFileName() {
        const input = document.getElementById('userImage');
        const display = document.getElementById('fileName');
        if (input.files && input.files.length > 0) {
            display.textContent = input.files[0].name;
        } else {
            display.textContent = "Aucun fichier choisi";
        }
    }

    const dropZone = document.getElementById('drop-zone');
    const fileInput = document.getElementById('userImage');

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => dropZone.classList.add('dragover'), false);
    });
    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => dropZone.classList.remove('dragover'), false);
    });
    dropZone.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        const files = dt.files;

        if (files && files.length > 0) {
            if (files[0].type.startsWith('image/')) {
                fileInput.files = files; 
                updateFileName(); 
            } else {
                alert("Veuillez glisser une image valide (JPG, PNG, WEBP).");
            }
        }
    });
</script>

<?php
$content = ob_get_clean();
echo $content;
?>