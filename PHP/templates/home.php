<?php
// templates/home.php
ob_start();
?>
<style>
    /* ... (Gardez tout votre style CSS existant ici, je ne le répète pas pour alléger) ... */
    /* Import d'une police plus ronde et moderne */
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

    /* HEADER */
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
        border-radius: 30px; /* Plus rond */
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
        background-color: #33FF77; /* Vert fluo */
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

    /* ... Le reste de votre CSS (main-wrapper, hero-panel, etc.) ... */
    
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
</style>

<header>
    <a href="index.php?page=home" class="brand-logo">IMG2BRICKS</a>
    
    <div class="auth-buttons">
        <?php if (class_exists('UserSession') && UserSession::isAuthenticated()): ?>
            <span class="user-greeting">Salut, <?= htmlspecialchars($_SESSION['user']['username'] ?? 'Fan de Lego') ?> !</span>
            
            <a href="index.php?page=history" class="btn-header btn-login">
                <i class="bi bi-box-seam"></i> Mes Commandes
            </a>
            
            <a href="index.php?page=profile" class="btn-header btn-login">
                <i class="bi bi-person-circle"></i> Compte
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
                <img src="https://media.discordapp.net/attachments/1285145800032129047/1473266897633022043/pexels-serap-sagbas-2149016901-30583565.jpg?ex=69959638&is=699444b8&hm=291925242816580126e792afbf12d18dad5e3913a5d57794145235f933f4b24f&=&format=webp&width=533&height=800" alt="Original" class="viz-img">
                <div class="arrow-red">➜</div>
                <img src="https://media.discordapp.net/attachments/1285145800032129047/1473266895959494873/mosaique_finale5.png?ex=69959637&is=699444b7&hm=1c8817da876373a7304638fd75ed21eb782a82a79e1c7982574ab2c5aeb259a5&=&format=webp&quality=lossless&width=800&height=800" alt="Pixel Art" class="viz-img" style="image-rendering: pixelated;">
            </div>
        </div>

        <div class="upload-panel">
            <h2>Lachez votre image ici et laissez la magie opérer</h2>
            <form action="index.php?page=home" method="POST" enctype="multipart/form-data">
                <div class="file-input-group">
                    <label for="userImage" class="file-label-btn">Parcourir</label>
                    <span class="file-name-display" id="fileName">image.png</span>
                    <input type="file" name="userImage" id="userImage" accept=".jpg,.jpeg,.png,.webp" required onchange="updateFileName()">
                </div>
                <button type="submit" class="btn-submit">Envoyer</button>
            </form>
        </div>

    </div>
</div>

<script>
    function updateFileName() {
        const input = document.getElementById('userImage');
        const fileNameDisplay = document.getElementById('fileName');
        if (input.files.length > 0) {
            fileNameDisplay.textContent = input.files[0].name;
        } else {
            fileNameDisplay.textContent = 'image.png';
        }
    }
</script>

<?php
$content = ob_get_clean();
echo $content;
?>