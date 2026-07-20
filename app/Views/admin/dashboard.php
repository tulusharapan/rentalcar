<?= view('admin/layouts/header', [
    'title' => 'Dashboard Admin - Aplikasi',
    'extraHead' => '<style>
        .dashboard-kpi {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            min-height: 122px;
        }
        .dashboard-kpi .value {
            font-size: 1.55rem;
            font-weight: 800;
            line-height: 1.1;
        }
        .dashboard-chart {
            position: relative;
            height: 320px;
        }
        .dashboard-chart-sm {
            position: relative;
            height: 248px;
        }
        .dashboard-list-item {
            display: flex;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid var(--admin-border);
        }
        .dashboard-list-item:last-child {
            border-bottom: 0;
        }
        .availability-grid {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 10px;
        }
        .availability-item {
            border: 1px solid var(--admin-border);
            border-radius: 8px;
            padding: 12px;
            background: #ffffff;
            min-height: 104px;
        }
        .availability-icon {
            width: 34px;
            height: 34px;
            display: grid;
            place-items: center;
            border-radius: 8px;
            margin-bottom: 10px;
            background: #eff6ff;
            color: #2563eb;
        }
        .availability-value {
            font-size: 1.35rem;
            font-weight: 800;
            line-height: 1;
        }
        @media (max-width: 1199.98px) {
            .availability-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
        }
        @media (max-width: 767.98px) {
            .availability-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
    </style>',
]) ?>
<?= view('admin/layouts/sidebar', [
    'activeMenu' => 'dashboard',
    'userName'   => $userName,
    'userEmail'  => $userEmail,
]) ?>
<?= view('admin/layouts/topbar', [
    'pageTitle'    => 'Dashboard Admin',
    'pageSubtitle' => 'Ringkasan operasional, transaksi, dan keuangan rental.',
    'userName'     => $userName,
    'userRole'     => $userRole,
]) ?>

<section class="content-area">
    <?php
        $summary = $summary ?? [];
        $canViewFinancials = $canViewFinancials ?? false;
        $rupiah = static fn ($value) => 'Rp ' . number_format((int) $value, 0, ',', '.');
        $kpiCards = $canViewFinancials ? [
                [
                    'title' => 'Pemasukan Bulan Ini',
                    'value' => $rupiah($summary['pemasukan_bulan_ini'] ?? 0),
                    'note' => 'Pembayaran yang sudah diterima',
                    'icon' => 'bi-cash-coin',
                    'class' => 'success',
                ],
                [
                    'title' => 'Piutang Aktif',
                    'value' => $rupiah($summary['piutang'] ?? 0),
                    'note' => 'Sisa tagihan belum lunas',
                    'icon' => 'bi-wallet2',
                    'class' => 'warning',
                ],
                [
                    'title' => 'Transaksi Berjalan',
                    'value' => (int) ($summary['transaksi_berjalan'] ?? 0),
                    'note' => ((int) ($summary['transaksi_telat'] ?? 0)) . ' transaksi telat',
                    'icon' => 'bi-key-fill',
                    'class' => 'primary',
                ],
                [
                    'title' => 'Kendaraan Ready',
                    'value' => (int) ($summary['kendaraan_ready'] ?? 0) . '/' . (int) ($summary['kendaraan_total'] ?? 0),
                    'note' => ((int) ($summary['kendaraan_maintenance'] ?? 0)) . ' maintenance',
                    'icon' => 'bi-car-front-fill',
                    'class' => 'success',
                ],
            ] : [
            [
                'title' => 'Transaksi Berjalan',
                'value' => (int) ($summary['transaksi_berjalan'] ?? 0),
                'note' => ((int) ($summary['transaksi_telat'] ?? 0)) . ' transaksi telat',
                'icon' => 'bi-key-fill',
                'class' => 'primary',
            ],
            [
                'title' => 'Kendaraan Ready',
                'value' => (int) ($summary['kendaraan_ready'] ?? 0) . '/' . (int) ($summary['kendaraan_total'] ?? 0),
                'note' => ((int) ($summary['kendaraan_maintenance'] ?? 0)) . ' maintenance',
                'icon' => 'bi-car-front-fill',
                'class' => 'success',
            ],
            [
                'title' => 'Booking 7 Hari',
                'value' => (int) ($summary['booking_mendatang'] ?? 0),
                'note' => 'Booking yang segera berjalan',
                'icon' => 'bi-calendar-check-fill',
                'class' => 'warning',
            ],
            [
                'title' => 'Pelanggan Aktif',
                'value' => (int) ($summary['pelanggan_aktif'] ?? 0),
                'note' => 'Pelanggan berstatus aktif',
                'icon' => 'bi-person-vcard-fill',
                'class' => 'primary',
            ],
        ];
        $availabilityToday = $availabilityToday ?? [];
        $availabilityItems = [
            [
                'label' => 'Tersedia',
                'value' => $availabilityToday['tersedia'] ?? 0,
                'icon'  => 'bi-check-circle-fill',
                'class' => 'text-success',
            ],
            [
                'label' => 'Booking',
                'value' => $availabilityToday['booking'] ?? 0,
                'icon'  => 'bi-calendar-check-fill',
                'class' => 'text-warning',
            ],
            [
                'label' => 'Berjalan',
                'value' => $availabilityToday['berjalan'] ?? 0,
                'icon'  => 'bi-key-fill',
                'class' => 'text-primary',
            ],
            [
                'label' => 'Jatuh Tempo',
                'value' => $availabilityToday['jatuh_tempo'] ?? 0,
                'icon'  => 'bi-clock-fill',
                'class' => 'text-warning',
            ],
            [
                'label' => 'Telat',
                'value' => $availabilityToday['telat'] ?? 0,
                'icon'  => 'bi-exclamation-octagon-fill',
                'class' => 'text-danger',
            ],
            [
                'label' => 'Maintenance',
                'value' => $availabilityToday['maintenance'] ?? 0,
                'icon'  => 'bi-tools',
                'class' => 'text-secondary',
            ],
            [
                'label' => 'Nonaktif',
                'value' => $availabilityToday['nonaktif'] ?? 0,
                'icon'  => 'bi-slash-circle',
                'class' => 'text-secondary',
            ],
        ];
    ?>

    <div class="row g-3 mb-4">
        <?php foreach ($kpiCards as $card) : ?>
            <div class="col-12 col-md-6 col-xl-3">
                <div class="summary-card p-4 dashboard-kpi">
                    <div class="min-w-0">
                        <div class="text-secondary small fw-semibold mb-2"><?= esc($card['title']) ?></div>
                        <div class="value"><?= esc((string) $card['value']) ?></div>
                        <div class="small text-secondary mt-2"><?= esc($card['note']) ?></div>
                    </div>
                    <div class="icon-box <?= $card['class'] === 'success' ? 'success' : ($card['class'] === 'warning' ? 'warning' : '') ?>">
                        <i class="bi <?= esc($card['icon']) ?>"></i>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="panel p-4 mb-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between gap-2 mb-3">
            <div>
                <h2 class="h6 fw-bold mb-1">Ketersediaan Kendaraan Hari Ini</h2>
                <div class="small text-secondary">
                    Status per <?= esc(date('d/m/Y', strtotime($availabilityToday['tanggal'] ?? date('Y-m-d')))) ?>. Total armada: <?= esc((int) ($availabilityToday['total'] ?? 0)) ?> kendaraan.
                </div>
            </div>
            <a href="<?= site_url('admin/kendaraan/monitor') ?>" class="btn btn-outline-dark align-self-lg-start">
                <i class="bi bi-clipboard-data"></i>Monitoring
            </a>
        </div>

        <div class="availability-grid">
            <?php foreach ($availabilityItems as $item) : ?>
                <div class="availability-item">
                    <div class="availability-icon <?= esc($item['class']) ?>">
                        <i class="bi <?= esc($item['icon']) ?>"></i>
                    </div>
                    <div class="availability-value <?= esc($item['class']) ?>"><?= esc((int) $item['value']) ?></div>
                    <div class="small text-secondary fw-semibold mt-1"><?= esc($item['label']) ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <?php if ($canViewFinancials) : ?>
            <div class="col-12 col-xl-8">
                <div class="panel p-4 h-100">
                    <div class="d-flex flex-column flex-md-row justify-content-between gap-2 mb-3">
                        <div>
                            <h2 class="h6 fw-bold mb-1">Tren Keuangan 6 Bulan</h2>
                            <div class="small text-secondary">Pemasukan dan pengeluaran berdasarkan transaksi keuangan.</div>
                        </div>
                        <div class="text-md-end">
                            <div class="small text-secondary">Saldo bulan ini</div>
                            <div class="fw-bold <?= ((int) ($summary['saldo_bulan_ini'] ?? 0)) >= 0 ? 'text-success' : 'text-danger' ?>">
                                <?= esc($rupiah($summary['saldo_bulan_ini'] ?? 0)) ?>
                            </div>
                        </div>
                    </div>
                    <div class="dashboard-chart">
                        <canvas id="monthlyFinanceChart"></canvas>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="col-12 <?= $canViewFinancials ? 'col-xl-4' : 'col-xl-12' ?>">
            <div class="panel p-4 h-100">
                <h2 class="h6 fw-bold mb-1">Status Transaksi</h2>
                <div class="small text-secondary mb-3">Komposisi semua invoice sewa.</div>
                <div class="dashboard-chart-sm">
                    <canvas id="transactionStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-12 col-xl-4">
            <div class="panel p-4 h-100">
                <h2 class="h6 fw-bold mb-1">Status Armada</h2>
                <div class="small text-secondary mb-3">Kondisi master kendaraan saat ini.</div>
                <div class="dashboard-chart-sm">
                    <canvas id="fleetStatusChart"></canvas>
                </div>
                <div class="row g-2 mt-2">
                    <div class="col-6">
                        <div class="border rounded p-2">
                            <div class="small text-secondary">Pelanggan Aktif</div>
                            <div class="fw-bold"><?= esc((int) ($summary['pelanggan_aktif'] ?? 0)) ?></div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-2">
                            <div class="small text-secondary">Booking 7 Hari</div>
                            <div class="fw-bold"><?= esc((int) ($summary['booking_mendatang'] ?? 0)) ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="panel p-4 h-100">
                <div class="d-flex justify-content-between gap-2 mb-2">
                    <div>
                        <h2 class="h6 fw-bold mb-1">Pengembalian Terdekat</h2>
                        <div class="small text-secondary">Transaksi berjalan yang jatuh tempo atau telat.</div>
                    </div>
                    <a href="<?= site_url('admin/transaksi-sewa?kondisi=telat') ?>" class="btn btn-sm btn-outline-danger">Telat</a>
                </div>

                <?php if (empty($dueReturns)) : ?>
                    <div class="text-center text-secondary py-4">
                        <i class="bi bi-check-circle fs-1 d-block mb-2"></i>
                        Tidak ada pengembalian mendesak.
                    </div>
                <?php endif; ?>

                <?php foreach ($dueReturns as $row) : ?>
                    <?php
                        $lateDays = $row['tanggal_kembali'] < date('Y-m-d')
                            ? (new DateTime($row['tanggal_kembali']))->diff(new DateTime(date('Y-m-d')))->days
                            : 0;
                    ?>
                    <div class="dashboard-list-item">
                        <div class="icon-box <?= $lateDays > 0 ? 'warning' : '' ?>">
                            <i class="bi <?= $lateDays > 0 ? 'bi-exclamation-octagon-fill' : 'bi-clock-fill' ?>"></i>
                        </div>
                        <div class="min-w-0 flex-grow-1">
                            <div class="d-flex justify-content-between gap-2">
                                <a href="<?= site_url('admin/transaksi-sewa/detail/' . $row['id']) ?>" class="fw-semibold text-decoration-none text-dark">
                                    <?= esc($row['kode_transaksi']) ?>
                                </a>
                                <?php if ($lateDays > 0) : ?>
                                    <span class="badge text-bg-danger">Telat <?= esc($lateDays) ?> hari</span>
                                <?php else : ?>
                                    <span class="badge text-bg-warning">Jatuh tempo</span>
                                <?php endif; ?>
                            </div>
                            <div class="small text-secondary text-truncate"><?= esc($row['nama_kendaraan'] . ' - ' . $row['plat_nomor']) ?></div>
                            <div class="small"><?= esc($row['nama_lengkap']) ?>, kembali <?= esc(date('d/m/Y', strtotime($row['tanggal_kembali']))) ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="panel p-4 h-100">
                <div class="d-flex justify-content-between gap-2 mb-2">
                    <div>
                        <h2 class="h6 fw-bold mb-1">Transaksi Terbaru</h2>
                        <div class="small text-secondary">Invoice terbaru dan status pembayaran.</div>
                    </div>
                    <a href="<?= site_url('admin/transaksi-sewa') ?>" class="btn btn-sm btn-outline-dark">Lihat</a>
                </div>

                <?php foreach ($recentTransaksi as $row) : ?>
                    <div class="dashboard-list-item">
                        <div class="avatar">
                            <?= esc(substr((string) $row['nama_lengkap'], 0, 1)) ?>
                        </div>
                        <div class="min-w-0 flex-grow-1">
                            <div class="d-flex justify-content-between gap-2">
                                <a href="<?= site_url('admin/transaksi-sewa/detail/' . $row['id']) ?>" class="fw-semibold text-decoration-none text-dark">
                                    <?= esc($row['kode_transaksi']) ?>
                                </a>
                                <?php if ($row['status_pembayaran'] === 'lunas') : ?>
                                    <span class="badge text-bg-success">Lunas</span>
                                <?php elseif ($row['status_pembayaran'] === 'belum_lunas') : ?>
                                    <span class="badge text-bg-primary">Belum Lunas</span>
                                <?php else : ?>
                                    <span class="badge text-bg-secondary">Belum Bayar</span>
                                <?php endif; ?>
                            </div>
                            <div class="small text-secondary text-truncate"><?= esc($row['nama_lengkap'] . ' - ' . $row['plat_nomor']) ?></div>
                            <?php if ($canViewFinancials) : ?>
                                <div class="small">
                                    <?= esc($rupiah($row['total_bayar'])) ?> / <?= esc($rupiah($row['total_tagihan'])) ?>
                                </div>
                            <?php else : ?>
                                <div class="small text-secondary">
                                    Status: <?= esc(ucfirst(str_replace('_', ' ', $row['status_transaksi']))) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const moneyFormatter = new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0
    });

    const canViewFinancials = <?= $canViewFinancials ? 'true' : 'false' ?>;
    const monthlyFinanceData = <?= json_encode($monthlyChart ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
    const transactionStatusData = <?= json_encode($transactionChart ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
    const fleetStatusData = <?= json_encode($fleetChart ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;

    if (window.Chart) {
        if (canViewFinancials && document.getElementById('monthlyFinanceChart')) {
            new Chart(document.getElementById('monthlyFinanceChart'), {
            type: 'bar',
            data: {
                labels: monthlyFinanceData.labels || [],
                datasets: [
                    {
                        label: 'Pemasukan',
                        data: monthlyFinanceData.pemasukan || [],
                        backgroundColor: '#16a34a',
                        borderRadius: 6
                    },
                    {
                        label: 'Pengeluaran',
                        data: monthlyFinanceData.pengeluaran || [],
                        backgroundColor: '#f59e0b',
                        borderRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return context.dataset.label + ': ' + moneyFormatter.format(context.raw || 0);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (value) {
                                return moneyFormatter.format(value);
                            }
                        }
                    }
                }
            }
            });
        }

        new Chart(document.getElementById('transactionStatusChart'), {
            type: 'doughnut',
            data: {
                labels: transactionStatusData.labels || [],
                datasets: [{
                    data: transactionStatusData.values || [],
                    backgroundColor: ['#f59e0b', '#2563eb', '#16a34a', '#dc2626'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } },
                cutout: '62%'
            }
        });

        new Chart(document.getElementById('fleetStatusChart'), {
            type: 'doughnut',
            data: {
                labels: fleetStatusData.labels || [],
                datasets: [{
                    data: fleetStatusData.values || [],
                    backgroundColor: ['#16a34a', '#f59e0b', '#6b7280'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } },
                cutout: '62%'
            }
        });
    }
</script>

<?= view('admin/layouts/footer') ?>
