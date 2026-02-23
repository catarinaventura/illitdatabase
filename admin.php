<?php
include "db.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: index.php");
    exit;
}

/* --------------------------------------------------------------------
CARREGAR USERS, ADMINS, PRODUTOS E ENCOMENDAS
-------------------------------------------------------------------- */
$usersResult = $conn->query("SELECT * FROM users WHERE role='user' ORDER BY id DESC");
$adminsResult = $conn->query("SELECT * FROM users WHERE role='admin' ORDER BY id DESC");

$productsResult = $conn->query("
    SELECT * FROM products
    ORDER BY is_active DESC, id DESC
");

$ordersResult = $conn->query("
    SELECT o.id AS order_id, o.user_id, o.order_date, o.status, u.username
    FROM orders o
    JOIN users u ON o.user_id = u.id
    ORDER BY o.order_date DESC
");

$orderItemsStmt = $conn->prepare("
    SELECT oi.quantity, p.name
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
");

/* --------------------------------------------------------------------
APAGAR UTILIZADORES
-------------------------------------------------------------------- */
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $_SESSION['notification'] = "Utilizador apagado com sucesso!";
    $_SESSION['notification_type'] = "danger";

    header("Location: admin.php");
    exit;
}

/* --------------------------------------------------------------------
EDITAR UTILIZADORES
-------------------------------------------------------------------- */
if (isset($_POST['edit_id'])) {
    $id = intval($_POST['edit_id']);
    $username = trim($_POST['username'][$id]);
    $email = trim($_POST['email'][$id]);

    $stmt = $conn->prepare("UPDATE users SET username=?, email=? WHERE id=?");
    $stmt->bind_param("ssi", $username, $email, $id);
    if($stmt->execute()){
        $_SESSION['notification'] = "Informações do utilizador atualizadas com sucesso!";
        $_SESSION['notification_type'] = "success";
    } else {
        $_SESSION['notification'] = "Erro ao atualizar o utilizador.";
        $_SESSION['notification_type'] = "danger";
    }

    header("Location: admin.php");
    exit;
}

/* --------------------------------------------------------------------
REGISTO DE NOVOS ADMINS
-------------------------------------------------------------------- */
$addAdminSuccess = "";
$addAdminError = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["new_admin_username"])) {
    $activeAdminTab = true;

    $username = trim($_POST["new_admin_username"]);
    $email = trim($_POST["new_admin_email"]);
    $password = $_POST["new_admin_password"];
    $confirm = $_POST["new_admin_confirm"];

    if ($password !== $confirm) {
        $addAdminError = "Passwords não coincidem!";
    } else {
        // Verficar se o username ou email já existem
        $check = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $check->bind_param("ss", $username, $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $addAdminError = "Username ou email já existe!";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'admin')";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $username, $email, $hashed);
            $stmt->execute();

            $_SESSION['notification'] = "Novo administrador registado com sucesso!";
            $_SESSION['notification_type'] = "success";

            header("Location: admin.php");
            exit;
        }

        $check->close();
    }
}

/* --------------------------------------------------------------------
ESTADO DA ENCOMENDA
-------------------------------------------------------------------- */
if (isset($_POST['update_order_id'], $_POST['new_status'])) {

    $order_id = (int) $_POST['update_order_id'];
    $new_status = $_POST['new_status'];

    $current = $conn->prepare("SELECT status FROM orders WHERE id = ?");
    $current->bind_param("i", $order_id);
    $current->execute();
    $res = $current->get_result();
    $order = $res->fetch_assoc();
    $current->close();

    if (!$order) {
        header("Location: admin.php#orders");
        exit;
    }

    if ($order['status'] === "cancelled") {
        header("Location: admin.php#orders");
        exit;
    }

    if ($order['status'] === "completed") {
        header("Location: admin.php#orders");
        exit;
    }

    $update = $conn->prepare(
    "UPDATE orders SET status = ? WHERE id = ?"
    );
    $update->bind_param("si", $new_status, $order_id);
    if($update->execute()){
        $_SESSION['notification'] = "Estado da encomenda atualizado com sucesso!";
        $_SESSION['notification_type'] = "success";
    } else {
        $_SESSION['notification'] = "Erro ao atualizar o estado da encomenda.";
        $_SESSION['notification_type'] = "danger";
    }
    $update->close();

    header("Location: admin.php#orders");
    exit;

}

/* --------------------------------------------------------------------
ADICIONAR E EDITAR PRODUTOS
-------------------------------------------------------------------- */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["product_action"])) {

    // ADICIONAR
    if ($_POST["product_action"] === "add_product") {

        $name = trim($_POST["name"]);
        $category = trim($_POST["category"]);
        $description = trim($_POST["description"]);
        $price = floatval($_POST["price"]);
        $stock = intval($_POST["stock"]);

        // IMAGE UPLOAD
        $imagePath = null;
        if (isset($_FILES["image"]) && $_FILES["image"]["error"] === 0) {
            $uploadDir = "uploads/products/";
            $ext = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
            $filename = uniqid("product_", true) . "." . $ext;
            $targetPath = $uploadDir . $filename;
            move_uploaded_file($_FILES["image"]["tmp_name"], $targetPath);
            $imagePath = $targetPath;
        }

        $stmt = $conn->prepare(
            "INSERT INTO products (name, category, description, image, price, stock) VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("sssdii", $name, $category, $description, $imagePath, $price, $stock);
        $stmt->execute();

        $_SESSION['notification'] = "Produto adicionado com sucesso!";
        $_SESSION['notification_type'] = "success";

        header("Location: admin.php#products");
        exit;

    }

    // EDITAR
    if (isset($_POST['edit_product_id'])) {
        $id = intval($_POST['edit_product_id']);
        $name = trim($_POST['name'][$id]);
        $category = trim($_POST['category'][$id]);
        $description = trim($_POST['description'][$id]);
        $price = floatval($_POST['price'][$id]);
        $stock = intval($_POST['stock'][$id]);

        $stmt = $conn->prepare("UPDATE products SET name=?, category=?, description=?, price=?, stock=? WHERE id=?");
        $stmt->bind_param("sssdii", $name, $category, $description, $price, $stock, $id);
        $stmt->execute();

        $_SESSION['notification'] = "Produto atualizado com sucesso!";
        $_SESSION['notification_type'] = "success";

        header("Location: admin.php#products");
        exit;

    }
}

/* --------------------------------------------------------------------
DESATIVAR PRODUTOS
-------------------------------------------------------------------- */
if (isset($_GET['delete_product_id'])) {
    $id = intval($_GET['delete_product_id']);

    $stmt = $conn->prepare("UPDATE products SET is_active = 0 WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $_SESSION['notification'] = "Produto desativado com sucesso!";
    $_SESSION['notification_type'] = "warning";

    header("Location: admin.php#products");
    exit;
}

/* --------------------------------------------------------------------
REATIVAR PRODUTOS
-------------------------------------------------------------------- */
if (isset($_GET['reactivate_product_id'])) {
    $id = intval($_GET['reactivate_product_id']);

    $stmt = $conn->prepare("UPDATE products SET is_active = 1 WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $_SESSION['notification'] = "Produto reativado com sucesso!";
    $_SESSION['notification_type'] = "success";

    header("Location: admin.php#products");
    exit;
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

    <title>ILLIT︱Admin</title>

    <script src="https://kit.fontawesome.com/e815cc27bb.js" crossorigin="anonymous"></script>
    <script src="admin_script.js"></script>

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
        <h1 class="mb-4 text-center">Painel de Administração</h1>
        <p>Olá <?= htmlspecialchars($_SESSION["username"]) ?>!</p>

    </section>

    <section class="container my-5">


        <!-- TABS - USERS E ADMINS -->

            <ul class="nav nav-tabs mb-4" id="adminTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab">Utilizadores</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="admins-tab" data-bs-toggle="tab" data-bs-target="#admins" type="button" role="tab">Administradores</button>
                </li>
                <li class="nav-item ms-auto">
                    <a href="logout.php" class="btn btn-danger logout-btn">Logout</a>
                </li>
            </ul>

            <div class="tab-content" id="adminTabsContent">


            <!-- USERS -->

                <div class="tab-pane fade show active" id="users" role="tabpanel">
                    <h3>Utilizadores Registados</h3>
                    <table class="table table-striped table-striped products-table-wrapper mb-4">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($user = $usersResult->fetch_assoc()): ?>
                            <tr id="user-<?= $user['id'] ?>">
                                <td><?= $user['id'] ?></td>
                                <td class="username"><?= htmlspecialchars($user['username']) ?></td>
                                <td class="email"><?= htmlspecialchars($user['email']) ?></td>
                                <td>
                                    <button class="btn btn-warning btn-sm edit-user-btn adicionar-btn" data-id="<?= $user['id'] ?>">Editar</button>
                                    <a href="admin.php?delete_id=<?= $user['id'] ?>" class="btn btn-danger" onclick="return confirm('Tens certeza que queres eliminar este utilizador?')">X</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            
                        </tbody>

                    </table>
                </div>


            <!-- ADMINS -->

                <div class="tab-pane fade" id="admins" role="tabpanel">
                    <h3>Administradores Registados</h3>
                    <table class="table table-striped table-striped products-table-wrapper mb-4">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Email</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($admin = $adminsResult->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $admin['id'] ?></td>
                                    <td><?= htmlspecialchars($admin['username']) ?></td>
                                    <td><?= htmlspecialchars($admin['email']) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>

                    <h5>Adicionar Novo Admin</h5>
                    <form method="POST" class="col-md-6 mb-3">
                        <div class="mb-3">
                            <input type="text" name="new_admin_username" class="form-control" placeholder="Username" required>
                        </div>
                        <div class="mb-3">
                            <input type="email" name="new_admin_email" class="form-control" placeholder="Email" required>
                        </div>
                        <div class="mb-3">
                            <input type="password" name="new_admin_password" class="form-control" placeholder="Password" required>
                        </div>
                        <div class="mb-3">
                            <input type="password" name="new_admin_confirm" class="form-control" placeholder="Confirm Password" required>
                        </div>
                        <button type="submit" class="btn adicionar-btn">Adicionar Admin</button>
                    </form>

                    <?php if ($addAdminError): ?>
                        <p class="text-danger"><?= $addAdminError ?></p>
                    <?php endif; ?>
                    <?php if ($addAdminSuccess): ?>
                        <p class="text-success"><?= $addAdminSuccess ?></p>
                    <?php endif; ?>
                </div>

        <!-- TABS - ENCOMENDAS E PRODUTOS -->

            <ul class="nav nav-tabs mb-4" id="orderTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders" type="button" role="tab">Encomendas</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="products-tab" data-bs-toggle="tab" data-bs-target="#products" type="button" role="tab">Produtos</button>
                </li>

            </ul>

            <!-- ENCOMENDAS -->
                <div class="tab-pane fade show active" id="orders" role="tabpanel">
                    <h3>Encomendas</h3>
                    <table class="table table-striped products-table-wrapper mb-4">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Produtos</th>
                                <th>Data</th>
                                <th>Estado</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($order = $ordersResult->fetch_assoc()): ?>
                            <tr>
                                <td><?= $order['order_id'] ?></td>
                                <td><?= htmlspecialchars($order['username']) ?></td>

                                <td>
                                <?php
                                    $orderItemsStmt->bind_param("i", $order['order_id']);
                                    $orderItemsStmt->execute();
                                    $itemsResult = $orderItemsStmt->get_result();

                                    while ($item = $itemsResult->fetch_assoc()) {
                                        echo htmlspecialchars($item['name']) . " x" . $item['quantity'] . "<br>";
                                    }
                                ?>
                                </td>

                                <td><?= $order['order_date'] ?></td>
                                <td><?= htmlspecialchars(ucfirst($order['status'] ?? '—')) ?></td>

                                <td>
                                    <?php
                                    
                                    $statusLabel = "";
                                    if ($order['status'] === "pending") {
                                        $statusLabel = "Pendente";
                                    } elseif ($order['status'] === "completed") {
                                        $statusLabel = "Entregue";
                                    }
                                    ?>

                                    <?php if ($order['status'] === "completed"): ?>
                                        <span class="text-success fw-bold">Entregue</span>

                                    <?php elseif ($order['status'] === "cancelled"): ?>
                                        <span class="text-danger fw-bold">Cancelada pelo utilizador</span>

                                    <?php else: ?>
                                        <form method="POST" class="d-flex gap-2">
                                            <input type="hidden" name="update_order_id" value="<?= $order['order_id'] ?>">
                                            <select name="new_status" class="form-select form-select-sm">
                                                <option value="pending" selected>Pendente</option>
                                                <option value="completed">Entregue</option>
                                            </select>
                                            <button type="submit" class="btn btn-success btn-sm adicionar-btn">
                                                Atualizar
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

            <!--PRODUTOS -->


            <div class="tab-content" id="orderTabsContent">
                <div class="tab-pane fade" id="products" role="tabpanel">
                    <h3>Produtos</h3>
                        <table class="table table-striped products-table-wrapper mb-4">
                            <thead>
                                <tr>
                                    <th>Imagem</th>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th>Categoria</th>
                                    <th>Descrição</th>
                                    <th>Preço</th>
                                    <th>Stock</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($product = $productsResult->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <?php if ($product["image"]): ?>
                                            <img src="<?= $product["image"] ?>" alt="" />
                                        <?php endif; ?>
                                        <?php if (!$product['is_active']): ?>
                                            <span class="badge bg-secondary ms-2">Inativo</span>
                                        <?php endif; ?>

                                    </td>
                                    <td><?= $product["id"] ?></td>
                                    <td><?= htmlspecialchars($product["name"]) ?></td>
                                    <td><?= $product["category"] ?></td>
                                    <td><?= htmlspecialchars($product["description"]) ?></td>
                                    <td>€<?= $product["price"] ?></td>
                                    <td><?= $product["stock"] ?></td>
                                    <td>
                                        <button class="btn btn-warning btn-sm edit-product-btn adicionar-btn"
                                            data-id="<?= $product['id'] ?>">
                                            Editar
                                        </button>

                                        <?php if ($product['is_active']): ?>
                                            <!-- DESATIVAR -->
                                            <a href="admin.php?delete_product_id=<?= $product['id'] ?>"
                                            class="btn btn-danger"
                                            onclick="return confirm('Desativar este produto?')">
                                            -
                                            </a>
                                        <?php else: ?>
                                            <!-- REATIVAR -->
                                            <a href="admin.php?reactivate_product_id=<?= $product['id'] ?>"
                                            class="btn btn-success"
                                            onclick="return confirm('Reativar este produto?')">
                                            +
                                            </a>
                                        <?php endif; ?>
                                    </td>

                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>

                    <h5 class="mt-4">Adicionar Produto</h5>

                    <form class="col-md-6 mb-3" method="POST" enctype="multipart/form-data">

                        <input type="hidden" name="product_action" value="add_product">
                        <div class="mb-3">
                            <input type="text" name="name" class="form-control" placeholder="Nome do produto" required>
                        </div>

                        <div class="mb-3">
                            <select name="category" class="form-control" required>
                                <option value="">Categoria</option>
                                <option value="albuns">Álbuns</option>
                                <option value="merch">Merchandise</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <textarea name="description" class="form-control" placeholder="Descrição"></textarea>
                        </div>
                        <div class="mb-3">
                            <input type="number" step="0.01" name="price" class="form-control" placeholder="Preço (€)" required>
                        </div>
                        <div class="mb-3">
                            <input type="number" name="stock" class="form-control" placeholder="Stock" required>
                        </div>

                        <div class="mb-3">
                            <input type="file" name="image" class="form-control" accept="image/*" required>
                        </div>

                        <button class="btn adicionar-btn">Adicionar Produto</button>

                    </form>
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
        // Clear it after showing once
        unset($_SESSION['notification']);
        unset($_SESSION['notification_type']);
    ?>
    <?php endif; ?>

</body>

</html>