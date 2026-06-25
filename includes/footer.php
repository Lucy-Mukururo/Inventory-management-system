</main>
</div>
</div>
<script>
    // Theme Engine Architecture Logic
    const themeToggle = document.getElementById('themeToggle');
    themeToggle.addEventListener('click', () => {
        if (document.documentElement.classList.contains('dark')) {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('theme', 'light');
        } else {
            document.documentElement.classList.add('dark');
            localStorage.setItem('theme', 'dark');
        }
    });

    // Simple Event Handler Closures for Top Nav Element Dropdowns
    const setupDropdown = (btnId, menuId) => {
        const btn = document.getElementById(btnId);
        const menu = document.getElementById(menuId);
        btn.addEventListener('click', (e) => { e.stopPropagation(); menu.classList.toggle('hidden'); });
        document.addEventListener('click', () => menu.classList.add('hidden'));
    };
    setupDropdown('profileBtn', 'profileMenu');
    setupDropdown('notiBtn', 'notiMenu');
</script>
</body>
</html>