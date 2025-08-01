<?php

namespace app\controllers;
use League\Plates;

$this->layout("master", [
    'title' => "Cadastrar Cupom",
    'description' => "Crie cupons de desconto para os clientes."
]);

if(session_status() === PHP_SESSION_NONE){
    session_start();
}

?>

<?php $this->start('body'); ?>

<main class="container my-5">
    <h2>Criar Novo Cupom</h2>

    <form action="/salvarCupom" method="POST" class="w-50">
        <div class="mb-3">
            <label for="code" class="form-label">Código do Cupom</label>
            <input type="text" id="code" name="code" class="form-control" required maxlength="20">
        </div>

        <div class="mb-3">
            <label for="discount_percent" class="form-label">Desconto (%)</label>
            <input type="number" id="discount_percent" name="discount_percent" class="form-control" min="0" max="100" step="0.01" required>
        </div>

        <div class="mb-3">
            <label for="discount_value" class="form-label">Valor fixo de desconto (opcional)</label>
            <input type="number" id="discount_value" name="discount_value" class="form-control" min="0" step="0.01">
            <small class="form-text text-muted">Preencha ou o desconto percentual será aplicado.</small>
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" id="active" name="active" class="form-check-input" checked>
            <label for="active" class="form-check-label">Ativo</label>
        </div>

        <div class="mb-3">
            <label for="expires_at" class="form-label">Data de Expiração</label>
            <input type="date" id="expires_at" name="expires_at" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Salvar Cupom</button>
    </form>
</main>

<?php $this->stop(); ?>
