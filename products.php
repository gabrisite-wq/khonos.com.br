<?php
$purchaseOptions = [
    'masculino' => [
        '1 camiseta' => [
            'url' => 'https://www.mercadolivre.com.br/camiseta-masculina-arata-gola-careca-malha-mid-fio-40/up/MLBU5269194288',
            'price' => 11390 ,
            'quantity' => 1,
        ],
        'Kit com 2 camisetas' => [
            'url' => 'https://www.mercadolivre.com.br/kit-duas2-camiseta-careca-masculina-arata-malha-mid-fio-40/up/MLBU5420045889?picker=true&quantity=1',
            'price' => 18990,
            'quantity' => 2,
        ],
        'Kit com 3 camisetas' => [
            'url' => 'https://www.mercadolivre.com.br/kit-tres3-camiseta-careca-masculino-arata-malha-mid-fio-40/up/MLBU5431006185',
            'price' => 26990,
            'quantity' => 3,
        ],
    ],
    'feminino' => [
        '1 camiseta' => [
            'url' => 'https://www.mercadolivre.com.br/camiseta-careca-feminina-arata-malha-mid-fio-40/up/MLBU5231967093',
            'price' => 10990,
            'quantity' => 1,
        ],
        'Kit com 2 camisetas' => [
            'url' => '',
            'price' => 17990,
            'quantity' => 2,
        ],
        'Kit com 3 camisetas' => [
            'url' => 'https://www.mercadolivre.com.br/kit-tres-3-camiseta-careca-feminina-arata-malha-mid-fio-40/up/MLBU5418183921',
            'price' => 24990,
            'quantity' => 3,
        ],
    ],
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Coleções</title>
    <meta name="description" content="Coleções Aratã com acesso direto aos produtos no Mercado Livre." />
    <link rel="icon" type="image/png" href="src/images/Logotipo%20-%20Varia%C3%A7%C3%B5es/fav-icon-y.jpeg">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <link rel="stylesheet" href="style.css?v=5.3">
</head>
<body>
    <?php include "header.php"; ?>

    <main class="container">
        <section id="produtos">
            <div class="section-title">
                <h2>Encontre sua coleção</h2>
                <p>Escolhas para acompanhar o seu movimento, o seu descanso e o seu cotidiano.</p>
            </div>
            <div class="products-grid-p">
                <article class="product-card-p">
                    <div class="product-slider">
                        <div class="slider-track">
                            <img class="product-visual-p active" src="src/images/mod-masc/1.png" alt="Camiseta Aratã Fitness - Cor 1" />
                            <img class="product-visual-p" src="src/images/mod-masc/2.png" alt="Camiseta Aratã Fitness - Cor 2" />
                            <img class="product-visual-p" src="src/images/mod-masc/3.png" alt="Camiseta Aratã Fitness - Cor 3" />
                            <img class="product-visual-p" src="src/images/mod-masc/4.png" alt="Camiseta Aratã Fitness - Cor 4" />
                            <img class="product-visual-p" src="src/images/mod-masc/5.png" alt="Camiseta Aratã Fitness - Cor 5" />
                        </div>
                        <button type="button" class="slider-btn prev" aria-label="Produto anterior" onclick="moveSlide(this, -1)">&lt;</button>
                        <button type="button" class="slider-btn next" aria-label="Próximo produto" onclick="moveSlide(this, 1)">&gt;</button>
                        <div class="slider-dots">
                            <span class="dot active" onclick="goToSlide(this, 0)"></span>
                            <span class="dot" onclick="goToSlide(this, 1)"></span>
                            <span class="dot" onclick="goToSlide(this, 2)"></span>
                            <span class="dot" onclick="goToSlide(this, 3)"></span>
                            <span class="dot" onclick="goToSlide(this, 4)"></span>
                        </div>
                    </div>
                    <h3>Camiseta Masculina Gola Careca Aratã – Modelagem Slim</h3>
                    <details class="product-description">
                        <summary>Ver descrição</summary>
                        <p>
                            Elegância, conforto e excelência em cada detalhe. Confeccionada em Malha Mid Fio 40, proporciona toque extremamente macio, excelente caimento e acabamento superior. Sua modelagem Slim foi desenvolvida para oferecer conforto e liberdade de movimentos, mantendo uma aparência sofisticada e atemporal.
                            Sua construção utiliza LINHA MISTA de alta qualidade, desenvolvida para minimizar os EFEITOS ALÉRGICOS, aliada ao travamento estratégico das costuras (Junção das costuras da galoneira com arremate na Reta), proporcionando maior resistência, segurança e durabilidade.
                            Um produto desenvolvido para quem valoriza qualidade premium, conforto, tecnologia e precisão em cada detalhe.
                        </p>
                    </details>
                    <div class="purchase-dropdown">
                        <button type="button" class="button button-primary purchase-toggle" aria-expanded="false" aria-controls="purchase-masculino" aria-label="Comprar camisetas masculinas">
                            Comprar <span class="purchase-chevron" aria-hidden="true"></span>
                        </button>
                        <div class="purchase-panel" id="purchase-masculino" inert>
                            <div class="purchase-panel-inner">
                                <ul class="purchase-options" aria-label="Opções de compra masculinas">
                                    <?php foreach ($purchaseOptions['masculino'] as $label => $option): ?>
                                        <li>
                                            <?php if ($option['url'] !== ''): ?>
                                                <a href="<?= htmlspecialchars($option['url'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer">
                                                    <span class="purchase-label"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></span>
                                                    <span class="purchase-price">
                                                        <strong>R$ <?= number_format($option['price'] / 100, 2, ',', '.') ?></strong>
                                                        <small>R$ <?= number_format($option['price'] / (100 * $option['quantity']), 2, ',', '.') ?> por camiseta</small>
                                                    </span>
                                                </a>
                                            <?php else: ?>
                                                <button type="button" disabled><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?> <small>Link em breve</small></button>
                                            <?php endif; ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                                
                            </div>
                        </div>
                    </div>
                </article>

                <article class="product-card-p">
                    <div class="product-slider">
                        <div class="slider-track">
                            <img class="product-visual-p active" src="src/images/mod-fem/1.jpg" alt="Conjunto feminino Aratã - Cor 1" />
                            <img class="product-visual-p" src="src/images/mod-fem/2.jpg" alt="Conjunto feminino Aratã - Cor 2" />
                            <img class="product-visual-p" src="src/images/mod-fem/3.jpg" alt="Conjunto feminino Aratã - Cor 3" />
                            <img class="product-visual-p" src="src/images/mod-fem/4.jpg" alt="Conjunto feminino Aratã - Cor 4" />
                        </div>
                        <button type="button" class="slider-btn prev" aria-label="Produto anterior" onclick="moveSlide(this, -1)">&lt;</button>
                        <button type="button" class="slider-btn next" aria-label="Próximo produto" onclick="moveSlide(this, 1)">&gt;</button>
                        <div class="slider-dots">
                            <span class="dot active" onclick="goToSlide(this, 0)"></span>
                            <span class="dot" onclick="goToSlide(this, 1)"></span>
                            <span class="dot" onclick="goToSlide(this, 2)"></span>
                            <span class="dot" onclick="goToSlide(this, 3)"></span>
                        </div>
                    </div>
                    <h3>Camiseta Feminina Gola Careca Aratã – Modelagem Baby Look</h3>
                    <details class="product-description">
                        <summary>Ver descrição</summary>
                        <p>
                            Elegância, conforto e excelência em cada detalhe. Confeccionada em Malha Mid Fio 40, oferece toque extremamente macio, caimento sofisticado e acabamento superior. Sua modelagem baby look valoriza a silhueta feminina, proporcionando um ajuste confortável e elegante.
                            Sua construção utiliza LINHA MISTA de alta qualidade, desenvolvida para minimizar os EFEITOS ALÉRGICOS, aliada ao travamento estratégico das costuras (Junção das costuras da galoneira com arremate na Reta), proporcionando maior resistência, segurança e durabilidade.
                            Um produto desenvolvido para quem valoriza qualidade premium, conforto, tecnologia e precisão em cada detalhe.
                        </p>
                    </details>
                    <div class="purchase-dropdown">
                        <button type="button" class="button button-primary purchase-toggle" aria-expanded="false" aria-controls="purchase-feminino" aria-label="Comprar camisetas femininas">
                            Comprar <span class="purchase-chevron" aria-hidden="true"></span>
                        </button>
                        <div class="purchase-panel" id="purchase-feminino" inert>
                            <div class="purchase-panel-inner">
                                <ul class="purchase-options" aria-label="Opções de compra femininas">
                                    <?php foreach ($purchaseOptions['feminino'] as $label => $option): ?>
                                        <li>
                                            <?php if ($option['url'] !== ''): ?>
                                                <a href="<?= htmlspecialchars($option['url'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer">
                                                    <span class="purchase-label"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></span>
                                                    <span class="purchase-price">
                                                        <strong>R$ <?= number_format($option['price'] / 100, 2, ',', '.') ?></strong>
                                                        <small>R$ <?= number_format($option['price'] / (100 * $option['quantity']), 2, ',', '.') ?> por camiseta</small>
                                                    </span>
                                                </a>
                                            <?php else: ?>
                                                <button type="button" disabled><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?> <small>Link em breve</small></button>
                                            <?php endif; ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                               
                            </div>
                        </div>
                    </div>
                </article>
            </div>
        </section>

    </main>

    <?php include "footer.php"; ?>

    <script>
        const purchaseDropdowns = document.querySelectorAll('.purchase-dropdown');

        function setPurchaseExpanded(dropdown, expanded) {
            dropdown.querySelector('.purchase-toggle').setAttribute('aria-expanded', String(expanded));
            dropdown.querySelector('.purchase-panel').inert = !expanded;
            dropdown.classList.toggle('is-open', expanded);
        }

        purchaseDropdowns.forEach(dropdown => {
            const toggle = dropdown.querySelector('.purchase-toggle');
            toggle.addEventListener('click', () => {
                const expanded = toggle.getAttribute('aria-expanded') !== 'true';
                purchaseDropdowns.forEach(item => setPurchaseExpanded(item, item === dropdown && expanded));
            });

            dropdown.addEventListener('keydown', event => {
                if (event.key === 'Escape' && toggle.getAttribute('aria-expanded') === 'true') {
                    event.preventDefault();
                    setPurchaseExpanded(dropdown, false);
                    toggle.focus();
                }
            });

            dropdown.addEventListener('focusout', event => {
                if (!dropdown.contains(event.relatedTarget)) setPurchaseExpanded(dropdown, false);
            });
        });

        document.addEventListener('click', event => {
            purchaseDropdowns.forEach(dropdown => {
                if (!dropdown.contains(event.target)) setPurchaseExpanded(dropdown, false);
            });
        });

        const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

        document.querySelectorAll('.product-description').forEach(details => {
            const summary = details.querySelector('summary');
            let animation = null;
            let expanded = details.open;

            function finishTransition() {
                if (animation) animation.cancel();
                animation = null;
                details.open = expanded;
                details.classList.remove('is-animating');
            }

            summary.addEventListener('click', event => {
                if (reducedMotion.matches || typeof details.animate !== 'function') return;
                event.preventDefault();

                const startHeight = details.getBoundingClientRect().height;
                expanded = animation ? !expanded : !details.open;
                if (animation) animation.cancel();

                // Measure the native end state, keeping content visible while closing.
                details.open = expanded;
                const endHeight = details.getBoundingClientRect().height;
                details.open = true;
                details.classList.add('is-animating');

                animation = details.animate(
                    [{ height: `${startHeight}px` }, { height: `${endHeight}px` }],
                    { duration: 320, easing: 'cubic-bezier(0.22, 1, 0.36, 1)' }
                );
                animation.onfinish = finishTransition;
            });

            reducedMotion.addEventListener('change', () => {
                if (reducedMotion.matches && animation) finishTransition();
            });
        });

        function updateSlider(slider, newIndex) {
            const images = slider.querySelectorAll('.product-visual-p');
            const dots = slider.querySelectorAll('.dot');

            if (newIndex >= images.length) newIndex = 0;
            if (newIndex < 0) newIndex = images.length - 1;

            images.forEach(img => img.classList.remove('active'));
            dots.forEach(dot => dot.classList.remove('active'));

            images[newIndex].classList.add('active');
            dots[newIndex].classList.add('active');
        }

        function moveSlide(btn, direction) {
            const slider = btn.closest('.product-slider');
            const images = slider.querySelectorAll('.product-visual-p');
            const currentIndex = Array.from(images).findIndex(img => img.classList.contains('active'));
            updateSlider(slider, currentIndex + direction);
        }

        function goToSlide(dot, index) {
            const slider = dot.closest('.product-slider');
            updateSlider(slider, index);
        }
    </script>
</body>
</html>