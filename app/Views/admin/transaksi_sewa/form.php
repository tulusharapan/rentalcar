<section class="content-area">
    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <?php $errors = session()->getFlashdata('errors'); ?>
    <?php if ($errors) : ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error) : ?>
                <div><?= esc($error) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php
    $transaksiId = (int) ($transaksi['id'] ?? 0);
    $selectedPelanggan = (string) old('pelanggan_id', $transaksi['pelanggan_id'] ?? '');
    $selectedKendaraan = (string) old('kendaraan_id', $transaksi['kendaraan_id'] ?? '');
    $tanggalTransaksi = old('tanggal_transaksi', $transaksi['tanggal_transaksi'] ?? date('Y-m-d'));
    $tanggalSewa = old('tanggal_sewa', $transaksi['tanggal_sewa'] ?? date('Y-m-d'));
    $tanggalKembali = old('tanggal_kembali', $transaksi['tanggal_kembali'] ?? date('Y-m-d'));
    $tanggalDikembalikan = old('tanggal_dikembalikan', $transaksi['tanggal_dikembalikan'] ?? date('Y-m-d'));
    $selectedStatus = old('status_transaksi', $transaksi['status_transaksi'] ?? 'booking');
    $oldLayananIds = old('layanan_id');
    $oldLayananQty = old('layanan_qty');
    $initialRows = [];

    if (is_array($oldLayananIds)) {
        foreach ($oldLayananIds as $index => $layananId) {
            $initialRows[] = [
                'layanan_tambahan_id' => (int) $layananId,
                'qty' => (int) ($oldLayananQty[$index] ?? 1),
            ];
        }
    } else {
        foreach ($layananTerpilih as $row) {
            $initialRows[] = [
                'layanan_tambahan_id' => (int) $row['layanan_tambahan_id'],
                'qty' => (int) $row['qty'],
            ];
        }
    }

    $layananJs = array_map(static fn ($row) => [
        'id' => (int) $row['id'],
        'nama_layanan' => $row['nama_layanan'],
        'harga_layanan' => (int) $row['harga_layanan'],
    ], $layananOptions);
    ?>

    <form action="<?= esc($action, 'attr') ?>" method="post" id="transaksiForm">
        <?= csrf_field() ?>
        <div class="row g-4">
            <div class="col-12 col-xl-8">
                <div class="panel p-4 h-100">
                    <div class="row g-4">
                        <div class="col-12 col-lg-6">
                            <label for="kode_transaksi" class="form-label fw-semibold">Invoice</label>
                            <input type="text" class="form-control" id="kode_transaksi" value="<?= esc($kodeTransaksi) ?>" readonly>
                        </div>

                        <div class="col-12 col-lg-6">
                            <label for="tanggal_transaksi" class="form-label fw-semibold">Tanggal Transaksi</label>
                            <input type="date" class="form-control" id="tanggal_transaksi" name="tanggal_transaksi" value="<?= esc($tanggalTransaksi) ?>" required>
                        </div>

                     

                        <div class="col-12 col-lg-6">
                            <label for="pelanggan_id" class="form-label fw-semibold">Pelanggan Aktif</label>
                            <select class="form-select" id="pelanggan_id" name="pelanggan_id" required>
                                <option value="">Pilih pelanggan</option>
                                <?php foreach ($pelangganOptions as $pelanggan) : ?>
                                    <option value="<?= esc($pelanggan['id']) ?>" <?= $selectedPelanggan === (string) $pelanggan['id'] ? 'selected' : '' ?>>
                                        <?= esc($pelanggan['kode_pelanggan'] . ' - ' . $pelanggan['nama_lengkap']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12 col-lg-6">
                            <label for="status_transaksi" class="form-label fw-semibold">Status Transaksi</label>
                            <select class="form-select" id="status_transaksi" name="status_transaksi" required>
                                <?php foreach ($statusTransaksiOptions as $value => $label) : ?>
                                    <option value="<?= esc($value) ?>" <?= $selectedStatus === $value ? 'selected' : '' ?>><?= esc($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12 col-lg-12 border border-primary rounded p-3" id="tanggalDikembalikanWrap">
                            <label for="tanggal_dikembalikan" class="form-label fw-semibold">Tanggal Kendaraan Dikembalikan</label>
                            <input type="date" class="form-control" id="tanggal_dikembalikan" name="tanggal_dikembalikan" value="<?= esc($tanggalDikembalikan) ?>">
                            <div class="form-text">Denda dihitung jika tanggal ini melewati tanggal kembali.</div>
                        </div>

                        <div class="col-12 col-lg-12">
                            <label for="kendaraan_id" class="form-label fw-semibold">Kendaraan Tersedia</label>
                            <select class="form-select" id="kendaraan_id" name="kendaraan_id" required>
                                <option value="">Pilih kendaraan</option>
                                <?php foreach ($kendaraanOptions as $kendaraan) : ?>
                                    <option value="<?= esc($kendaraan['id']) ?>" data-price="<?= esc($kendaraan['harga_sewa_per_hari']) ?>" <?= $selectedKendaraan === (string) $kendaraan['id'] ? 'selected' : '' ?>>
                                        <?= esc(($kendaraan['jenis_kendaraan'] ?? 'Mobil') . ' - ' . $kendaraan['plat_nomor'] . ' - ' . $kendaraan['nama_kendaraan'] . ' (Rp ' . number_format((int) $kendaraan['harga_sewa_per_hari'], 0, ',', '.') . '/hari)') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Daftar kendaraan berubah sesuai tanggal sewa dan tanggal kembali.</div>
                        </div>

                        <div class="col-12 col-lg-4">
                            <label for="tanggal_sewa" class="form-label fw-semibold">Tanggal Sewa</label>
                            <input type="date" class="form-control" id="tanggal_sewa" name="tanggal_sewa" value="<?= esc($tanggalSewa) ?>" required>
                        </div>

                        <div class="col-12 col-lg-4">
                            <label for="tanggal_kembali" class="form-label fw-semibold">Tanggal Kembali</label>
                            <input type="date" class="form-control" id="tanggal_kembali" name="tanggal_kembali" value="<?= esc($tanggalKembali) ?>" required>
                        </div>

                        <div class="col-12 col-lg-4">
                            <label for="lama_sewa_display" class="form-label fw-semibold">Lama Sewa</label>
                            <input type="text" class="form-control" id="lama_sewa_display" readonly>
                        </div>

                     

                        <div class="col-12">
                            <label for="catatan" class="form-label fw-semibold">Catatan</label>
                            <textarea class="form-control" id="catatan" name="catatan" rows="3"><?= old('catatan', $transaksi['catatan'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-4">
                <div class="panel p-4 h-100">
                    <div class="fw-semibold mb-3">Ringkasan Biaya</div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-secondary">Lama Sewa Ditagihkan</span>
                        <span id="summaryLamaSewa">0 hari</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-secondary">Harga Sewa / Hari</span>
                        <span id="summaryHargaHarian">Rp 0</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-secondary">Subtotal Sewa</span>
                        <span id="summarySubtotalSewa">Rp 0</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-secondary">Total Layanan</span>
                        <span id="summaryTotalLayanan">Rp 0</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-secondary">Denda / Hari</span>
                        <span id="summaryHargaDendaPerHari">Rp 0</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-secondary">Terlambat</span>
                        <span id="summaryHariTerlambat">0 hari</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-secondary">Denda</span>
                        <span id="summaryDenda">Rp 0</span>
                    </div>
                    <div class="d-flex justify-content-between py-3 fs-5 fw-bold">
                        <span>Total Tagihan</span>
                        <span id="summaryTotalTagihan">Rp 0</span>
                    </div>

                    <?php if ($showInitialPayment) : ?>
                        <div class="mt-3">
                            <label for="jumlah_bayar" class="form-label fw-semibold">Pembayaran Pertama</label>
                            <input type="text" class="form-control currency-input" id="jumlah_bayar" name="jumlah_bayar" value="<?= old('jumlah_bayar', '0') ?>" required>
                        </div>

                        <div class="mt-3">
                            <label for="metode_pembayaran" class="form-label fw-semibold">Metode Pembayaran</label>
                            <select class="form-select" id="metode_pembayaran" name="metode_pembayaran" required>
                                <?php foreach ($metodePembayaranOptions as $value => $label) : ?>
                                    <option value="<?= esc($value) ?>" <?= old('metode_pembayaran', 'tunai') === $value ? 'selected' : '' ?>><?= esc($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-12">
                <div class="panel p-4">
                    <div class="d-flex justify-content-between align-items-center gap-3 mb-3">
                        <div>
                            <div class="fw-semibold">Layanan Tambahan</div>
                            <div class="small text-secondary">Tambahkan layanan, qty, dan total akan dihitung otomatis.</div>
                        </div>
                        <button type="button" class="btn btn-outline-dark" id="addLayananButton">
                            <i class="bi bi-plus-lg me-1"></i>Tambah Layanan
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Layanan</th>
                                    <th style="width: 130px;">Qty</th>
                                    <th style="width: 180px;">Harga</th>
                                    <th style="width: 180px;">Total</th>
                                    <th style="width: 70px;" class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="layananRows"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="<?= site_url('admin/transaksi-sewa') ?>" class="btn btn-light">Batal</a>
            <button type="submit" class="btn btn-dark">Simpan Transaksi</button>
        </div>
    </form>
</section>

<script>
    const layananOptions = <?= json_encode($layananJs, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
    const initialLayananRows = <?= json_encode($initialRows, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
    const transaksiId = <?= $transaksiId ?>;
    const selectedKendaraanId = '<?= esc($selectedKendaraan, 'js') ?>';
    const hargaDendaPerHari = <?= (int) ($hargaDendaPerHari ?? 0) ?>;

    const currency = function (value) {
        return 'Rp ' + Number(value || 0).toLocaleString('id-ID');
    };

    const numeric = function (value) {
        return Number(String(value || '').replace(/\D/g, '')) || 0;
    };

    const tanggalSewaInput = document.getElementById('tanggal_sewa');
    const tanggalKembaliInput = document.getElementById('tanggal_kembali');
    const tanggalDikembalikanInput = document.getElementById('tanggal_dikembalikan');
    const tanggalDikembalikanWrap = document.getElementById('tanggalDikembalikanWrap');
    const statusTransaksiInput = document.getElementById('status_transaksi');
    const kendaraanSelect = document.getElementById('kendaraan_id');
    const layananRows = document.getElementById('layananRows');

    const toDate = function (dateValue) {
        return new Date(dateValue + 'T00:00:00');
    };

    const getTanggalAkhirTagihan = function () {
        if (! tanggalKembaliInput.value) {
            return '';
        }

        if (statusTransaksiInput.value === 'selesai' && tanggalDikembalikanInput.value) {
            const due = toDate(tanggalKembaliInput.value);
            const returned = toDate(tanggalDikembalikanInput.value);

            if (returned > due) {
                return tanggalDikembalikanInput.value;
            }
        }

        return tanggalKembaliInput.value;
    };

    const getLamaSewa = function () {
        const tanggalAkhirTagihan = getTanggalAkhirTagihan();
        const start = toDate(tanggalSewaInput.value);
        const end = toDate(tanggalAkhirTagihan);

        if (! tanggalSewaInput.value || ! tanggalAkhirTagihan || end < start) {
            return 0;
        }

        return Math.floor((end - start) / 86400000) + 1;
    };

    const getHariTerlambat = function () {
        if (statusTransaksiInput.value !== 'selesai' || ! tanggalKembaliInput.value || ! tanggalDikembalikanInput.value) {
            return 0;
        }

        const due = toDate(tanggalKembaliInput.value);
        const returned = toDate(tanggalDikembalikanInput.value);

        if (returned <= due) {
            return 0;
        }

        return Math.floor((returned - due) / 86400000);
    };

    const updateReturnDateVisibility = function () {
        const isReturned = statusTransaksiInput.value === 'selesai';

        tanggalDikembalikanWrap.style.display = isReturned ? '' : 'none';
        tanggalDikembalikanInput.required = isReturned;

        if (isReturned && ! tanggalDikembalikanInput.value) {
            tanggalDikembalikanInput.value = tanggalKembaliInput.value || new Date().toISOString().slice(0, 10);
        }
    };

    const layananOptionHtml = function (selectedId) {
        let html = '<option value="">Pilih layanan</option>';

        layananOptions.forEach(function (layanan) {
            const selected = String(layanan.id) === String(selectedId) ? ' selected' : '';
            html += '<option value="' + layanan.id + '" data-price="' + layanan.harga_layanan + '"' + selected + '>' + layanan.nama_layanan + '</option>';
        });

        return html;
    };

    const addLayananRow = function (selectedId = '', qty = 1) {
        const tr = document.createElement('tr');
        tr.innerHTML = '<td><select class="form-select layanan-select" name="layanan_id[]">' + layananOptionHtml(selectedId) + '</select></td>' +
            '<td><input type="number" class="form-control layanan-qty" name="layanan_qty[]" value="' + qty + '" min="1"></td>' +
            '<td class="layanan-harga">Rp 0</td>' +
            '<td class="layanan-total fw-semibold">Rp 0</td>' +
            '<td class="text-end"><button type="button" class="btn btn-sm btn-danger btn-icon remove-layanan" title="Hapus layanan" aria-label="Hapus layanan"><i class="bi bi-trash3"></i></button></td>';

        layananRows.appendChild(tr);
        if (window.initSelect2Controls) {
            window.initSelect2Controls(tr);
        }

        const layananSelect = tr.querySelector('.layanan-select');
        const layananQty = tr.querySelector('.layanan-qty');

        layananSelect.addEventListener('change', updateTotals);
        layananQty.addEventListener('input', updateTotals);

        if (window.jQuery) {
            $(layananSelect).on('change select2:select select2:clear', updateTotals);
        }

        updateTotals();
    };

    const updateTotals = function () {
        const lamaSewa = getLamaSewa();
        const selectedOption = kendaraanSelect.options[kendaraanSelect.selectedIndex];
        const hargaHarian = selectedOption ? numeric(selectedOption.getAttribute('data-price')) : 0;
        let totalLayanan = 0;
        const denda = getHariTerlambat() * hargaDendaPerHari;

        document.getElementById('lama_sewa_display').value = lamaSewa > 0 ? lamaSewa + ' hari' : '-';

        document.querySelectorAll('#layananRows tr').forEach(function (row) {
            const select = row.querySelector('.layanan-select');
            const option = select.options[select.selectedIndex];
            const qty = Math.max(0, Number(row.querySelector('.layanan-qty').value || 0));
            const harga = option ? numeric(option.getAttribute('data-price')) : 0;
            const total = harga * qty;

            row.querySelector('.layanan-harga').textContent = currency(harga);
            row.querySelector('.layanan-total').textContent = currency(total);
            totalLayanan += total;
        });

        const subtotalSewa = lamaSewa * hargaHarian;
        document.getElementById('summaryLamaSewa').textContent = lamaSewa > 0 ? lamaSewa + ' hari' : '-';
        document.getElementById('summaryHargaHarian').textContent = currency(hargaHarian);
        document.getElementById('summarySubtotalSewa').textContent = currency(subtotalSewa);
        document.getElementById('summaryTotalLayanan').textContent = currency(totalLayanan);
        document.getElementById('summaryHargaDendaPerHari').textContent = currency(hargaDendaPerHari);
        document.getElementById('summaryHariTerlambat').textContent = getHariTerlambat() + ' hari';
        document.getElementById('summaryDenda').textContent = currency(denda);
        document.getElementById('summaryTotalTagihan').textContent = currency(subtotalSewa + totalLayanan + denda);
    };

    const refreshKendaraanOptions = function () {
        if (! tanggalSewaInput.value || ! tanggalKembaliInput.value) {
            updateTotals();
            return;
        }

        const currentValue = kendaraanSelect.value || selectedKendaraanId;
        const url = new URL('<?= site_url('admin/transaksi-sewa/available-kendaraan') ?>');
        url.searchParams.set('tanggal_sewa', tanggalSewaInput.value);
        url.searchParams.set('tanggal_kembali', tanggalKembaliInput.value);
        url.searchParams.set('exclude_id', transaksiId);
        url.searchParams.set('selected_kendaraan_id', currentValue);

        fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (response) { return response.json(); })
            .then(function (data) {
                kendaraanSelect.innerHTML = '<option value="">Pilih kendaraan</option>';

                (data.kendaraan || []).forEach(function (kendaraan) {
                    const option = document.createElement('option');
                    option.value = kendaraan.id;
                    option.setAttribute('data-price', kendaraan.harga_sewa_per_hari);
                    option.textContent = (kendaraan.jenis_kendaraan || 'Mobil') + ' - ' + kendaraan.plat_nomor + ' - ' + kendaraan.nama_kendaraan + ' (' + currency(kendaraan.harga_sewa_per_hari) + '/hari)';

                    if (String(kendaraan.id) === String(currentValue)) {
                        option.selected = true;
                    }

                    kendaraanSelect.appendChild(option);
                });

                updateTotals();
            })
            .catch(updateTotals);
    };

    document.getElementById('addLayananButton').addEventListener('click', function () {
        addLayananRow();
    });

    layananRows.addEventListener('change', updateTotals);
    layananRows.addEventListener('input', updateTotals);
    layananRows.addEventListener('click', function (event) {
        const button = event.target.closest('.remove-layanan');

        if (button) {
            button.closest('tr').remove();
            updateTotals();
        }
    });

    tanggalSewaInput.addEventListener('change', refreshKendaraanOptions);
    tanggalKembaliInput.addEventListener('change', refreshKendaraanOptions);
    tanggalKembaliInput.addEventListener('change', function () {
        updateReturnDateVisibility();
        updateTotals();
    });
    tanggalDikembalikanInput.addEventListener('change', updateTotals);
    statusTransaksiInput.addEventListener('change', function () {
        updateReturnDateVisibility();
        updateTotals();
    });

    if (window.jQuery) {
        $(statusTransaksiInput).on('change select2:select select2:clear', function () {
            updateReturnDateVisibility();
            updateTotals();
        });
    }

    kendaraanSelect.addEventListener('change', updateTotals);

    initialLayananRows.forEach(function (row) {
        addLayananRow(row.layanan_tambahan_id, row.qty || 1);
    });

    updateReturnDateVisibility();
    updateTotals();
</script>
