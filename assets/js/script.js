
document.addEventListener('DOMContentLoaded', function () {

    // SIDEBAR MOBILE
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');

    function toggleSidebar() {
        if (!sidebar) return;

        sidebar.classList.toggle('show');

        if (overlay) {
            overlay.classList.toggle(
                'show',
                sidebar.classList.contains('show')
            );
        }
    }

    if (menuToggle) {
        menuToggle.addEventListener('click', toggleSidebar);
    }

    if (overlay) {
        overlay.addEventListener('click', toggleSidebar);
    }


    // PERHITUNGAN TRANSAKSI
    const serviceSelect = document.getElementById('serviceSelect');
    const weightInput = document.getElementById('weightInput');
    const totalPreview = document.getElementById('totalPreview');
    const estimatePreview = document.getElementById('estimatePreview');

    function calculateTransaction() {

        if (
            !serviceSelect ||
            !weightInput ||
            !totalPreview
        ) {
            return;
        }

        const selected = serviceSelect.options[
            serviceSelect.selectedIndex
        ];

        const price = Number(selected?.dataset.price || 0);
        const duration = Number(selected?.dataset.duration || 0);
        const weight = Number(weightInput.value || 0);

        const total = price * weight;

        totalPreview.textContent =
            'Rp ' + Math.round(total).toLocaleString('id-ID');

        if (!estimatePreview) return;

        if (!serviceSelect.value || duration <= 0) {
            estimatePreview.textContent = '-';
            return;
        }

        const finish = new Date(
            Date.now() + duration * 60 * 60 * 1000
        );

        estimatePreview.textContent =
            finish.toLocaleString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
    }

    if (serviceSelect) {
        serviceSelect.addEventListener(
            'change',
            calculateTransaction
        );
    }

    if (weightInput) {
        weightInput.addEventListener(
            'input',
            calculateTransaction
        );
    }

    calculateTransaction();

});
