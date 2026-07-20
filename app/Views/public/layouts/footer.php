<?php
$setting = $setting ?? (function_exists('app_setting') ? app_setting() : []);
$appName = trim((string) ($setting['nama_aplikasi'] ?? 'Rental Kendaraan')) ?: 'Rental Kendaraan';
$companyName = trim((string) ($setting['nama_perusahaan'] ?? $appName)) ?: $appName;
?>
<footer class="footer-public py-4">
    <div class="container d-flex flex-column flex-md-row justify-content-between gap-2 small">
        <div>&copy; <?= date('Y') ?> <?= esc($companyName) ?>. Semua hak dilindungi.</div>
        <div>Rental mobil dan motor dengan armada siap jalan.</div>
    </div>
</footer>
