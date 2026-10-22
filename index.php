<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Aratã by Khono's</title>
    <meta name="description" content="Coleções Aratã com acesso direto aos produtos no Mercado Livre." />
    <link rel="icon" type="image/png" href="src/images/Logotipo%20-%20Varia%C3%A7%C3%B5es/fav-icon-y.jpeg">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <link rel="stylesheet" href="style.css?v=4.2">
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
                </div>
                <button type="button" class="slider-btn prev" onclick="moveSlide(this, -1)">&lt;</button>
                <button type="button" class="slider-btn next" onclick="moveSlide(this, 1)">&gt;</button>
                <div class="slider-dots">
                    <span class="dot active" onclick="goToSlide(this, 0)"></span>
                    <span class="dot" onclick="goToSlide(this, 1)"></span>
                    <span class="dot" onclick="goToSlide(this, 2)"></span>
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
                <img class="hero-model" src="src/images/fem/conj-roxo-esc.png" alt="Modelo vestindo conjunto Aratã" />
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
        const slides = slider.querySelectorAll('.slider-slide');
        const dots = slider.querySelectorAll('.dot');
        
        if (newIndex >= slides.length) newIndex = 0;
        if (newIndex < 0) newIndex = slides.length - 1;

        slides.forEach(slide => slide.classList.remove('active'));
        dots.forEach(dot => dot.classList.remove('active'));

        slides[newIndex].classList.add('active');
        
        if (dots[newIndex]) {
            dots[newIndex].classList.add('active');
        }
      }

      function moveSlide(btn, direction) {
        const slider = btn.closest('.hero-slider, .product-slider, .slider');
        const slides = slider.querySelectorAll('.slider-slide');
        const currentIndex = Array.from(slides).findIndex(slide => slide.classList.contains('active'));
        updateSlider(slider, currentIndex + direction);
      }

      function goToSlide(dot, index) {
        const slider = dot.closest('.hero-slider, .product-slider, .slider');
        updateSlider(slider, index);
      }
    </script>
</body>
</html>