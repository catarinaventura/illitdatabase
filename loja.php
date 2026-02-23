<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Database de informações sobre o grupo de K-pop ILLIT">
    <meta name="author" content="Catarina Ventura">
    <meta name="keywords" content="ILLIT, K-pop, Girl Group, Database, informações, música, banda">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>ILLIT︱Merchandise</title>

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
        <section class="intro text-center my-5 section-titulo">
            <h1>Loja Online das ILLIT</h1>
            <p>Cópias físicas dos álbuns e Merchandise disponíveis para compra</p>
        </section>
    

<!-- PRODUTOS -->

        <section class="text-center my-4 filter-section">

            <div class="filter-controls">

                <button id="open-calc">Calcular Valores <i class="fa-regular fa-heart"></i></button>

                
                
                <select id="category-filter">
                    <option value="">Todas as Categorias</option>
                </select>
                
                

            </div>

        </section>

        <div id="store-container">
    
            <div id="product-list">
                <!-- Produtos serão carregados aqui -->
            </div>

            <div id="order-form">
            <h2>Calcular Valor Total</h2>
            

<!-- CALCULO DOS VALORES -->

                <form id="calc-form">

                    <label for="product">Escolha um produto:</label>
                    <select id="product" name="product">
                        <!-- Produtos serão carregados aqui -->
                    </select>

                    <label for="quantity">Quantidade:</label>
                    <input type="number" id="quantity" min="1" value="1">

                    <button type="button" id="calculate">Calcular Valor</button>

                </form>

                <div id="value-box">
                    <label for="total-value" id="total-value-text">Valor Total</label>

                    <div id="total-value">
                        <!-- Valor total será exibido aqui -->
                    </div>

                </div>

              </div>      

        </div>

        <hr class="my-5">

        <section class="intro text-center my-5 section-titulo">
            <h2>Alguma dúvida?</h2>
                <p class="mt-3">Não hesite e entre em <a class="links" href="contactos.php">contacto</a>!</p>
        </section>

        <div class="mb-5 text-center about-photos">
            <img src="Grupo/grupo_duvidas.jpg" alt="Foto de Grupo" class="img-fluid duvidas-photo">
        </div>

    </main>

    <div id="lightbox" class="lightbox" onclick="event.openLightbox">
        <div class="lightbox-content">
            <h3 id="lightbox-title"></h3>
            <img id="lightbox-image" src="" alt="">
            
            <button id="prev-btn" class="lightbox-nav">&lt;</button>
            <button id="next-btn" class="lightbox-nav">&gt;</button>
            <hr>
            <p id="lightbox-price"></p>
            <p id="lightbox-description"></p>

            
        </div>
    </div>

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

        <p>&copy; <span class="highlights">Catarina Ventura</span> e <span class="highlights">ILLIT Database</span>. Todos os direitos reservados.</p>

    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    
    <script>
    const userRole = "<?php echo isset($_SESSION['role']) ? $_SESSION['role'] : 'guest'; ?>";
    const isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
    </script>

    <script src="script.js"></script>

    <!-- Notificações -->
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1055">
            <div id="cart-toast" class="toast align-items-center text-bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body" id="cart-toast-message">
                        <!-- Mensagem -->
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>

</body>

</html>