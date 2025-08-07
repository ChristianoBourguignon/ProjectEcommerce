<?php

namespace app\views;
use League\Plates\Engine;

/** @var Engine $this */

$this->layout("master", [
    'title' => "Página Não Encontrada",
    'description' => "Erro 404 - Página não encontrada"
]);

?>

<?php $this->start('body'); ?>

<section class="vh-100 d-flex align-items-center justify-content-center bg-light text-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Ícone grande de erro -->
                <div class="display-1 text-danger mb-4">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>

                <!-- Título -->
                <h1 class="display-4 fw-bold mb-3">Erro 404</h1>
                <p class="lead mb-4">Oops! A página que você tentou acessar não existe ou foi movida.</p>

                <!-- Botão para voltar -->
                <a href="/" class="btn btn-primary btn-lg">
                    <i class="bi bi-house-door me-2"></i>Voltar para a página inicial
                </a>
            </div>
        </div>
    </div>
</section>

<?php $this->stop(); ?>
