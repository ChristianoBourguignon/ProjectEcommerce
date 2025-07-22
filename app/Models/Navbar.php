<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
/** @var string $username */
$username = $_SESSION["username"] ?? NULL;
$isLoggedIn = !empty($username);
$logoSrc = "/App/Static/images/Logo.png";
?>
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top border-bottom-0">
    <div class="container-fluid">

        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center me-auto" href="/">
            <img src="<?= htmlspecialchars($logoSrc) ?>" alt="Projeto Ecommerce" class="navbar-logo img-fluid">
        </a>

        <!-- Botão hamburguer (mobile) -->
        <button
                class="navbar-toggler d-lg-none border-0"
                type="button"
                data-bs-toggle="offcanvas"
                data-bs-target="#offcanvasNavbar"
                aria-controls="offcanvasNavbar"
                aria-label="Abrir menu"
        >
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Itens centralizados (desktop) -->
        <div class="collapse navbar-collapse justify-content-center d-none d-lg-flex" id="navbarMain">
            <ul class="navbar-nav gap-lg-3">
                <li class="nav-item"><a class="nav-link" href="/">Início</a></li>
                <li class="nav-item"><a class="nav-link" href="/produtos">Produtos</a></li>
                <li class="nav-item"><a class="nav-link" href="/sobre">Sobre nós</a></li>
            </ul>
        </div>

        <!-- Perfil / Login (desktop) -->
        <?php if ($isLoggedIn): ?>
            <div class="dropdown d-none d-lg-block">
                <a
                        class="btn btn-outline-primary dropdown-toggle login-button"
                        href="#"
                        id="perfilDropdown"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                >
                    Olá, <?= htmlspecialchars($username) ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="perfilDropdown">
                    <li>
                        <a
                                class="dropdown-item"
                                href="#cadastroProdutosModal"
                                data-bs-toggle="modal"
                                data-bs-target="#cadastroProdutosModal"
                        >Cadastrar Produto</a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="/deslogar">Sair</a></li>
                </ul>
            </div>
        <?php else: ?>
            <a
                    class="btn btn-primary d-none d-lg-block login-button"
                    href="#perfilModal"
                    data-bs-toggle="modal"
                    data-bs-target="#perfilModal"
            >Entrar / Cadastrar</a>
        <?php endif; ?>

        <!-- Offcanvas (menu mobile) -->
        <div class="offcanvas offcanvas-end d-lg-none" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
            <div class="offcanvas-header border-bottom">
                <h5 class="offcanvas-title d-flex align-items-center gap-2" id="offcanvasNavbarLabel">
                    <img
                            src="<?= htmlspecialchars($logoSrc) ?>"
                            alt="Projeto Ecommerce"
                            class="offcanvas-logo"
                    >
                    Menu
                </h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Fechar"></button>
            </div>
            <div class="offcanvas-body">
                <?php if ($isLoggedIn): ?>
                    <div class="fw-bold text-center mb-3">
                        Olá, <?= htmlspecialchars($username) ?>
                    </div>
                    <div class="list-group">
                        <div class="list-group-item disabled small text-uppercase">Páginas</div>
                        <a class="list-group-item list-group-item-action" href="/">Início</a>
                        <a class="list-group-item list-group-item-action" href="/produtos">Produtos</a>
                        <a class="list-group-item list-group-item-action" href="/sobre">Sobre nós</a>

                        <div class="list-group-item disabled small text-uppercase mt-3">Minha Conta</div>
                        <a class="list-group-item list-group-item-action" href="/inventario">Meu Inventário</a>
                        <a class="list-group-item list-group-item-action" href="/trocas">Minhas Trocas</a>
                        <a
                                class="list-group-item list-group-item-action"
                                href="#cadastroProdutosModal"
                                data-bs-toggle="modal"
                                data-bs-target="#cadastroProdutosModal"
                        >Cadastrar Produto</a>

                        <a class="list-group-item list-group-item-action text-danger mt-3" href="/deslogar">Sair</a>
                    </div>
                <?php else: ?>
                    <div class="list-group">
                        <div class="list-group-item disabled small text-uppercase">Páginas</div>
                        <a class="list-group-item list-group-item-action" href="/">Início</a>
                        <a class="list-group-item list-group-item-action" href="/produtos">Produtos</a>
                        <a class="list-group-item list-group-item-action" href="/sobre">Sobre nós</a>
                        <a
                                class="btn btn-primary w-100 mt-3"
                                href="#perfilModal"
                                data-bs-toggle="modal"
                                data-bs-target="#perfilModal"
                        >Entrar / Cadastrar</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
