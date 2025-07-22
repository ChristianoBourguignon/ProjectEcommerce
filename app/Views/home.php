<?php
use League\Plates;
/** @var Plates\Template\Template $this */
$this->layout("master", [
    'title' => "Projeto Ecommerce",
    'description' => "Criando um projeto de ecommerce para aumentar meu portfolio"
]); ?>

<?php $this->start('body'); ?>

<header class="full-height d-flex align-items-center justify-content-center text-center text-dark bg-light">
    <div class="container">
        <!-- Título -->
        <h2 class="fw-bold mb-3 animate__animated animate__fadeIn">
            Faça o seu teste!
        </h2>

        <!-- Linha decorativa -->
        <hr class="w-25 mx-auto mb-4 border-primary">

        <!-- Headline -->
        <h1 class="display-4 fw-bold mb-3 animate__animated animate__fadeInUp">
            O <span class="text-primary">Projeto Ecommerce</span> é o meu novo projeto
        </h1>

        <!-- Descrição -->
        <p class="lead mb-4 text-muted animate__animated animate__fadeIn delay-1s">
            Aplicando todo o meu conhecimento, até o certo momento nesse projeto!
        </p>

        <!-- Botão -->
        <a href="#itens" class="btn btn-primary btn-lg shadow animate__animated animate__pulse delay-2s">
            <i class="bi bi-box-seam"></i> Ver Itens
        </a>
    </div>
</header>



<?php $this->stop(); ?>
