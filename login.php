<?php
include "db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"] ?? '';
    $password = $_POST["password"] ?? '';

    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user["password"])) {
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["role"] = $user["role"];
        $_SESSION["username"] = $user["username"];
        
        $_SESSION['notification'] = "Login efetuado com sucesso!";
        $_SESSION['notification_type'] = "success";

        if ($user["role"] === "admin") {
            header("Location: admin.php");
        } else {
            header("Location: profile.php");
        }
        exit;
    } else {
        $error = "Nome de utilizador ou password incorretos!";
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

    <title>ILLIT︱Login</title>

    <script src="https://kit.fontawesome.com/e815cc27bb.js" crossorigin="anonymous"></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/png" href="Logo-Cursor/illit-logo-tab.jpg">

</head>

<body>

<!-- CABEÇALHO -->

<?php include "header.php"; ?>

<!-- CONTEUDO PRINCIPAL -->

    <section class="intro text-center my-5 section-titulo">
            <h1>Login!</h1>
    </section>

    <section class="contact-section my-5">

            <div class="container contactos-container">

                <div class="row align-items-center justify-content-center">

                    <div class="col-md-6 mb-4">
                        <form method="POST">
                            
                        <div class="mb-3">
                            <label for="username" class="form-label">Nome de Utilizador:</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password:</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>

                        <?php if ($error): ?>
                            <div class="alert alert-danger text-center mb-3" role="alert">
                                <?= htmlspecialchars($error) ?>
                            </div>
                        <?php endif; ?>

                        <div class="text-center" id="enviar-mensagem">
                            <button type="submit" class="btn btn-light registar-btn">Login</button>
                        </div>

                        </form>
  
                    </div><br>

                    <div class="col-6 text-center mt-3" id="caixa-registo">
                        <p>Não tens conta? <a class="links" href="register.php">Regista-te!</a>
                    </div>

                </div>

            </div>

        </section>

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
