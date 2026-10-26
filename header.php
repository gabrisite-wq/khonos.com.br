<header>
    <div id="kh-b-line">
        <h1>
            Aratã by Khonos
        </h1>
    </div>
    <div id="hdr">
        <div class="brand-strip">
            <div class="container">Aratã by Khono's</div>
        </div>
        <div class="container topbar">
            <a href="index.php" class="brand" aria-label="Aratã by Khono's">
                <span class="brand-submark" aria-hidden="true">
                    <img src="src/images/Logotipo%20-%20Varia%C3%A7%C3%B5es/Isotipo%20Arat%C3%A3%20-%20Original.png" alt="" />
                </span>
                <img class="brand-wordmark" src="src/images/Logotipo%20-%20Varia%C3%A7%C3%B5es/Cor%20Original%20-%20PNG.png" alt="Aratã by Khono's" />
            </a>
            
            <button class="header-menu-toggle" type="button" aria-expanded="false" aria-controls="header-navigation">
                Menu <span class="purchase-chevron" aria-hidden="true"></span>
            </button>
            <nav id="header-navigation" aria-label="Navegação principal">
                <a <?php $homePath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: ''; echo ($homePath === '' || str_ends_with($homePath, '/') || basename($homePath) === 'index.php') ? 'id="current-page" aria-current="page"' : ''; ?> href="index.php" aria-label="Início" title="Início"><i class="fa-solid fa-house" aria-hidden="true"></i></a>
                <a <?php $currentP = $_SERVER['REQUEST_URI'] ?? ''; echo (strpos($currentP, 'quem-somos') !== false) ? 'id="current-page"' : ''; ?> href="quem-somos.php">Quem somos</a>
                <a <?php $currentP = $_SERVER['REQUEST_URI'] ?? ''; echo (strpos($currentP, 'objetivo') !== false) ? 'id="current-page"' : ''; ?> href="objetivo.php">Objetivo</a>
                
                <a <?php $currentP = $_SERVER['REQUEST_URI'] ?? ''; echo (strpos($currentP, 'products') !== false) ? 'id="current-page"' : ''; ?> href="products.php">Coleção</a>
                <a <?php $currentP = $_SERVER['REQUEST_URI'] ?? ''; echo (strpos($currentP, 'contato') !== false) ? 'id="current-page"' : ''; ?> href="contato.php">Contato</a>
            </nav>
        </div>
    </div> 
    <!--
    <nav class="collection-nav" aria-label="Coleções">
        <a href="index.php#produtos">Fitness</a>
        <a href="#produtos">Sleepwear</a>
        <a href="#produtos">Casual</a>
    </nav>
    -->
</header>
<script>
(() => {
    const topbar = document.querySelector('.topbar');
    const toggle = topbar.querySelector('.header-menu-toggle');
    const navigation = topbar.querySelector('nav');
    const mobile = window.matchMedia('(max-width: 640px)');

    function setMenuOpen(open) {
        toggle.setAttribute('aria-expanded', String(open));
        topbar.classList.toggle('is-menu-open', open);
        navigation.inert = mobile.matches && !open;
    }

    topbar.classList.add('has-mobile-menu');
    setMenuOpen(false);
    toggle.addEventListener('click', () => setMenuOpen(toggle.getAttribute('aria-expanded') !== 'true'));
    topbar.addEventListener('keydown', event => {
        if (event.key === 'Escape' && toggle.getAttribute('aria-expanded') === 'true') {
            setMenuOpen(false);
            toggle.focus();
        }
    });
    document.addEventListener('click', event => {
        if (!topbar.contains(event.target)) setMenuOpen(false);
    });
    topbar.addEventListener('focusout', event => {
        if (!topbar.contains(event.relatedTarget)) setMenuOpen(false);
    });
    navigation.addEventListener('click', event => {
        if (event.target.closest('a')) setMenuOpen(false);
    });
    mobile.addEventListener('change', () => setMenuOpen(false));
})();
</script>