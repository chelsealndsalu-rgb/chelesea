</main>

        <footer class="app-footer">
            &copy; <?= date('Y') ?> FreshLaundry - Sistem Kasir Laundry
        </footer>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= e($baseUrl ?? '/laundrypos') ?>/assets/js/script.js"></script>
<script>
    (function () {
        const btn = document.getElementById('mobileMenuBtn');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        if (!btn || !sidebar) return;

        const toggleMenu = () => {
            sidebar.classList.toggle('show');
            if (overlay) overlay.style.display = sidebar.classList.contains('show') ? 'block' : 'none';
        };
        btn.addEventListener('click', toggleMenu);
        overlay?.addEventListener('click', toggleMenu);
    })();
</script>

</body>
</html>