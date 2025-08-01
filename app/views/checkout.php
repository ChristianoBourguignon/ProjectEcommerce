<?php
$this->layout("master", [
    'title' => "Finalizar Compra",
    'description' => "Revise seu pedido e informe o endereço de entrega."
]);
?>
<?php $this->start('body'); ?>
<main class="container my-5">
    <h2 class="mb-4">Finalizar Compra</h2>
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Itens do Pedido</h5>
        </div>
        <div class="card-body" id="cartPreview">
            <!-- Itens do carrinho serão inseridos via JavaScript -->
        </div>
    </div>

    <form id="checkoutForm">
        <div class="row g-3">
            <div class="col-md-4">
                <label for="zipcode" class="form-label">CEP</label>
                <input type="text" class="form-control" id="zipcode" name="zipcode" required>
            </div>

            <div class="col-md-6">
                <label for="street" class="form-label">Rua</label>
                <input type="text" class="form-control" id="street" name="street" required>
            </div>

            <div class="col-md-3">
                <label for="number" class="form-label">Número</label>
                <input type="text" class="form-control" id="number" name="number" required>
            </div>

            <div class="col-md-3">
                <label for="complement" class="form-label">Complemento</label>
                <input type="text" class="form-control" id="complement" name="complement">
            </div>

            <div class="col-md-6">
                <label for="neighborhood" class="form-label">Bairro</label>
                <input type="text" class="form-control" id="neighborhood" name="neighborhood" required>
            </div>

            <div class="col-md-4">
                <label for="city" class="form-label">Cidade</label>
                <input type="text" class="form-control" id="city" name="city" required>
            </div>

            <div class="col-md-2">
                <label for="state" class="form-label">Estado (UF)</label>
                <input type="text" class="form-control" id="state" name="state" maxlength="2" required>
            </div>

            <div class="col-md-4">
                <label for="frete" class="form-label">Frete (R$)</label>
                <input type="text" step="0.01" class="form-control" readonly id="frete" name="frete" required>
            </div>
            <div class="col-md-4">
                <label for="total" class="form-label">Total (R$)</label>
                <input type="text" step="0.01" class="form-control" readonly id="total" name="total" required>
            </div>
        </div>

        <div class="text-end mt-4">
            <button type="submit" class="btn btn-success">Confirmar Pedido</button>
        </div>
    </form>
</main>

<script>
    <?php require_once("app/static/js/checkoutController.js"); ?>
</script>

<?php $this->stop(); ?>
