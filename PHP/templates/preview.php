<?php ob_start(); ?>
    <h2 class="mb-4">Crop your image</h2>
    <div class="row">
        <div class="col-md-8 text-center bg-dark">
            <img id="imageToCrop" src="image.php?id=<?= $uploadId ?>" style="max-width: 100%;">
        </div>
        <div class="col-md-4">
            <h4>Options</h4>
            <p>Select the area to transform.</p>

            <form action="index.php?page=results" method="POST">
                <input type="hidden" name="id_upload" value="<?= $uploadId ?>">

                <div class="mb-3">
                    <label>Board size</label>
                    <select name="size" class="form-select">
                        <option value="32">32x32 studs</option>
                        <option value="64" selected>64x64 studs</option>
                        <option value="96">96x96 studs</option>
                    </select>
                </div>

                <input type="hidden" name="crop_x" id="crop_x">
                <input type="hidden" name="crop_y" id="crop_y">
                <input type="hidden" name="crop_width" id="crop_width">
                <input type="hidden" name="crop_height" id="crop_height">

                <button type="submit" class="btn btn-primary w-100 mt-3">Generate Mosaic</button>
            </form>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <script>
        const image = document.getElementById('imageToCrop');
        const cropper = new Cropper(image, {
            aspectRatio: 1,
            viewMode: 1,
            autoCropArea: 0.8,
            crop(event) {
                document.getElementById('crop_x').value = Math.round(event.detail.x);
                document.getElementById('crop_y').value = Math.round(event.detail.y);
                document.getElementById('crop_width').value = Math.round(event.detail.width);
                document.getElementById('crop_height').value = Math.round(event.detail.height);
            },
        });
    </script>
<?php $content = ob_get_clean(); require __DIR__ . '/layout.php'; ?>