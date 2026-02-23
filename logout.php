<?php
session_start();

$_SESSION['notification'] = "Logout efetuado com sucesso!";
$_SESSION['notification_type'] = "warning";

foreach ($_SESSION as $key => $value) {
    if (!in_array($key, ['notification', 'notification_type'])) {
        unset($_SESSION[$key]);
    }
}

header("Location: index.php");
exit;
?>


