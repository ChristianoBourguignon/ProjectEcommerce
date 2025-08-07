<?php

namespace app\views;
use App\controllers\CuponsController;
use League\Plates\Engine;

/** @var Engine $this */

$this->layout("master", [
    'title' => "Gerenciar Cupons",
    'description' => "Visualize e crie cupons com regras específicas e validade."
]);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$coupons = CuponsController::obterCupons();
?>

<?php $this->start('body'); ?>

<main class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary"><i class="bi bi-ticket-perforated-fill me-2"></i>Cupons de Desconto</h2>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createCouponModal">
            <i class="bi bi-plus-lg me-1"></i> Novo Cupom
        </button>
    </div>

    <!-- Lista de Cupons -->
    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead class="table-light">
            <tr>
                <th>Código</th>
                <th>Desconto (%)</th>
                <th>Desconto (R$)</th>
                <th>Valor Mínimo</th>
                <th>Criado em</th>
                <th>Validade</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($coupons as $c): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($c['code']) ?></strong></td>
                    <td><?= htmlspecialchars((string)$c['discount_percent']) ?>%</td>
                    <td>R$ <?= number_format($c['discount_value'], 2, ',', '.') ?></td>
                    <td>R$ <?= number_format($c['min_cart_value'], 2, ',', '.') ?></td>
                    <td><?= date('d/m/Y H:i', (int)strtotime($c['created_at'])) ?></td>
                    <td><?= date('d/m/Y', (int)strtotime($c['expires_at'])) ?></td>
                    <td>
                        <?php if ($c['active']): ?>
                            <span class="badge bg-success">Ativo</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Inativo</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

<!-- Modal para Criar Cupom -->
<div class="modal fade" id="createCouponModal" tabindex="-1" aria-labelledby="createCouponModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="/salvarCupom" id="formCupom" method="POST" class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="createCouponModalLabel">
                    <i class="bi bi-ticket-detailed me-2"></i>Criar Novo Cupom
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>

            <div class="modal-body row g-3">
                <div class="col-md-6">
                    <label for="code" class="form-label">Código do Cupom</label>
                    <input type="text" id="code" name="code" class="form-control" required maxlength="20">
                </div>

                <div class="col-md-3">
                    <label for="discount_percent" class="form-label">Desconto (%)</label>
                    <input type="number" id="discount_percent" name="discount_percent" class="form-control" min="0" max="100" step="0.01">
                </div>

                <div class="col-md-3">
                    <label for="discount_value" class="form-label">Desconto (R$)</label>
                    <input type="number" id="discount_value" name="discount_value" class="form-control" min="0" step="0.01">
                </div>

                <div class="col-md-6">
                    <label for="min_cart_value" class="form-label">Valor Mínimo do Carrinho</label>
                    <input type="number" id="min_cart_value" name="min_cart_value" class="form-control" min="0" step="0.01" required>
                </div>

                <div class="col-md-6">
                    <label for="expires_at" class="form-label">Validade</label>
                    <input type="date" id="expires_at" name="expires_at" class="form-control" required>
                </div>

                <div class="col-12 form-check mt-3">
                    <input type="checkbox" id="active" name="active" class="form-check-input" checked>
                    <label for="active" class="form-check-label">Cupom Ativo</label>
                </div>
            </div>

            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Salvar Cupom
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    <?php require_once ("app/static/js/cuponsController.js") ?>
</script>
<?php $this->stop(); ?>