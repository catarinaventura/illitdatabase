<?php
include "db.php";

/* --------------------------------------------------------------------
APAGAR CONTA
-------------------------------------------------------------------- */

if (isset($_POST['delete_account'])) {
    $user_id = $_SESSION['user_id'];

    $conn->begin_transaction();

    // Apagar itens das encomendas do utilizador
    $stmt = $conn->prepare("
        DELETE oi FROM order_items oi
        JOIN orders o ON oi.order_id = o.id
        WHERE o.user_id = ?
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    // Apagar encomendas do utilizador
    $stmt = $conn->prepare("DELETE FROM orders WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    // Apagar o utilizador
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    $conn->commit();

    // Logout automático
    $_SESSION['notification'] = "Conta apagada com sucesso!";
    $_SESSION['notification_type'] = "danger";

    unset($_SESSION['user_id']);

    header("Location: index.php");
    exit;
}

/* --------------------------------------------------------------------
ENCOMENDAS
-------------------------------------------------------------------- */
if (isset($_POST['delete_order_id'])) {
    $order_id = (int) $_POST['delete_order_id'];
    $user_id  = $_SESSION['user_id'];

    // Confirmar que a encomenda é do utilizador e está pendente
    $check = $conn->prepare("
        SELECT id 
        FROM orders 
        WHERE id = ? AND user_id = ? AND status = 'pending'
    ");
    $check->bind_param("ii", $order_id, $user_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows === 1) {

        $conn->begin_transaction();

        // Carregar os itens da encomenda
        $items = $conn->prepare("
            SELECT product_id, quantity 
            FROM order_items 
            WHERE order_id = ?
        ");
        $items->bind_param("i", $order_id);
        $items->execute();
        $items_result = $items->get_result();

        // Repor stock
        while ($item = $items_result->fetch_assoc()) {
            $updateStock = $conn->prepare("
                UPDATE products 
                SET stock = stock + ? 
                WHERE id = ?
            ");
            $updateStock->bind_param(
                "ii",
                $item['quantity'],
                $item['product_id']
            );
            $updateStock->execute();
        }

        // Marcar encomenda como cancelada
        $updateOrder = $conn->prepare("
            UPDATE orders 
            SET status = 'cancelled' 
            WHERE id = ?
        ");
        $updateOrder->bind_param("i", $order_id);
        $updateOrder->execute();

        $conn->commit();
    }

    $_SESSION['notification'] = "Encomenda cancelada com sucesso!";
    $_SESSION['notification_type'] = "warning";
    header("Location: profile.php");
    exit;
}

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];
$username = "";

$sql = "SELECT username FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $username = $row["username"];
}

// Carregar encomendas do utilizador
$orders = [];

$sql = "
    SELECT o.id, o.order_date, o.status
    FROM orders o
    WHERE o.user_id = ?
    ORDER BY o.order_date DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

function getOrderItems($conn, $order_id) {
    $items = [];

    $sql = "
        SELECT p.name, p.price, oi.quantity
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = ?
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }

    return $items;
}

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

<main class="container text-center">
    <section class="intro text-center my-5 section-titulo">
        <h1>Olá, <?= htmlspecialchars($username) ?>!</h1>
    </section>
    

    <a href="logout.php" class="btn btn-light logout-btn">Logout</a>

    <form method="POST" onsubmit="return confirm('Tens a certeza que queres apagar a tua conta?');">
        <button type="submit" name="delete_account" class="btn btn-danger mt-3">Apagar Conta</button>
    </form>

    <hr class="my-5">

    <h2>As minhas encomendas</h2>

    <?php if (empty($orders)): ?>
        <p class="mt-3">Ainda não fez nenhuma encomenda.</p>
        <p class="mt-3">Adicione produtos ao teu carrinho de compras <a class="links" href="loja.php">aqui</a>!
    <?php else: ?>

        <?php foreach ($orders as $order): ?>
            <div class="card my-4 shadow-sm">
                <div class="card-body">

                    <h5 class="card-title">
                        Encomenda #<?= $order['id'] ?>
                    </h5>

                    <p class="card-text">
                        <strong>Data:</strong>
                        <?= date("d/m/Y H:i", strtotime($order['order_date'])) ?>
                    </p>

                    <p class="card-text">
                        <strong>Estado:</strong>
                        <?php if ($order['status'] === 'pending'): ?>
                            <span class="badge bg-warning text-dark">Pendente</span>
                        <?php endif; ?>
                        <?php if ($order['status'] === 'completed'): ?>
                            <span class="badge bg-success">Concluída</span>
                        <?php endif; ?>
                        <?php if ($order['status'] === 'cancelled'): ?>
                            <span class="badge bg-secondary">Cancelada</span>
                        <?php endif; ?>

                    </p>

                    <?php if ($order['status'] === 'pending'): ?>
                        <form method="POST" class="mt-3"
                            onsubmit="return confirm('Tens a certeza que queres apagar esta encomenda?');">
                            <input type="hidden" name="delete_order_id" value="<?= $order['id'] ?>">
                            <button type="submit" class="btn btn-danger btn-sm adicionar-btn">
                                Cancelar encomenda
                            </button>
                        </form>
                    <?php endif; ?>


                    <hr>

                    <ul class="list-group list-group-flush">
                        <?php
                            $items = getOrderItems($conn, $order['id']);
                            foreach ($items as $item):
                        ?>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>
                                    <?= htmlspecialchars($item['name']) ?>
                                    × <?= $item['quantity'] ?>
                                </span>
                                <span>
                                    <?= number_format($item['price'] * $item['quantity'], 2) ?> €
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                </div>
            </div>
        <?php endforeach; ?>

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


