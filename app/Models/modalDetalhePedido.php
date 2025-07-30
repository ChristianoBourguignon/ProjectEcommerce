<div class="modal fade" id="detailOrderItems" tabindex="-1" aria-labelledby="detailOrder" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailOrderLabel">Meu pedido <span class="orderId"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>

            <div class="modal-body">
                <div class="table-responsive" id="detail-table-container">
                    <!-- Conteúdo do carrinho renderizado via JS -->
                </div>
                <div class="text-end fw-bold fs-5" id="detail-total">
                    <!-- Total será preenchido via JS -->
                </div>
                <p class="text-center" id="detail-empty-message" style="display: none;">Seu pedido está vazio.</p>
            </div>
        </div>
    </div>
</div>
<script>
    <?php require_once "app/static/js/ordersDetailController.js"; ?>
</script>
