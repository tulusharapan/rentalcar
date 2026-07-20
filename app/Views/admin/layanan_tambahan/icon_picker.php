<div class="modal fade" id="iconPickerModal" tabindex="-1" aria-labelledby="iconPickerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="iconPickerModalLabel">Pilih Icon</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="row g-2">
                    <?php foreach ($iconOptions as $icon => $label) : ?>
                        <div class="col-6 col-md-4 col-lg-3">
                            <button type="button" class="btn btn-light border w-100 justify-content-start icon-picker-button" data-icon="<?= esc($icon) ?>">
                                <i class="bi <?= esc($icon) ?> me-2"></i>
                                <span class="text-truncate"><?= esc($label) ?></span>
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.icon-picker-button').forEach(function (button) {
        button.addEventListener('click', function () {
            const icon = button.getAttribute('data-icon');
            const input = document.getElementById('icon');
            const preview = document.getElementById('selectedIconPreview');
            const modalElement = document.getElementById('iconPickerModal');

            if (input) {
                input.value = icon;
            }

            if (preview) {
                preview.innerHTML = '<i class="bi ' + icon + '"></i>';
            }

            if (modalElement && window.bootstrap) {
                bootstrap.Modal.getOrCreateInstance(modalElement).hide();
            }
        });
    });
</script>
