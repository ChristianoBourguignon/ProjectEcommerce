$('.detailOrder').on('click', function () {
    const order_id = $(this).data('id');
    obterProdutosDoPedido(order_id).then(itens => {
        mostrarDetalheDoPedido(itens, order_id);
    });
});
$('#formPedidos').on('submit', function (e) {
    e.preventDefault();

    const pedidosAlterados = [];

    const selects = this.querySelectorAll('.pedido-status');
    selects.forEach(select => {
        const statusOriginal = select.getAttribute('data-status');
        const statusAtual = select.value;
        const orderId = select.getAttribute('data-order-id');

        if (statusAtual !== statusOriginal) {
            pedidosAlterados.push({
                order_id: orderId,
                status: statusAtual
            });
        }
    });

    if (pedidosAlterados.length === 0) {
        showModal(300,"Nenhum pedido foi alterado.")
        return;
    }
    console.log(pedidosAlterados);
    atualizarStatusPedido(pedidosAlterados);
});

function atualizarStatusPedido(pedidosAlterados){
    const orders = pedidosAlterados.map(order => ({
        order_id: Number(order.order_id),
        status: String(order.status)
    }));
    $.ajax({
        url: `/atualizarStatusPedidos`,
        method: "POST",
        data: {
            orders: JSON.stringify(orders)
        },
        dataType: 'json',
        success: function (data) {
            if(data.code === 200){
                showModal(200,data.message)
            } else {
                showModal(data.code,data.message);
            }
        },
        error: function (error) {
            showModal(404,"Erro ao atualizar o status dos pedidos: " + error.responseText);
        }
    });

}

function obterProdutosDoPedido(order_id) {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: `/getItemsPedido`,
            method: "POST",
            data: { order_id: order_id },
            dataType: 'json',
            success: function (data) {
                if (data.code === 200) {
                    let produtos = data.products;
                    if (typeof produtos === 'string') {
                        try {
                            produtos = JSON.parse(produtos);
                        } catch (e) {
                            return reject("Erro ao parsear JSON");
                        }
                    }
                    resolve(produtos);
                } else {
                    reject(data.message);
                }
            },
            error: function (error) {
                reject("Erro ao obter produtos: " + error.responseText);
            }
        });
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

    let total = Number(itens[0].shipping_price);

    itens.forEach((item, index) => {
        const itemTotal = Number(item.item_unit_price) * Number(item.item_quantity);
        const unitPrice = Number(item.item_unit_price);
        const quantity = Number(item.item_quantity);
        total += itemTotal;

        html += `
            <tr data-index="${index}">
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <img src="${item.product_image}" alt="${item.product_name}" width="50">
                        ${item.product_name}
                    </div>
                </td>
                <td>R$ ${unitPrice.toFixed(2).replace('.', ',')}</td>
                <td>
                    <div class="d-flex align-items-center">
                        <span class="mx-2">${quantity}</span>
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
