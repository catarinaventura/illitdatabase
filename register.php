<?php
include "db.php";

$error = "";
$success = "";

$adminCheck = $conn->query("SELECT id FROM users WHERE role = 'admin' LIMIT 1");
$adminExists = $adminCheck->num_rows > 0;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"]);
    $email    = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm  = $_POST["confirmPassword"];

    if ($password !== $confirm) {
        $error = "As passwords não coincidem!";

    } else {

    // Veirifcar se o username ou email já existem
    $check = $conn->prepare(
        "SELECT id FROM users WHERE username = ? OR email = ?"
    );
    $check->bind_param("ss", $username, $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $error = "Username ou e-mail já registado!";
    } else {

        if ($adminExists) {
            $role = "user";
        } else {
            $role = $_POST["role"] ?? "user";
        }

        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (username, email, password, role)
                VALUES (?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $username, $email, $hashed, $role);

        if ($stmt->execute()) {
            header("Location: login.php");
            exit;
        } else {
            $error = "Erro ao criar conta!";
        }
    }
    }
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

    <title>ILLIT︱Registo</title>

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
        <h1>Cria uma conta!</h1>
    </section>

    <section class="contact-section my-5">
        <div class="container contactos-container">
            <div class="row justify-content-center">
                <div class="col-md-6 mb-4">

                    <form method="POST">

                        <div class="mb-3">
                            <label class="form-label">Nome de Utilizador:</label>
                            <input type="text" class="form-control" name="username" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">E-mail:</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password:</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Confirmar Password:</label>
                            <input type="password" class="form-control" name="confirmPassword" required>
                        </div>

                    <?php if (!$adminExists): ?>

                        <div class="mb-3">
                            <label class="form-label">Tipo de Utilizador:</label>
                    
                            <select class="form-select" id="tipo-user" name="role" required>
                                <option value="user">Utilizador</option>
                                <option value="admin">Administrador</option>
                            </select>
                        </div>

                    <?php endif; ?>

                        <div class="text-center">
                            <button type="submit" class="btn btn-light registar-btn">
                                Registar
                            </button>
                        </div>

                        <?php if ($error): ?>
                            <div class="alert alert-danger mt-3 text-center" role="alert">
                                <?= htmlspecialchars($error) ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($success): ?>
                            <p class="text-success mt-3 text-center"><?= $success ?></p>
                        <?php endif; ?>

                    </form>

                </div>

                <div class="col-6 text-center mt-3" id="caixa-login">
                        <p>Já tens conta? Faz<a class="links" href="login.php"> Login!</a>
                    </div>
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
    
</body>

</html>

