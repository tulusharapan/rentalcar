<script src="https://unpkg.com/filepond-plugin-file-validate-type@^1/dist/filepond-plugin-file-validate-type.js"></script>
<script src="https://unpkg.com/filepond-plugin-file-validate-size@^2/dist/filepond-plugin-file-validate-size.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-preview@^4/dist/filepond-plugin-image-preview.js"></script>
<script src="https://unpkg.com/filepond@^4/dist/filepond.js"></script>
<script>
    FilePond.registerPlugin(FilePondPluginFileValidateType, FilePondPluginFileValidateSize, FilePondPluginImagePreview);

    let csrfHash = '<?= csrf_hash() ?>';
    const csrfName = '<?= csrf_token() ?>';
    const saveButton = document.getElementById('saveButton');
    let activeUploads = 0;

    const updateCsrf = function (newHash) {
        if (! newHash) {
            return;
        }

        csrfHash = newHash;

        const csrfInput = document.querySelector('input[name="' + csrfName + '"]');

        if (csrfInput) {
            csrfInput.value = newHash;
        }
    };

    const setSaveButtonState = function () {
        if (saveButton) {
            saveButton.disabled = activeUploads > 0;
        }
    };

    const parseUploadResponse = function (response) {
        const data = JSON.parse(response);
        updateCsrf(data.csrfHash);

        return data.fileName;
    };

    FilePond.setOptions({
        allowMultiple: true,
        maxParallelUploads: 3,
        maxFileSize: '2MB',
        acceptedFileTypes: ['image/png', 'image/jpeg', 'image/webp'],
        labelIdle: 'Tarik foto ke sini atau <span class="filepond--label-action">pilih foto</span>',
        labelFileProcessing: 'Mengupload',
        labelFileProcessingComplete: 'Upload selesai',
        labelFileProcessingAborted: 'Upload dibatalkan',
        labelFileProcessingError: 'Upload gagal',
        labelFileTypeNotAllowed: 'Tipe file tidak diizinkan',
        fileValidateTypeLabelExpectedTypes: 'Gunakan gambar jpg, png, atau webp',
        labelMaxFileSizeExceeded: 'Ukuran file terlalu besar',
        labelMaxFileSize: 'Maksimal {filesize}',
        server: {
            process: function (fieldName, file, metadata, load, error, progress, abort) {
                const formData = new FormData();
                const request = new XMLHttpRequest();

                formData.append(fieldName, file, file.name);
                request.open('POST', '<?= site_url('admin/kendaraan/upload-photo') ?>');
                request.setRequestHeader('X-CSRF-TOKEN', csrfHash);

                request.upload.onprogress = function (event) {
                    progress(event.lengthComputable, event.loaded, event.total);
                };

                request.onload = function () {
                    if (request.status >= 200 && request.status < 300) {
                        load(parseUploadResponse(request.responseText));
                        return;
                    }

                    try {
                        const data = JSON.parse(request.responseText);
                        updateCsrf(data.csrfHash);
                        error(data.error || 'Upload gagal.');
                    } catch (parseError) {
                        error('Upload gagal.');
                    }
                };

                request.onerror = function () {
                    error('Upload gagal.');
                };

                request.send(formData);

                return {
                    abort: function () {
                        request.abort();
                        abort();
                    }
                };
            },
            revert: function (uniqueFileId, load) {
                const request = new XMLHttpRequest();

                request.open('DELETE', '<?= site_url('admin/kendaraan/revert-photo') ?>');
                request.setRequestHeader('X-CSRF-TOKEN', csrfHash);

                request.onload = function () {
                    try {
                        const data = JSON.parse(request.responseText);
                        updateCsrf(data.csrfHash);
                    } catch (error) {
                    }

                    load();
                };

                request.send(uniqueFileId);
            }
        }
    });

    const ponds = FilePond.parse(document.body);

    ponds.forEach(function (pond) {
        pond.on('processfilestart', function () {
            activeUploads += 1;
            setSaveButtonState();
        });

        pond.on('processfile', function () {
            activeUploads = Math.max(0, activeUploads - 1);
            setSaveButtonState();
        });

        pond.on('processfileabort', function () {
            activeUploads = Math.max(0, activeUploads - 1);
            setSaveButtonState();
        });

        pond.on('processfilerevert', function () {
            setSaveButtonState();
        });
    });
</script>
