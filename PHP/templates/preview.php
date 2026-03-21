<?php ob_start(); ?>

<div class="container mt-4">
    <div class="row g-4 align-items-stretch">
        <div class="col-lg-8">
            <div class="card bg-dark border-0 rounded-4 overflow-hidden h-100 shadow-sm position-relative">
                <div class="d-flex align-items-center justify-content-center h-100 p-4" style="min-height: 400px; background: #222;">
                    <img id="image-preview" 
                         src="image.php?id=<?= htmlspecialchars($image['id_upload']) ?>" 
                         style="max-width: 100%; max-height: 550px; display: block; box-shadow: 0 0 20px rgba(0,0,0,0.5);">
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 rounded-4 shadow-sm h-100">
                <div class="card-body p-4 d-flex flex-column">
                    <h3 class="mb-2 text-primary fw-bold">Préparation</h3>
                    <p class="text-muted small mb-4">Ajustez votre image avant la transformation.</p>

                    <div class="d-grid gap-2 mb-3">
                        <button type="button" class="btn btn-outline-primary fw-bold" onclick="startCropper()">
                            <i class="bi bi-crop me-2"></i> Recadrer l'image
                        </button>
                        <button type="button" class="btn btn-success fw-bold text-white" id="btn-save-crop" style="display:none;" onclick="saveCrop()">
                            <i class="bi bi-check-lg me-2"></i> Valider le recadrage
                        </button>
                    </div>

                    <div id="crop-message" class="alert alert-success border-0 bg-success-subtle text-success-emphasis rounded-3 mb-4" style="display:none;"></div>

                    <hr class="border-secondary-subtle mb-4">

                    <form action="index.php?page=results" method="POST" id="form-generate" class="mt-auto">
                        
                        <input type="hidden" name="id_upload" id="input-upload-id" value="<?= htmlspecialchars($image['id_upload']) ?>">

                        <div class="mb-4">
                            <label class="form-label fw-bold text-uppercase small text-muted">Taille de la mosaïque</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-grid-3x3"></i></span>
                                <select name="size_option" class="form-select form-select-lg border-start-0 bg-light ps-0" style="font-weight: 600;">
                                    <option value="32">32x32 (Rapide)</option>
                                    <option value="64" selected>64x64 (Standard)</option>
                                    <option value="96">96x96 (Grand)</option>
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 btn-lg py-3 rounded-3 shadow-sm fw-bold">
                            ✨ Générer la Mosaïque
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
<script>
    let cropper;
    const image = document.getElementById('image-preview');
    const btnSave = document.getElementById('btn-save-crop');
    const inputId = document.getElementById('input-upload-id');
    const msgBox = document.getElementById('crop-message');

    function startCropper() {
        if (cropper) cropper.destroy();
        cropper = new Cropper(image, { 
            aspectRatio: 1, 
            viewMode: 1, 
            autoCropArea: 0.8, 
            background: false 
        });
        btnSave.style.display = 'block';
        msgBox.style.display = 'none';
    }

    function saveCrop() {
        if (!cropper) return;
        
        btnSave.disabled = true;
        btnSave.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sauvegarde...';
        
        const canvas = cropper.getCroppedCanvas({ width: 512, height: 512 });
        const base64Image = canvas.toDataURL('image/jpeg');

        fetch('index.php?page=crop', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ image: base64Image, original_id: inputId.value })
        })
        .then(r => r.json())
        .then(d => {
            if (d.success) {
                // REDIRECTION VERS LA NOUVELLE IMAGE (corrige le bug d'affichage)
                window.location.href = 'index.php?page=preview&id_upload=' + d.new_id;
            } else {
                alert("Erreur lors du recadrage.");
                btnSave.disabled = false; 
                btnSave.innerHTML = '<i class="bi bi-check-lg me-2"></i> Valider le recadrage';
            }
        })
        .catch(e => {
            console.error(e);
            alert("Erreur réseau.");
            btnSave.disabled = false; 
            btnSave.innerHTML = '<i class="bi bi-check-lg me-2"></i> Valider le recadrage';
        });
    }
</script>

<?php $content = ob_get_clean(); require __DIR__ . '/layout.php'; ?>