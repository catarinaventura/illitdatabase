<?php
include "db.php";

/* --------------------------------------------------------------------
CHECK DE SEGURANÇA
-------------------------------------------------------------------- */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    http_response_code(403);
    exit;
}

/* --------------------------------------------------------------------
INICIAR CARRINHO
-------------------------------------------------------------------- */
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

/* --------------------------------------------------------------------
DETETAR AJAX REQUESTS
-------------------------------------------------------------------- */
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

/* --------------------------------------------------------------------
DETETAR STOCK DO PRODUTO
-------------------------------------------------------------------- */
function getProductStock($conn, $product_id) {
    $stmt = $conn->prepare("SELECT stock FROM products WHERE id = ? AND is_active = 1");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $product = $res->fetch_assoc();
    return $product ? (int)$product['stock'] : 0;
}

$action = $_POST['action'] ?? '';
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
$response = [
    'notification' => '',
    'type' => 'info',
    'cart_total_items' => array_sum($_SESSION['cart'])
];

switch ($action) {

    case 'add':
        if ($product_id) {
            $stock = getProductStock($conn, $product_id);
            $currentQty = $_SESSION['cart'][$product_id] ?? 0;
            $newQty = $currentQty + $quantity;

            if ($newQty > $stock) {
                $_SESSION['cart'][$product_id] = $stock;
                $response['notification'] = "Só existem {$stock} unidades em stock! Quantidade ajustada.";
                $response['type'] = 'warning';
            } else {
                $_SESSION['cart'][$product_id] = $newQty;
                $response['notification'] = "Produto adicionado ao carrinho!";
                $response['type'] = 'success';
            }
            $response['cart_total_items'] = array_sum($_SESSION['cart']);
        }
        break;

    case 'update':
        if ($product_id) {
            $stock = getProductStock($conn, $product_id);

            if ($quantity <= 0) {
                unset($_SESSION['cart'][$product_id]);
                $response['notification'] = "Produto removido do carrinho.";
                $response['type'] = 'info';
            } elseif ($quantity > $stock) {
                $_SESSION['cart'][$product_id] = $stock;
                $response['notification'] = "Só existem {$stock} unidades em stock! Quantidade ajustada.";
                $response['type'] = 'warning';
            } else {
                $_SESSION['cart'][$product_id] = $quantity;
                $response['notification'] = "Quantidade atualizada!";
                $response['type'] = 'success';
            }
            $response['cart_total_items'] = array_sum($_SESSION['cart']);
        }
        break;

    case 'remove':
        if ($product_id && isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
            $response['notification'] = "Produto removido do carrinho.";
            $response['type'] = 'info';
            $response['cart_total_items'] = array_sum($_SESSION['cart']);
        }
        break;

    case 'checkout':
        if (!empty($_SESSION['cart'])) {
            $user_id = $_SESSION['user_id'];
            $conn->begin_transaction();

            foreach ($_SESSION['cart'] as $pid => $qty) {
                $stmt = $conn->prepare("SELECT stock FROM products WHERE id = ? AND is_active = 1 FOR UPDATE");
                $stmt->bind_param("i", $pid);
                $stmt->execute();
                $res = $stmt->get_result();
                $product = $res->fetch_assoc();

                if (!$product || $product['stock'] < $qty) {
                    $conn->rollback();
                    $_SESSION['notification'] = "Stock insuficiente para um ou mais produtos!";
                    $_SESSION['notification_type'] = 'danger';
                    header("Location: cart.php");
                    exit;
                }
            }

            $stmt = $conn->prepare("INSERT INTO orders (user_id) VALUES (?)");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $order_id = $stmt->insert_id;

            $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity) VALUES (?,?,?)");
            foreach ($_SESSION['cart'] as $pid => $qty) {
                $stmt->bind_param("iii", $order_id, $pid, $qty);
                $stmt->execute();
            }

            $conn->commit();
            unset($_SESSION['cart']);
            $_SESSION['notification'] = "Encomenda realizada com sucesso!";
            $_SESSION['notification_type'] = 'success';
            header("Location: cart.php");
            exit;
        }
        break;
}

if (in_array($action, ['add', 'update', 'remove'])) {
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode($response);
    } else {
        $_SESSION['notification'] = $response['notification'];
        $_SESSION['notification_type'] = $response['type'];
        header("Location: cart.php");
    }
    exit;
}
?>
