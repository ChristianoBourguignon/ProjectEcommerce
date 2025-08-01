<?php
?>
<!-- Modal de Cadastro de Produto -->
<div class="modal fade" id="alterarProdutoModal" tabindex="-1" aria-labelledby="alterarProdutoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-4 shadow">
            <div class="modal-header">
                <h5 class="modal-title" id="alterarProdutoModalLabel">Alterar Produto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form action="/alterarProduto" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <input type="hidden" class="form-control" readonly id="id" name="id" required/>
                        <label for="nome" class="form-label">Nome do Produto</label>
                        <input type="text" class="form-control" id="nome" name="nome" required>
                    </div>
                    <div class="mb-3">
                        <label for="preco" class="form-label">Preço</label>
                        <input type="number" class="form-control" id="preco" name="preco" required>
                    </div>
                    <div class="mb-3">
                        <label for="estoque" class="form-label">Estoque</label>
                        <input type="number" class="form-control" id="estoque" name="estoque" required>
                    </div>
                    <div class="mb-3">
                        <label for="imagem" class="form-label">Imagem do Produto</label>
                        <input class="form-control" type="file" id="imagem" name="imagem" accept="image/*" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Alterar</button>
                    <button type="reset" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const modal = document.getElementById('alterarProdutoModal');
            modal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;

                const id = button.getAttribute('data-id');
                const nome = button.getAttribute('data-nome');
                const preco = button.getAttribute('data-preco');
                const estoque = button.getAttribute('data-estoque');
                modal.querySelector('#id').value = id;
                modal.querySelector('#nome').value = nome;
                modal.querySelector('#preco').value = preco;
                modal.querySelector('#estoque').value = estoque;
            });
        });
    </script>
