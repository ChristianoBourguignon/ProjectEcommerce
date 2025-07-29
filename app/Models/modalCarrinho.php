<div class="modal fade" id="carrinhoModal" tabindex="-1" aria-labelledby="carrinhoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="carrinhoModalLabel">Meu Carrinho</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>

            <div class="modal-body">
                <div class="table-responsive" id="cart-table-container">
                    <!-- Conteúdo do carrinho renderizado via JS -->
                </div>
                <div class="text-end fw-bold fs-5" id="cart-total">
                    <!-- Total será preenchido via JS -->
                </div>
                <p class="text-center" id="cart-empty-message" style="display: none;">Seu carrinho está vazio.</p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Continuar comprando</button>
                <button type="button"  class="btn btn-success" id="btn-finalizar-compra" style="display: none;">
                    Finalizar compra
                </button>
            </div>
        </div>
    </div>
</div>
