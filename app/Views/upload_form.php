<!DOCTYPE html>
<html lang="en">
<head>
    <title>Upload Form</title>
    <style>
        .drag-area {
            width: 100%;
            max-width: 400px;
            height: 200px;
            border: 2px dashed #ccc;
            color: #ccc;
            line-height: 200px;
            text-align: center;
            margin: 10px 0;
        }
        .drag-area:hover {
            cursor: pointer;
        }
    </style>
</head>
<body>

<?php if (empty($files)): ?>
    <div style="display: flex; flex-direction: column; align-items: center;">
        <h2>Uploaded File List</h2>
        <p>There are currently no files.</p>
        <br><br>
    </div>
<?php else: ?>
    <div style="display: flex; flex-direction: column; align-items: center;">
        <h2>Uploaded File List</h2>
        <ul>
            <?php foreach ($files as $file): ?>
                <li>
                    <?php echo $file['title']; ?>
                    <a href="<?php echo base_url('upload/download_file/' . $file['filename']); ?>" class="btn btn-primary">Download</a>
                </li>
                <br>
            <?php endforeach; ?>
        </ul>
        <br><br>
    </div>
<?php endif; ?>

<?= form_open_multipart(base_url() . 'upload/upload_file') ?>
    <div style="display: flex; flex-direction: column; align-items: center;">
        <label for="title">Item Name</label>
        <input type="text" name="title" size="20" required>
        <input type="file" id="userfile" name="userfile[]" size="20" multiple hidden>
        <br><br>
        <div id="drag-area" class="drag-area">
            Drag and Drop Files Here or Click to Select Files
        </div>
        <br><br>
        <p>Warning: you need to log in first to uplaod a file.</p>
        <input type="submit" value="upload">
    </div>
</form>

<script>
    const dropArea = document.getElementById('drag-area');
    const fileInput = document.getElementById('userfile');

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults (e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropArea.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, unhighlight, false);
    });

    function highlight(e) {
        dropArea.style.borderColor = 'lime';
    }

    function unhighlight(e) {
        dropArea.style.borderColor = '#ccc';
    }

    dropArea.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        let dt = e.dataTransfer;
        let files = dt.files;

        handleFiles(files);
    }

    function handleFiles(files) {
        fileInput.files = files;
        updateDragArea(files);
    }

    function updateDragArea(files) {
        if (files.length > 0) {
            dropArea.textContent = Array.from(files).map(file => file.name).join(', ');
        } else {
            dropArea.textContent = "Drag and Drop Files Here or Click to Select Files";
        }
    }

    dropArea.addEventListener('click', () => {
        fileInput.click();
    });

    fileInput.addEventListener('change', () => {
        handleFiles(fileInput.files);
    });
</script>

</body>
</html>