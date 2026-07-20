    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="<?= base_url('assets/vendor/datatables/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('assets/vendor/datatables/dataTables.bootstrap5.min.js') ?>"></script>
<script>
    const appSidebar = document.getElementById('appSidebar');
    const sidebarCollapseToggle = document.getElementById('sidebarCollapseToggle');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarMenu = document.getElementById('sidebarMenu');
    let sidebarTooltips = [];

    const destroySidebarTooltips = function () {
        sidebarTooltips.forEach(function (tooltip) {
            tooltip.dispose();
        });
        sidebarTooltips = [];
    };

    const initSidebarTooltips = function () {
        destroySidebarTooltips();

        if (! appSidebar || ! appSidebar.classList.contains('is-collapsed') || ! window.bootstrap) {
            return;
        }

        sidebarTooltips = Array.from(appSidebar.querySelectorAll('.sidebar-link[data-bs-toggle="tooltip"]')).map(function (element) {
            return new bootstrap.Tooltip(element, {
                boundary: document.body,
                trigger: 'hover focus'
            });
        });
    };

    const setSidebarCollapsed = function (collapsed) {
        if (! appSidebar) {
            return;
        }

        appSidebar.classList.toggle('is-collapsed', collapsed);

        if (sidebarCollapseToggle) {
            const icon = sidebarCollapseToggle.querySelector('i');
            sidebarCollapseToggle.setAttribute('aria-label', collapsed ? 'Perbesar sidebar' : 'Perkecil sidebar');
            sidebarCollapseToggle.setAttribute('title', collapsed ? 'Perbesar sidebar' : 'Perkecil sidebar');

            if (icon) {
                icon.className = collapsed ? 'bi bi-layout-sidebar-inset-reverse' : 'bi bi-layout-sidebar-inset';
            }
        }

        initSidebarTooltips();
    };

    if (appSidebar) {
        setSidebarCollapsed(localStorage.getItem('sidebarCollapsed') === '1');
    }

    if (sidebarCollapseToggle && appSidebar) {
        sidebarCollapseToggle.addEventListener('click', function () {
            const collapsed = ! appSidebar.classList.contains('is-collapsed');
            localStorage.setItem('sidebarCollapsed', collapsed ? '1' : '0');
            setSidebarCollapsed(collapsed);
        });
    }

    if (sidebarToggle && sidebarMenu) {
        sidebarToggle.addEventListener('click', function () {
            sidebarMenu.classList.toggle('show');
        });
    }

    if (window.jQuery && $.fn.DataTable) {
        try {
            $('table.datatable').each(function () {
                const table = this;
                const lastHeader = table.querySelector('thead th:last-child');
                const hasActionColumn = lastHeader && lastHeader.textContent.trim().toLowerCase() === 'aksi';
                const isReportTable = table.classList.contains('datatable-report');
                const columnDefs = [];

                if (hasActionColumn) {
                    columnDefs.push({
                        targets: -1,
                        orderable: false,
                        searchable: false
                    });
                }

                const dataTableOptions = {
                    pageLength: 10,
                    ordering: false,
                    order: [],
                    paging: ! isReportTable,
                    searching: ! isReportTable,
                    info: ! isReportTable,
                    lengthMenu: [
                        [10, 25, 50, -1],
                        [10, 25, 50, 'Semua']
                    ],
                    columnDefs: columnDefs,
                    language: {
                        emptyTable: 'Belum ada data.',
                        info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                        infoEmpty: 'Menampilkan 0 data',
                        infoFiltered: '(difilter dari _MAX_ total data)',
                        lengthMenu: 'Tampilkan _MENU_ data',
                        loadingRecords: 'Memuat...',
                        processing: 'Memproses...',
                        search: 'Cari:',
                        zeroRecords: 'Data tidak ditemukan',
                        paginate: {
                            first: 'Pertama',
                            last: 'Terakhir',
                            next: 'Berikutnya',
                            previous: 'Sebelumnya'
                        }
                    }
                };

                $(table).DataTable(dataTableOptions);
            });
        } catch (error) {
            console.warn('DataTables gagal dimuat:', error);
        }
    }

    window.initSelect2Controls = function (context) {
        if (! window.jQuery || ! $.fn.select2) {
            return;
        }

        const root = context ? $(context) : $(document);

        root.find('select.form-select').each(function () {
            const select = $(this);

            if (select.hasClass('select2-hidden-accessible') || select.closest('.dataTables_wrapper').length) {
                return;
            }

            const modalParent = select.closest('.modal');

            select.select2({
                theme: 'bootstrap-5',
                width: '100%',
                dropdownParent: modalParent.length ? modalParent : $(document.body)
            });
        });
    };

    window.initSelect2Controls(document);

    const currencySelector = '.currency-input';

    const formatCurrency = function (value) {
        const digits = String(value || '').replace(/\D/g, '');

        return digits.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    };

    document.querySelectorAll(currencySelector).forEach(function (input) {
        input.value = formatCurrency(input.value);

        input.addEventListener('input', function () {
            input.value = formatCurrency(input.value);
        });
    });

    document.querySelectorAll('form').forEach(function (form) {
        form.addEventListener('submit', function () {
            form.querySelectorAll(currencySelector).forEach(function (input) {
                input.value = String(input.value || '').replace(/\D/g, '');
            });
        });
    });
</script>
</body>
</html>
