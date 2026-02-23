<?php
include "db.php";

?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Database de informações sobre o grupo de K-pop ILLIT">
    <meta name="author" content="Catarina Ventura">
    <meta name="keywords" content="ILLIT, K-pop, Girl Group, Database, informações, música, banda">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>ILLIT︱Perfil</title>

    <script src="https://kit.fontawesome.com/e815cc27bb.js" crossorigin="anonymous"></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/png" href="Logo-Cursor/illit-logo-tab.jpg">

</head>

<body>

<!-- CABEÇALHO -->

<?php include "header.php"; ?>

<!-- CONTEUDO PRINCIPAL -->

<?php

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

$cart = $_SESSION['cart'] ?? [];
$total = 0;
?>

<main class="container text-center">
    <section class="intro text-center my-5 section-titulo">
    <h1>Carrinho</h1>
     <p>Reveja os produtos que adicionou ao carrinho antes de finalizar a sua encomenda!</p>
    </section>


    <?php if (empty($cart)): ?>
        <p>O teu carrinho está vazio. Começa a comprar <a class="links" href="loja.php">aqui</a>!</p>
    <?php else: ?>

        <table class="table align-middle order-table-wrapper mb-4">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Preço</th>
                    <th>Quantidade</th>
                    <th>Subtotal</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>

            <?php
            $ids = implode(',', array_keys($cart));
            $result = $conn->query("SELECT * FROM products WHERE id IN ($ids)");

            while ($product = $result->fetch_assoc()):
                $qty = $cart[$product['id']];
                $subtotal = $product['price'] * $qty;
                $total += $subtotal;
            ?>
                <tr>
                    <td><?= htmlspecialchars($product['name']) ?></td>
                    <td><?= number_format($product['price'], 2) ?> €</td>

                    <td>
                        <form method="POST" action="cart_action.php" class="d-flex gap-2">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                            <input type="number" name="quantity" value="<?= $qty ?>" min="1" class="form-control" style="width: 80px">
                            <button class="btn btn-sm btn-outline-primary adicionar-btn">Atualizar</button>
                        </form>
                    </td>

                    <td><?= number_format($subtotal, 2) ?> €</td>

                    <td>
                        <form method="POST" action="cart_action.php">
                            <input type="hidden" name="action" value="remove">
                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                            <button class="btn btn-danger btn-sm">X</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>

            </tbody>
        </table>

        <div class="text-end fw-bold fs-5">
            Total: <?= number_format($total, 2) ?> €
        </div>

        <form method="POST" action="cart_action.php" class="text-end mt-3">
            <input type="hidden" name="action" value="checkout">
            <button class="btn btn-success">Finalizar Encomenda</button>
        </form>

    <?php endif; ?>

    </main>

    <hr class="my-5">

    <section class="intro text-center my-5 section-titulo">
        <h2>Alguma dúvida?</h2>
            <p class="mt-3">Não hesite e entre em <a class="links" href="contactos.php">contacto</a>!</p>
    </section>

    <div class="mb-5 text-center about-photos">
        <img src="Grupo/grupo_duvidas.jpg" alt="Foto de Grupo" class="img-fluid duvidas-photo">
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

    <?php if(isset($_SESSION['notification']) && $_SESSION['notification'] != ''): ?>
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1055">
            <div id="cartToast" class="toast align-items-center text-bg-<?= $_SESSION['notification_type'] ?> border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <?= htmlspecialchars($_SESSION['notification']) ?>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>

        <script>
            var toastEl = document.getElementById('cartToast');
            var toast = new bootstrap.Toast(toastEl, { delay: 4000 });
            toast.show();
        </script>
    <?php 
        unset($_SESSION['notification']);
        unset($_SESSION['notification_type']);
    endif; ?>


</body>

</html>
