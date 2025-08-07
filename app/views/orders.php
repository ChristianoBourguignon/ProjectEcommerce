<?php

namespace app\controllers;
use App\exceptions\exceptionCustom;
use League\Plates;

$this->layout("master", [
    'title' => "Meus Pedidos",
    'description' => "Acompanhe e gerencie seus pedidos."
]);

if(session_status() === PHP_SESSION_NONE){
    session_start();
}
$orders = OrdersController::getPedidosPorUsuario($_SESSION['userid']) ?? [];
?>

<?php $this->start('body'); ?>

<main class="container my-5">
    <h2>Meus Pedidos</h2>

    <?php if(empty($orders)): ?>
        <p>Você não tem pedidos ainda.</p>
    <?php else: var_dump($orders);?>
        <form id="formPedidos" method="POST" action="/salvarStatusPedidos">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>ID Pedido</th>
                    <th>Data</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($orders as $order): ?>
                    <tr>
                        <td><?= htmlspecialchars($order['order_id']) ?></td>
                        <td><?= htmlspecialchars(date('d/m/Y', strtotime($order['order_date']))) ?></td>
                        <td>R$ <?= number_format($order['total_price'], 2, ',', '.') ?></td>
                        <td>
                            <select
                                    name="status"
                                    data-status="<?= $order['order_status'] ?>"
                                    data-order-id="<?= $order['order_id'] ?>"
                                    class="form-select form-select-sm pedido-status"
                            >
                                <option value="PENDENTE" <?= $order['order_status'] === 'PENDENTE' ? 'selected' : '' ?>>PENDENTE</option>
                                <option value="PAGO" <?= $order['order_status'] === 'PAGO' ? 'selected' : '' ?>>PAGO</option>
                                <option value="CANCELADO" <?= $order['order_status'] === 'CANCELADO' ? 'selected' : '' ?>>CANCELADO</option>
                            </select>
                        </td>
                        <td>
                            <button
                                    type="button"
                                    class="btn btn-primary detailOrder"
                                    data-id="<?= $order['order_id'] ?>"
                            >
                                Detalhes
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <button type="submit" class="btn btn-success">Salvar Alterações</button>
        </form>
    <?php endif; ?>
</main>

<?php require_once "app/models/modalDetalhePedido.php"; ?>

<?php $this->stop(); ?>
