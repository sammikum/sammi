<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Background Remover</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'templates/parts/header.php'; ?>

    <main>
        <div class="upload-container">
            <h1>Upload an Image to Remove the Background</h1>
            <div id="drag-drop-area">
                <p>Drag & drop an image here or click to select a file</p>
                <input type="file" id="file-input" accept="image/png, image/jpeg, image/jpg" hidden>
            </div>
            <div id="preview-area" style="display: none;">
                <h2>Preview</h2>
                <img id="image-preview" src="#" alt="Image Preview">
            </div>
            <button id="submit-btn" style="display: none;">Remove Background</button>
        </div>

        <div id="result-area" style="display: none;">
            <h2>Result</h2>
            <div class="result-images">
                <div class="image-container">
                    <h3>Original</h3>
                    <img id="original-image" src="#" alt="Original Image">
                </div>
                <div class="image-container">
                    <h3>Background Removed</h3>
                    <img id="processed-image" src="#" alt="Processed Image">
                </div>
            </div>
            <a id="download-btn" href="#" download="processed_image.png">Download Processed Image</a>
        </div>
    </main>

    <?php include 'templates/parts/footer.php'; ?>

    <script src="assets/js/main.js"></script>
</body>
</html>
