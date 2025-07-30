$('.detailOrder').on('click', function () {
    const order_id = $(this).data('id');
    let items = obterProdutosDoPedido(order_id);
    mostrarDetalheDoPedido(items,order_id);
});
function obterProdutosDoPedido(order_id){
    $.ajax({
        url: `/pedido`,
        method: "POST",
        data: {
            order_id: order_id
        },
        dataType: 'json',
        success: function (data) {
            if (data.code === 200) {
                return data.products;
            } else {
                showModal(data.code, data.messages)
            }
        },
        error: function (jqXHR) {
            showModal(404, "Erro ao obter os dados do pedido: " + jqXHR.responseText);
            products = [];
        }
    });
}
function mostrarDetalheDoPedido(itens, order_id) {
    const modal = new bootstrap.Modal($('#detailOrderItems'));
    const container = $("#detail-table-container");
    const totalContainer = $("#detail-total");
    const emptyMessage = $("#detail-empty-message");
    $(".orderId").text(`#${order_id}`);

    if (!itens || itens.length === 0) {
        container.html("");
        totalContainer.html("");
        emptyMessage.show();
        modal.show();
        return;
    }

    emptyMessage.hide();

    let html = `
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Preço</th>
                    <th>Quantidade</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
    `;

    let total = 0;

    itens.forEach((item, index) => {
        const itemTotal = item.price * item.stock;
        total += itemTotal;

        html += `
            <tr data-index="${index}">
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <img src="${item.image}" alt="${item.name}" width="50">
                        ${item.name}
                    </div>
                </td>
                <td>R$ ${item.price.toFixed(2).replace('.', ',')}</td>
                <td>
                    <div class="d-flex align-items-center">
                        <span class="mx-2">${item.stock}</span>
                    </div>
                </td>
                <td>R$ ${itemTotal.toFixed(2).replace('.', ',')}</td>
            </tr>           
        `;
    });

    html += "</tbody></table>";
    container.html(html);
    totalContainer.html("Total: R$ " + total.toFixed(2).replace('.', ','));
    modal.show();
}
