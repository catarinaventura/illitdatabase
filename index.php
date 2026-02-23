<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Database de informações sobre o grupo de K-pop ILLIT">
    <meta name="author" content="Catarina Ventura">
    <meta name="keywords" content="ILLIT, K-pop, Girl Group, Database, informações, música, banda">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>ILLIT︱Home</title>

    <script src="https://kit.fontawesome.com/e815cc27bb.js" crossorigin="anonymous"></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/png" href="Logo-Cursor/illit-logo-tab.jpg">

</head>

<body>

<!-- CABEÇALHO -->

<?php include "header.php"; ?>

<!-- CONTEUDO PRINCIPAL -->

    <main>
        <section class="intro text-center my-5 section-titulo" id="bem-vindo">
            <h1>Bem-vindo à ILLIT Database</h1>
            <p>Fica a par de tudo o que são as ILLIT!</p>
        </section>

<!-- CAROUSEL -->

        <div id="carouselExampleFade" class="carousel slide carousel-fade" data-bs-ride="carousel">
        <div class="carousel-inner">

        <div class="carousel-item active">
         <img src="Grupo/illit-to-1.jpg" class="d-block w-100 page_img" alt="Cherish Concept 1">
        </div>

        <div class="carousel-item">
         <img src="Grupo/illit-to-2.jpg" class="d-block w-100 page_img" alt="Cherish Concept 1.2">
        </div>

        </div>

  <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleFade" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Previous</span>
  </button>

  <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleFade" data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Next</span>
  </button>
</div>

<!-- FEATURES -->

        <section class="features container" id="section-content">
            <div class="row text-center">

                <div class="col-md-4 mb-4">
                    <h3><a class="links" href="sobre.php" target="_self">Sobre as ILLIT</a></h3>
                    <p>Conhece um pouco da banda e dos integrantes.</p>
                </div>

                <div class="col-md-4 mb-4">
                    <h3><a class="links" href="albuns.php" target="_self">Discografia</a></h3>
                    <p>Explora todos os álbuns, singles e EPs lançados pelas ILLIT.</p>
                </div>

                <div class="col-md-4 mb-4">
                    <h3><a class="links" href="tour.php" target="_self">GLITTER DAY</a></h3>
                    <p>Informa-te relativamente ao "Fan Concert" mais recente do grupo.</p>
                </div>

            </div>
        </section>

    </main>

<!-- RODAPÉ -->

    <footer class="text-center py-4 rodapé">

        <div class="container-fluid">

        <ul class="navbar-nav" id="redes-sociais">

            <li class="nav-item icon-item">
            <a class="nav-link active" target="_blank" href="https://instagram.com/ILLIT_official"><i class="fa-brands fa-instagram"></i></a>
            </li>

            <li class="nav-item icon-item">
            <a class="nav-link active" target="_blank" href="https://x.com/ILLIT_official"><i class="fa-brands fa-x-twitter"></i></a>
            </li>

            <li class="nav-item icon-item">
            <a class="nav-link active" target="_blank" href="https://youtube.com/@ILLIT_official"><i class="fa-brands fa-youtube"></i></a>
            </li>

            <li class="nav-item icon-item">
            <a class="nav-link active" target="_blank" href="https://tiktok.com/@illit_official"><i class="fa-brands fa-tiktok"></i></a>
            </li>

        </ul>

        </div>

        <hr>

        <p><span class="highlights">&copy; Catarina Ventura</span> e <span class="highlights">ILLIT Database</span>. Todos os direitos reservados.</p>

    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    
    <?php if(isset($_SESSION['notification']) && $_SESSION['notification'] != ''): ?>
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1055">
        <div id="actionToast" class="toast align-items-center text-bg-<?= $_SESSION['notification_type'] ?> border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <?= htmlspecialchars($_SESSION['notification']) ?>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <script>
        const toastEl = document.getElementById('actionToast');
        if(toastEl) {
            const toast = new bootstrap.Toast(toastEl, { delay: 4000 });
            toast.show();
        }
    </script>

    <?php 
        unset($_SESSION['notification']);
        unset($_SESSION['notification_type']);
    ?>
    <?php endif; ?>
    
</body>

</html>