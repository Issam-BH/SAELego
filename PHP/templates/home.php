<?php ob_start(); ?>
    <div class="text-center py-5">
        <h1 class="display-4">Turn your photos into bricks!</h1>
        <p class="lead">Upload an image to start the magic.</p>

        <form action="index.php?page=home" method="POST" enctype="multipart/form-data" class="mt-4 border p-5 rounded border-dashed" style="border: 2px dashed #ccc;">
            <div class="mb-3">
                <label for="userImage" class="form-label h5">Choose your image</label>
                <input class="form-control form-control-lg" type="file" name="userImage" id="userImage" accept=".jpg,.jpeg,.png,.webp" required>
            </div>
            <button type="submit" class="btn btn-success btn-lg mt-3">Start !</button>
        </form>
    </div>
<?php $content = ob_get_clean(); require __DIR__ . '/layout.php'; ?>