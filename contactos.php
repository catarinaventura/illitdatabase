<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Database de informações sobre o grupo de K-pop ILLIT">
    <meta name="author" content="Catarina Ventura">
    <meta name="keywords" content="ILLIT, K-pop, Girl Group, Database, informações, música, banda">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>ILLIT︱Contacta-me</title>

    <script src="https://kit.fontawesome.com/e815cc27bb.js" crossorigin="anonymous"></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/png" href="Logo-Cursor/illit-logo-tab.jpg">

    <script type="text/javascript">

    function enviar(event) {
    if (!event.target.checkValidity()) {
        event.preventDefault();
        return;
    }

    event.preventDefault();
    alert("A mensagem foi enviada com sucesso! Obrigada");
}

    </script>

</head>

<body>

<!-- CABEÇALHO -->

<?php include "header.php"; ?>

<!-- CONTEUDO PRINCIPAL -->

    <main>
        <section class="intro text-center my-5 section-titulo">
            <h1>Contacta-me !!</h1>
            <p>Qualquer dúvida ou sugestão, fica à vontade para contactar-me!</p>

        </section>

        <section class="contact-section my-5">
  <div class="container contactos-container">

    <div class="row align-items-center justify-content-center">

      <div class="col-md-12 mb-4">
        <form onsubmit="enviar(event)">

          <div class="mb-3">
            <label for="name" class="form-label">Nome:</label>
            <input type="text" class="form-control" id="name" placeholder="Insira o seu nome" required>
          </div>

          <div class="mb-3">
            <label for="surname" class="form-label">Apelido:</label>
            <input type="text" class="form-control" id="surname" placeholder="Insira o seu apelido" required>
          </div>

          <div class="mb-3">
            <label for="birthday" class="form-label">Data de Nascimento:</label>
            <input type="date" class="form-control" id="birthday" required>
          </div>

          <div class="mb-3">
            <label for="email" class="form-label">Email:</label>
            <input type="email" class="form-control" id="email" placeholder="Insira o seu email" required>
          </div>

          <div class="mb-3">
            <label for="phone" class="form-label">Telemóvel (Opcional):</label>
            <input type="tel" class="form-control" id="phone" placeholder="Insira o seu numero de telemóvel" inputmode="numeric" pattern="[0-9]*" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
          </div>

          <div class="mb-3">
            <label for="message" class="form-label">Mensagem:</label>
            <textarea class="form-control" id="message" rows="5" placeholder="Escreva a sua mensagem aqui..." required></textarea>
          </div>

          <div class="text-center" id="enviar-mensagem">
            <button type="submit" class="btn btn-light" id="enviar-btn">Enviar</button>
          </div>

        </form>
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