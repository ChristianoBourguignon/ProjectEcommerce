<?php
use League\Plates;

/** @var Plates\Template\Template $this */
/** @var string $description */
/** @var string $title */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?=$this->e($description)?>">
    <title>Ecommerce<?=' - ' . $this->e($title)?></title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Font Montserrat -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;700&display=swap" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <!-- CSS GLOBALS -->
    <link href="/App/static/css/globals.css" rel="stylesheet">
</head>
<body>
<?php include_once dirname(__DIR__) . "/models/navbar.php"; ?>

<?= $this->section('body') ?>

<?php
require_once("app/models/modalMessages.php");
require_once("app/models/modalPerfil.php");

if (isset($username) && $username != "") {
    include_once("app/models/modalCadastrarProdutos.php");
    include_once("app/models/modalAlterarProduto.php");
}
?>

<script>
    <?php
    require_once("app/static/js/cartController.js");
    require_once("app/static/js/showModal.js");
    ?>
</script>
</body>
</html>


