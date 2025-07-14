const dragDropArea = document.getElementById('drag-drop-area');
const fileInput = document.getElementById('file-input');
const previewArea = document.getElementById('preview-area');
const imagePreview = document.getElementById('image-preview');
const submitBtn = document.getElementById('submit-btn');
const resultArea = document.getElementById('result-area');
const originalImage = document.getElementById('original-image');
const processedImage = document.getElementById('processed-image');
const downloadBtn = document.getElementById('download-btn');

dragDropArea.addEventListener('click', () => fileInput.click());

dragDropArea.addEventListener('dragover', (e) => {
    e.preventDefault();
    dragDropArea.classList.add('drag-over');
});

dragDropArea.addEventListener('dragleave', () => {
    dragDropArea.classList.remove('drag-over');
});

dragDropArea.addEventListener('drop', (e) => {
    e.preventDefault();
    dragDropArea.classList.remove('drag-over');
    const file = e.dataTransfer.files[0];
    handleFile(file);
});

fileInput.addEventListener('change', (e) => {
    const file = e.target.files[0];
    handleFile(file);
});

function handleFile(file) {
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.src = e.target.result;
            previewArea.style.display = 'block';
            submitBtn.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
}

submitBtn.addEventListener('click', async () => {
    const file = fileInput.files[0];
    if (file) {
        const formData = new FormData();
        formData.append('image', file);

        try {
            const response = await fetch('upload.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                originalImage.src = result.original;
                processedImage.src = result.processed;
                downloadBtn.href = result.processed;
                resultArea.style.display = 'block';
            } else {
                alert(result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred while processing the image.');
        }
    }
});
