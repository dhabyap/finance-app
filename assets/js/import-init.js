// Import Page Initialization
$(document).ready(function () {
    const $fileInput = $('#fileInput');
    const $btnChooseFile = $('#btnChooseFile');
    const $fileName = $('#fileName');
    const $btnUpload = $('#btnUpload');
    const $uploadForm = $('#uploadForm');

    // Handle button click to reset input and trigger picker
    $btnChooseFile.on('click', function () {
        $fileInput.val('').click();
    });

    // Trigger upload on file selection
    $fileInput.on('change', function () {
        const file = this.files[0];
        if (!file) return;

        // Validate file size (2MB)
        if (file.size > 2 * 1024 * 1024) {
            alert('File is too large! Maximum size is 2MB.');
            $(this).val('');
            return;
        }

        const formData = new FormData();
        formData.append('file', file);
        
        // CSRF token from form data attributes
        const csrfName = $uploadForm.data('csrf-name');
        const csrfHash = $uploadForm.data('csrf-hash');
        if (csrfName && csrfHash) {
            formData.append(csrfName, csrfHash);
        }

        // Show loading state
        $fileName.text('Uploading...');
        $btnUpload.prop('disabled', true);

        $.ajax({
            url: $uploadForm.data('upload-url'),
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function (res) {
                if (res.status === 'success') {
                    window.location.href = res.redirect;
                } else {
                    alert(res.message || 'Upload failed');
                    $fileName.text('Choose File');
                    $btnUpload.prop('disabled', false);
                }
            },
            error: function (xhr) {
                alert('Upload error: ' + (xhr.responseJSON?.message || xhr.statusText));
                $fileName.text('Choose File');
                $btnUpload.prop('disabled', false);
            }
        });
    });
});
