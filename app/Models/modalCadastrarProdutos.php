<!-- Modal de Cadastro de Produto -->
<div class="modal fade" id="cadastroProdutosModal" tabindex="-1" aria-labelledby="cadastroProdutosModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-4 shadow">
            <div class="modal-header">
                <h5 class="modal-title" id="cadastroProdutosModalLabel">Cadastrar Produto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form action="/criarProduto" id="formCriarProduto" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome do Produto</label>
                        <input type="text" class="form-control" id="nome" name="nome" required>
                    </div>
                    <div class="mb-3">
                        <label for="preco" class="form-label">Preço</label>
                        <input type="text" class="form-control" id="preco" name="preco" step="any" required>
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
                    <button type="submit" class="btn btn-success">Cadastrar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $('#preco').on('input', function () {
            let val = $(this).val();

            // Remove vírgulas e tudo que não for número ou ponto
            val = val.replace(/[^0-9.]/g, '');

            // Permite apenas um ponto
            let parts = val.split('.');
            if (parts.length > 2) {
                val = parts[0] + '.' + parts[1];
            }

            $(this).val(val);
        });

        // Validação opcional ao enviar o formulário
        $('#formCriarProduto').on('submit', function (e) {
            let preco = $('#preco').val();
            if (!/^\d+(\.\d{1,2})?$/.test(preco)) {
                e.preventDefault();
                alert('Por favor, insira um preço válido. Ex: 199.99');
            }
        });
    });
</script>