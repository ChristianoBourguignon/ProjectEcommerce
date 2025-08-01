<?php
use League\Plates;
/** @var Plates\Template\Template $this */
$this->layout("master", [
    'title' => "Sobre o Projeto",
    'description' => "Conheça mais sobre o desenvolvedor e o projeto ProjectEcommerce"
]); ?>

<?php $this->start('body'); ?>

<!-- Hero Section -->
<header class="full-height d-flex align-items-center justify-content-center text-center text-dark bg-gradient-primary">
    <div class="container text-white">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Avatar e Nome -->
                <div class="mb-4">
                    <div class="avatar-circle mx-auto mb-3">
                        <i class="bi bi-person-circle display-1"></i>
                    </div>
                    <h1 class="display-4 fw-bold mb-2 animate__animated animate__fadeIn">
                        Christiano Monteiro Bourguignon Leite
                    </h1>
                    <p class="lead animate__animated animate__fadeIn delay-1s">
                        Desenvolvedor Full Stack & Entusiasta de Tecnologia
                    </p>
                </div>

                <!-- Linha decorativa -->
                <hr class="w-25 mx-auto mb-4 border-white">

                <!-- Descrição -->
                <p class="lead mb-4 animate__animated animate__fadeIn delay-2s">
                    Desenvolvedor apaixonado por criar soluções inovadoras e aprender novas tecnologias.
                    Este projeto representa minha jornada de aprendizado e crescimento profissional.
                </p>
            </div>
        </div>
    </div>
</header>

<!-- Sobre o Projeto -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="display-5 fw-bold mb-4 text-primary">
                    <i class="bi bi-code-slash"></i> Sobre o ProjectEcommerce
                </h2>
                <p class="lead mb-5">
                    Um sistema completo de e-commerce desenvolvido em PHP puro, demonstrando 
                    habilidades em arquitetura MVC, banco de dados, APIs e testes automatizados.
                </p>
            </div>
        </div>

        <!-- Cards de Funcionalidades -->
        <div class="row g-4 mb-5">
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="bi bi-shield-check display-4 text-primary mb-3"></i>
                        <h5 class="card-title">Autenticação</h5>
                        <p class="card-text">Sistema de login/registro com CPF, cookies e controle de sessão</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="bi bi-box-seam display-4 text-success mb-3"></i>
                        <h5 class="card-title">Produtos</h5>
                        <p class="card-text">CRUD completo com upload de imagens e controle de estoque</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="bi bi-cart display-4 text-warning mb-3"></i>
                        <h5 class="card-title">Carrinho</h5>
                        <p class="card-text">Carrinho de compras com persistência e cálculos automáticos</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="bi bi-receipt display-4 text-info mb-3"></i>
                        <h5 class="card-title">Pedidos</h5>
                        <p class="card-text">Sistema completo de pedidos com frete e histórico</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Tecnologias Utilizadas -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="display-5 fw-bold mb-4 text-primary">
                    <i class="bi bi-tools"></i> Stack Tecnológica
                </h2>
                <p class="lead mb-5">
                    Tecnologias e ferramentas utilizadas no desenvolvimento deste projeto
                </p>
            </div>
        </div>

        <!-- Grid de Tecnologias -->
        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <div class="tech-card p-4 border rounded-3 shadow-sm">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-filetype-php display-6 text-primary me-3"></i>
                        <div>
                            <h5 class="mb-0">PHP 8.0+</h5>
                            <small class="text-muted">Backend Principal</small>
                        </div>
                    </div>
                    <p class="mb-0">Linguagem principal para desenvolvimento do backend, utilizando recursos modernos do PHP 8.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="tech-card p-4 border rounded-3 shadow-sm">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-database display-6 text-success me-3"></i>
                        <div>
                            <h5 class="mb-0">MySQL 5.7+</h5>
                            <small class="text-muted">Banco de Dados</small>
                        </div>
                    </div>
                    <p class="mb-0">Sistema de gerenciamento de banco de dados relacional para armazenamento de dados.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="tech-card p-4 border rounded-3 shadow-sm">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-layers display-6 text-info me-3"></i>
                        <div>
                            <h5 class="mb-0">League Plates</h5>
                            <small class="text-muted">Template Engine</small>
                        </div>
                    </div>
                    <p class="mb-0">Motor de templates para separação clara entre lógica e apresentação.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="tech-card p-4 border rounded-3 shadow-sm">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-shield-check display-6 text-warning me-3"></i>
                        <div>
                            <h5 class="mb-0">PDO</h5>
                            <small class="text-muted">Database Access</small>
                        </div>
                    </div>
                    <p class="mb-0">Interface para acesso seguro ao banco de dados com prepared statements.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="tech-card p-4 border rounded-3 shadow-sm">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-bug display-6 text-danger me-3"></i>
                        <div>
                            <h5 class="mb-0">PHPUnit</h5>
                            <small class="text-muted">Testes Automatizados</small>
                        </div>
                    </div>
                    <p class="mb-0">Framework de testes unitários com x testes implementados.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="tech-card p-4 border rounded-3 shadow-sm">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-search display-6 text-secondary me-3"></i>
                        <div>
                            <h5 class="mb-0">PHPStan</h5>
                            <small class="text-muted">Análise Estática</small>
                        </div>
                    </div>
                    <p class="mb-0">Ferramenta de análise estática para garantir qualidade do código.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Aprendizados -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="display-5 fw-bold mb-4 text-primary">
                    <i class="bi bi-lightbulb"></i> O Que Aprendi
                </h2>
                <p class="lead mb-5">
                    Este projeto foi uma jornada incrível de aprendizado e crescimento profissional
                </p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="learning-card p-4 bg-white rounded-3 shadow-sm">
                    <h5 class="text-primary mb-3">
                        <i class="bi bi-architecture"></i> Arquitetura MVC
                    </h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Separação clara de responsabilidades</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Organização modular do código</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Manutenibilidade e escalabilidade</li>
                    </ul>
                </div>
            </div>
            <div class="col-md-6">
                <div class="learning-card p-4 bg-white rounded-3 shadow-sm">
                    <h5 class="text-success mb-3">
                        <i class="bi bi-database"></i> Banco de Dados
                    </h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Design de esquemas relacionais</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Prepared statements para segurança</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Otimização de consultas</li>
                    </ul>
                </div>
            </div>
            <div class="col-md-6">
                <div class="learning-card p-4 bg-white rounded-3 shadow-sm">
                    <h5 class="text-warning mb-3">
                        <i class="bi bi-shield-check"></i> Segurança
                    </h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Sanitização de inputs</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Validação de arquivos</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Controle de sessão</li>
                    </ul>
                </div>
            </div>
            <div class="col-md-6">
                <div class="learning-card p-4 bg-white rounded-3 shadow-sm">
                    <h5 class="text-info mb-3">
                        <i class="bi bi-bug"></i> Testes
                    </h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>x testes automatizados</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Cobertura de código</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Qualidade e confiabilidade</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Estatísticas do Projeto -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="display-5 fw-bold mb-4 text-primary">
                    <i class="bi bi-graph-up"></i> Números do Projeto
                </h2>
            </div>
        </div>

        <div class="row g-4 text-center">
            <div class="col-md-3">
                <div class="stat-card p-4">
                    <i class="bi bi-file-code display-4 text-primary mb-3"></i>
                    <h3 class="fw-bold text-primary">7</h3>
                    <p class="mb-0">Controllers</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card p-4">
                    <i class="bi bi-bug display-4 text-success mb-3"></i>
                    <h3 class="fw-bold text-success">x</h3>
                    <p class="mb-0">Testes</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card p-4">
                    <i class="bi bi-exclamation-triangle display-4 text-warning mb-3"></i>
                    <h3 class="fw-bold text-warning">9</h3>
                    <p class="mb-0">Exceções Customizadas</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card p-4">
                    <i class="bi bi-database display-4 text-info mb-3"></i>
                    <h3 class="fw-bold text-info">6</h3>
                    <p class="mb-0">Tabelas no BD</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contato -->
<section class="py-5 bg-primary text-white">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="display-5 fw-bold mb-4">
                    <i class="bi bi-envelope"></i> Entre em Contato
                </h2>
                <p class="lead mb-4">
                    Interessado em colaborar ou tem alguma dúvida sobre o projeto?
                </p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="https://github.com/ChristianoBourguignon/ProjectEcommerce" class="btn btn-outline-light btn-lg">
                        <i class="bi bi-github me-2"></i>GitHub
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<style>

</style>

<?php $this->stop(); ?> 