<?php

namespace app\controllers;
use App\Exceptions\exceptionCustom;
use League\Plates;

/** @var Plates\Template\Template $this */
$this->layout("master", [
    'title' => "Produtos",
    'description' => "Aqui você encontrará todos os produtos disponível para realizar uma troca, tendo total liberdade de escolha."
]);
if(session_status() === PHP_SESSION_NONE){
    session_start();
}
$produtos = ProductsController::getProdutos() ?? null;
$username = $_SESSION['username'];

?>
    <?php $this->start('body'); ?>

    <main class="container my-5">

        <section class="produtos-categoria">
            <h3>Todos os Itens</h3>
            <div class="produtos-lista">
                <?php if ($produtos): ?>
                    <?php foreach ($produtos as $produto): ?>
                        <div class="product" style="position: relative; width: 200px; padding: 10px; border: 1px solid #ddd; border-radius: 10px; transition: all 0.3s; text-align: center; overflow: visible; z-index: 1; background-color: #fff;">
                            <img src="<?= htmlspecialchars($produto['image']) ?>"  class="img-prod" alt="<?= htmlspecialchars($produto['nome']) ?>">
                            <p class="condition font-monospace"><?= htmlspecialchars($produto['fk_categoria']) ?></p>
                            <p class="product-name" style="font-weight: bold; margin-top: 10px;"><?= htmlspecialchars($produto['nome']) ?></p>
                            <p class="condition"><?= htmlspecialchars($produto['descricao']) ?></p>
                            <a href="#modalTrocarProduto"
                               class="btn-trocar"
                               data-bs-toggle="modal"
                               data-bs-target="#modalTrocarProduto"
                               data-id="<?= $produto['id'] ?>"
                               data-nome="<?= htmlspecialchars($produto['nome']) ?>"
                               data-descricao="<?= htmlspecialchars($produto['descricao']) ?>"
                               data-imagem="<?= htmlspecialchars($produto['image']) ?>"
                               data-categoria =<?= htmlspecialchars($produto['fk_categoria']) ?>
                            >
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