<?php

namespace app\controllers;
use League\Plates;

/** @var Plates\Template\Template $this */
$this->layout("master", [
    'title' => "Produtos",
    'description' => "Aqui você encontrará todos os produtos disponível."
]);
if(session_status() === PHP_SESSION_NONE){
    session_start();
}
$produtos = ProductsController::getProdutos() ?? NULL;
$username = $_SESSION['username'] ?? NULL;

?>
    <?php $this->start('body');?>

    <main class="container my-5">

        <section class="produtos-categoria">
            <h3>Todos os Itens</h3>
            <div class="d-flex">
                <?php if ($produtos): ?>
                    <?php foreach ($produtos as $produto): ?>
                        <div class="product m-1">
                            <img src="<?= htmlspecialchars($produto['image']) ?>"  class="img-prod" alt="<?= htmlspecialchars($produto['name']) ?>">
                            <p class="product-name" style="font-weight: bold; margin-top: 10px;"><?= htmlspecialchars($produto['name']) ?></p>
                            <p class="condition font-monospace"><?= "R$" . number_format($produto['price'],2) ?></p>
                            <?php if($username): ?>
                            <div class="dropdown dropdown-btn" style="position: absolute; top: 10px; left: 10px;">
                                <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    :
                                </button>
                                <ul class="dropdown-menu" style="position: absolute;">
                                    <a href="#alterarProdutoModal"
                                       class="dropdown-item btn-editar-produto"
                                       data-bs-toggle="modal"
                                       data-bs-target="#alterarProdutoModal"
                                       data-id="<?= $produto['id'] ?>"
                                       data-nome="<?= htmlspecialchars($produto['name']) ?>"
                                       data-preco="<?= htmlspecialchars($produto['price']) ?>"
                                       data-estoque="<?= htmlspecialchars($produto['quantity']) ?>"
                                    >
                                        Alterar
                                    </a>
                                    <li>
                                        <form action="/excluirProduto" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este produto?')" style="margin: 0;">
                                            <input type="hidden" name="product_id" value="<?= $produto['id'] ?>">
                                            <button type="submit" class="dropdown-item text-danger" style="background: none; border: none;">Excluir</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                            <?php endif; ?>
                            <a href="#" class="btn btn-primary w-100 mt-3">
                                Comprar
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="d-flex flex-column justify-content-center align-items-center text-center">
                        <p>Nenhum produto cadastrado ainda.</p>
                        <p>Tente criar um aqui: </p>
                        <a
                            <?php if(isset($username)): ?>
                                href="#cadastroProdutosModal"
                                data-bs-toggle="modal"
                                data-bs-target="#cadastroProdutosModal"
                            <?php else: ?>
                                href="#perfilModal"
                                data-bs-toggle="modal"
                                data-bs-target="#perfilModal"
                            <?php endif ?>
                            class="btn btn-primary btn-lg shadow animate__animated animate__pulse delay-2s">
                            <i class="bi bi-box-seam"></i> Criar Produto
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>
    <?php
require_once("app/models/modalPerfil.php");
require_once("app/models/modalCadastrarProdutos.php");
?>
<?php $this->stop();