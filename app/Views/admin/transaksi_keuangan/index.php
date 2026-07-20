<?= view('admin/layouts/header', ['title' => 'Transaksi Keuangan - Aplikasi']) ?>
<?= view('admin/layouts/sidebar', [
    'activeMenu' => 'transaksi-keuangan',
    'userName'   => $userName,
    'userEmail'  => $userEmail,
]) ?>
<?= view('admin/layouts/topbar', [
    'pageTitle'    => 'Transaksi Keuangan',
    'pageSubtitle' => 'Catat pemasukan dan pengeluaran usaha rental.',
    'topbarAction' => '<div class="d-flex gap-2"><a href="' . site_url('admin/transaksi-keuangan/laporan') . '" class="btn btn-outline-dark"><i class="bi bi-file-earmark-spreadsheet me-1"></i>Laporan</a><a href="' . site_url('admin/transaksi-keuangan/create') . '" class="btn btn-dark"><i class="bi bi-plus-lg me-1"></i>Tambah Transaksi</a></div>',
    'userName'     => $userName,
    'userRole'     => $userRole,
]) ?>

<section class="content-area">
    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="panel p-3 h-100">
                <div class="small text-secondary">Total Pemasukan</div>
                <div class="fs-5 fw-bold text-success">Rp <?= number_format((int) $summary['pemasukan'], 0, ',', '.') ?></div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="panel p-3 h-100">
                <div class="small text-secondary">Total Pengeluaran</div>
                <div class="fs-5 fw-bold text-danger">Rp <?= number_format((int) $summary['pengeluaran'], 0, ',', '.') ?></div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="panel p-3 h-100">
                <div class="small text-secondary">Saldo</div>
                <div class="fs-5 fw-bold <?= (int) $summary['saldo'] >= 0 ? 'text-success' : 'text-danger' ?>">Rp <?= number_format((int) $summary['saldo'], 0, ',', '.') ?></div>
            </div>
        </div>
    </div>

    <div class="panel p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle datatable">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Jenis</th>
                        <th>Kategori</th>
                        <th>Nominal</th>
                        <th>Catatan</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transaksiKeuangan as $row) : ?>
                        <tr>
                            <td><?= esc(date('d/m/Y', strtotime($row['tanggal']))) ?></td>
                            <td>
                                <?php if ($row['jenis'] === 'Pemasukan') : ?>
                                    <span class="badge text-bg-success">Pemasukan</span>
                                <?php else : ?>
                                    <span class="badge text-bg-danger">Pengeluaran</span>
                                <?php endif; ?>
                            </td>
                            <td class="fw-semibold"><?= esc($row['kategori']) ?></td>
                            <td>Rp <?= number_format((int) $row['nominal'], 0, ',', '.') ?></td>
                            <td><?= esc($row['catatan'] ?: '-') ?></td>
                            <td class="text-end text-nowrap">
                                <a href="<?= site_url('admin/transaksi-keuangan/edit/' . $row['id']) ?>" class="btn btn-sm btn-primary btn-icon" title="Edit transaksi" aria-label="Edit transaksi">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="<?= site_url('admin/transaksi-keuangan/delete/' . $row['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus transaksi keuangan ini?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-danger btn-icon" title="Hapus transaksi" aria-label="Hapus transaksi">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?= view('admin/layouts/footer') ?>
