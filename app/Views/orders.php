<?php

namespace app\controllers;
use League\Plates;

$this->layout("master", [
    'title' => "Meus Pedidos",
    'description' => "Acompanhe e gerencie seus pedidos."
]);

if(session_status() === PHP_SESSION_NONE){
    session_start();
}

$orders = OrdersController::getPedidosPorUsuario($_SESSION['user_id'] ?? 0) ?? [];

?>

<?php $this->start('body'); ?>

<main class="container my-5">
    <h2>Meus Pedidos</h2>

    <?php if(empty($orders)): ?>
        <p>Você não tem pedidos ainda.</p>
    <?php else: ?>
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
                        <td><?= htmlspecialchars($order['id_orders']) ?></td>
                        <td><?= htmlspecialchars(date('d/m/Y', strtotime($order['order_date']))) ?></td>
                        <td>R$ <?= number_format($order['total_price'], 2, ',', '.') ?></td>
                        <td>
                            <select name="status[<?= $order['id_orders'] ?>]" class="form-select form-select-sm">
                                <option value="PENDENTE" <?= $order['status'] === 'PENDENTE' ? 'selected' : '' ?>>PENDENTE</option>
                                <option value="PAGO" <?= $order['status'] === 'PAGO' ? 'selected' : '' ?>>PAGO</option>
                                <option value="CANCELADO" <?= $order['status'] === 'CANCELADO' ? 'selected' : '' ?>>CANCELADO</option>
                            </select>
                        </td>
                        <td>
                            <!-- Opcional: botão para detalhes do pedido -->
                            <a href="/pedido/<?= $order['id_orders'] ?>" class="btn btn-sm btn-info">Detalhes</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <button type="submit" class="btn btn-success">Salvar Alterações</button>
        </form>
    <?php endif; ?>
</main>

<?php $this->stop(); ?>
