<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Aratã by Khono's</title>
    <meta name="description" content="Coleções Aratã com acesso direto aos produtos no Mercado Livre." />
    <link rel="icon" type="image/png" href="src/images/Logotipo%20-%20Varia%C3%A7%C3%B5es/isotipo-arata-positivo-azul-marinho.png">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <link rel="stylesheet" href="style.css?v=5.6">
</head>
<body>
    <?php include "header.php"; ?>
    
    <main class="container full-bleed-page">
        <section class="slider">
            <div class="hero-slider">
                <div class="slider-track">
                    <div class="slider-slide active slide-1"></div>
                    <div class="slider-slide slide-2"></div>
                    <div class="slider-slide slide-3"></div>
                    <div class="slider-slide slide-4"></div>
                </div>
                <button type="button" class="slider-btn prev" aria-label="Slide anterior" onclick="moveSlide(this, -1)">&lt;</button>
                <button type="button" class="slider-btn next" aria-label="Próximo slide" onclick="moveSlide(this, 1)">&gt;</button>
                <button type="button" class="slider-autoplay" aria-pressed="false">Pausar slides</button>
                <div class="slider-dots">
                    <span class="dot active" onclick="goToSlide(this, 0)"></span>
                    <span class="dot" onclick="goToSlide(this, 1)"></span>
                    <span class="dot" onclick="goToSlide(this, 2)"></span>
                    <span class="dot" onclick="goToSlide(this, 3)"></span>
                </div>
            </div>
        </section>

        <section class="hero home-intro">
            <div class="home-intro__content">
                <span class="eyebrow">Aratã by Khono's</span>
                <h1>Peças que acompanham o ritmo brasileiro, do descanso ao dia a dia.</h1>
                <p>Do ritmo da cidade aos dias de sol, as coleções Aratã unem conforto, presença e liberdade para viver o Brasil do seu jeito.</p>
                <div class="hero-actions">
                    <a class="button button-primary" href="#produtos">Conhecer coleções</a>
                </div>
                <div class="stats">
                    <div><strong>Conforto</strong><span>para todos os dias</span></div>
                    <div><strong>Leveza</strong><span>em cada escolha</span></div>
                    <div><strong>Estilo</strong><span>que acompanha você</span></div>
                </div>
            </div>
            <div class="hero-card home-intro__visual">
                <!-- Substitua about:blank por https://www.youtube.com/embed/ID_DO_VIDEO. -->
                <iframe class="home-intro__video" src="about:blank" title="Vídeo Aratã by Khono's"
                    loading="lazy" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
            </div>
        </section>

        <section id="produtos" class="home-collection" aria-labelledby="collection-title">
            <div class="home-collection__visual">
                <img class="product-visual" src="src/images/mod-masc/4cores.jpeg" alt="Camisetas Aratã Fitness em várias cores" />
            </div>
            <div class="home-collection__content">
                <span class="eyebrow">Fitness</span>
                <h2 id="collection-title">Encontre sua coleção</h2>
                <p>Escolhas para acompanhar o seu movimento, o seu descanso e o seu cotidiano.</p>
                <p>Liberdade para se movimentar com segurança e conforto, no seu ritmo.</p>
                <a class="button button-primary" href="products.php">Explorar coleção Fitness</a>
            </div>
        </section>

        <section class="cta home-contact" id="contato">
            <h2>Vista a sua melhor versão.</h2>
            <p>Fale com a Aratã e encontre peças que se conectam com o seu dia a dia.</p>
            <div class="home-contact__actions">
                <a class="button button-primary" href="contato.php">Enviar mensagem</a>
                <a class="button button-secondary" href="https://wa.me/message/5CULJK6YITB4O1">Falar pelo WhatsApp</a>
            </div>
        </section>
    </main>
    <?php include "whatsapp-button.php"; ?>
    <?php include "footer.php"; ?>

    <script>
      function updateSlider(slider, newIndex) {
        const slides = Array.from(slider.querySelectorAll('.slider-slide')).filter(slide => getComputedStyle(slide).display !== 'none');
        const dots = Array.from(slider.querySelectorAll('.dot')).filter(dot => getComputedStyle(dot).display !== 'none');
        
        if (newIndex >= slides.length) newIndex = 0;
        if (newIndex < 0) newIndex = slides.length - 1;

        slider.querySelectorAll('.slider-slide, .dot').forEach(item => item.classList.remove('active'));

        slides[newIndex].classList.add('active');
        
        if (dots[newIndex]) {
            dots[newIndex].classList.add('active');
        }
      }

      function moveSlide(btn, direction) {
        const slider = btn.closest('.hero-slider, .product-slider, .slider');
        const slides = Array.from(slider.querySelectorAll('.slider-slide')).filter(slide => getComputedStyle(slide).display !== 'none');
        const currentIndex = Array.from(slides).findIndex(slide => slide.classList.contains('active'));
        updateSlider(slider, currentIndex + direction);
      }

      function goToSlide(dot, index) {
        const slider = dot.closest('.hero-slider, .product-slider, .slider');
        updateSlider(slider, index);
      }

      window.matchMedia('(min-width: 641px)').addEventListener('change', () => {
        document.querySelectorAll('.hero-slider').forEach(slider => updateSlider(slider, 0));
      });

      document.querySelectorAll('.hero-slider').forEach(slider => {
        const toggle = slider.querySelector('.slider-autoplay');
        const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
        let paused = reducedMotion.matches;
        let timer;

        function syncAutoplay() {
          clearInterval(timer);
          toggle.setAttribute('aria-pressed', String(paused));
          toggle.textContent = paused ? 'Reproduzir slides' : 'Pausar slides';
          if (paused || document.hidden || slider.matches(':hover') || slider.contains(document.activeElement)) return;
          timer = setInterval(() => moveSlide(slider, 1), 5000);
        }

        toggle.addEventListener('click', () => {
          paused = !paused;
          syncAutoplay();
        });
        slider.addEventListener('mouseenter', syncAutoplay);
        slider.addEventListener('mouseleave', syncAutoplay);
        slider.addEventListener('focusin', syncAutoplay);
        slider.addEventListener('focusout', () => setTimeout(syncAutoplay, 0));
        document.addEventListener('visibilitychange', syncAutoplay);
        reducedMotion.addEventListener('change', () => {
          paused = reducedMotion.matches;
          syncAutoplay();
        });
        syncAutoplay();
      });
    </script>
</body>
</html>