function atualizarCarrinho(id, nome, preco, estoque, max_estoque, imagem) {
    const cart = JSON.parse(localStorage.getItem("cart")) || [];

    const existingIndex = cart.findIndex(item => item.id === id);
    if (existingIndex !== -1) {
        if (cart[existingIndex].stock < max_estoque) {
            cart[existingIndex].stock++;
        } else {
            return;
        }
    } else {
        cart.push({
            id,
            name: nome,
            price: parseFloat(preco),
            stock: 1,
            image: imagem,
            max_estoque: max_estoque
        });
    }

    localStorage.setItem("cart", JSON.stringify(cart));
    atualizarContadorCarrinho();
    renderizarCarrinho();
}
function atualizarContadorCarrinho() {
    const cart = JSON.parse(localStorage.getItem("cart")) || [];
    let total = 0;
    cart.forEach(item => total += item.stock);

    $("#cart-count").text(total);
    $("#cart-count-mobile").text(total);
}
function renderizarCarrinho() {
    const cart = JSON.parse(localStorage.getItem("cart")) || [];
    const container = $("#cart-table-container");
    const totalContainer = $("#cart-total");
    const emptyMessage = $("#cart-empty-message");
    const finalizarBtn = $("#btn-finalizar-compra");

    if (cart.length === 0) {
        container.html("");
        totalContainer.html("");
        emptyMessage.show();
        finalizarBtn.hide();
        return;
    }

    emptyMessage.hide();
    finalizarBtn.show();

    let html = `
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Preço</th>
                    <th>Quantidade</th>
                    <th>Total</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
    `;

    let total = 0;

    cart.forEach((item, index) => {
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
                        <button class="btn btn-sm btn-outline-secondary alterar-quantidade" data-dir="down" data-index="${index}">-</button>
                        <span class="mx-2">${item.stock}</span>
                        <button class="btn btn-sm btn-outline-secondary alterar-quantidade" data-dir="up" data-index="${index}">+</button>
                    </div>
                    <span class="error-stock alert alert-danger d-none" role="alert">
                </td>
                <td>R$ ${itemTotal.toFixed(2).replace('.', ',')}</td>
                <td>
                    <button class="btn btn-sm btn-danger remover-item" data-index="${index}">Remover</button>
                </td>                
            </tr>           
        `;
    });

    html += "</tbody></table>";
    container.html(html);
    totalContainer.html("Total: R$ " + total.toFixed(2).replace('.', ','));
}
$(document).on("click", ".alterar-quantidade", function () {
    const dir = $(this).data("dir");
    const index = $(this).data("index");
    const cart = JSON.parse(localStorage.getItem("cart")) || [];

    if (dir === "up") {
        let stockAdjust = cart[index].stock + 1;

        if (stockAdjust > cart[index].max_estoque) {
            $(".error-stock").removeClass("d-none").text("Estoque máximo atingido!");
            setTimeout(function () {
                $(".error-stock").addClass("d-none").text("");
            }, 3000);
            return;
        }
        cart[index].stock++;
    } else if (dir === "down" && cart[index].stock > 1) {
        cart[index].stock--;
    } else {
        return;
    }

    localStorage.setItem("cart", JSON.stringify(cart));
    atualizarContadorCarrinho();
    renderizarCarrinho();
});

$(document).on("click", ".remover-item", function () {
    const index = $(this).data("index");
    const cart = JSON.parse(localStorage.getItem("cart")) || [];

    cart.splice(index, 1);

    localStorage.setItem("cart", JSON.stringify(cart));
    atualizarContadorCarrinho();
    renderizarCarrinho();
});

$('.addToCart').on('click', function () {
    const button = $(this);
    const id = button.data('id');
    const nome = button.data('nome');
    const preco = button.data('preco');
    const estoque = button.data('estoque');
    const max_estoque = button.data('estoque-max');
    const imagem = button.data('image');

    atualizarCarrinho(id, nome, preco, estoque, max_estoque, imagem);
});

$(function () {
    atualizarContadorCarrinho();
    renderizarCarrinho();
});
