
let totalProducts = 0;

const cart = JSON.parse(localStorage.getItem("cart")) || [];

function renderizarCarrinhoPreview() {
    const container = document.getElementById("cartPreview");
    if (!container) return;

    if (!cart.length) {
        container.innerHTML = "<p>Seu carrinho está vazio.</p>";
        return;
    }

    const resumoInicial = cart.slice(0, 2);
    const restante = cart.slice(2);
    let html = '<ul class="list-group list-group-flush">';

    resumoInicial.forEach((item, index) => {
        let priceProducts = item.price * item.stock;
        totalProducts += priceProducts;
        html += `
            <li class="list-group-item d-flex align-items-center gap-3">
                <img src="${item.image}" alt="${item.name}" class="rounded" style="height: 40px; width: 40px; object-fit: cover;">
                
                <div class="flex-grow-1">
                    <div class="fw-bold">${item.name}</div>
                    <small class="text-muted">${item.stock}x R$${item.price}</small>
                </div>
                <span class="badge bg-dark rounded-pill">
                    R$ ${priceProducts.toFixed(2)}
                </span>
            </li>`;
    });

    if (cart.length > 2) {
        html += `<div id="extraItems" class="collapse">`;
        restante.forEach(item => {
            let priceProducts = item.price * item.stock;
            totalProducts += priceProducts;
            html += `
            <li class="list-group-item d-flex align-items-center gap-3">
                <img src="${item.image}" alt="${item.name}" class="rounded" style="height: 40px; width: 40px; object-fit: cover;">
                
                <div class="flex-grow-1">
                    <div class="fw-bold">${item.name}</div>
                    <small class="text-muted">${item.stock}x R$${item.price}</small>
                </div>
                <span class="badge bg-dark rounded-pill">
                    R$ ${priceProducts.toFixed(2)}
                </span>
            </li>`;
        });
        html += `</div>`;
        html += `
                <div class="text-center mt-2">
                    <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#extraItems">
                        Ver todos os itens
                    </button>
                </div>`;
    }
    html += '</ul>';
    container.innerHTML = html;
    atualizarFrete(totalProducts);

}

function atualizarFrete(subtotal) {
    let frete = 20.00;
    if (subtotal >= 52.00 && subtotal <= 166.59) {
        frete = 15.00;
    } else if (subtotal > 200.00) {
        frete = 0.00;
    }
    $('#frete').val(frete.toFixed(2).replace('.', ','));
    atualizarTotal();
}
function atualizarTotal(){
    let frete = parseFloat($('#frete').val());
    if (isNaN(frete) || frete < 0) {
        frete = 0.0;
    }
    let totalPedido = totalProducts + frete;
    $('#total').val(totalPedido.toFixed(2).replace('.',','));
}

function enviarDados(formObjectJson) {
    if (formObjectJson === []) {
        return;
    }
    $.ajax({
        url: "/finalizarCompra",
        method: "POST",
        data: {
            formCheckout: JSON.stringify(formObjectJson)
        },
        dataType: 'json',
        success: function (data) {
            if (data.code === 200) {
                showModal(data.code,data.messages);
                showModal(200, "Pedido realizado com sucesso!")
                localStorage.setItem('cart', JSON.stringify([]));
                atualizarContadorCarrinho();
                renderizarCarrinho();
                window.location.href = "/meus-pedidos"
            } else {
                showModal(data.code, data.messages)
            }
        },
        error: function (jqXHR) {
            showModal(404, "Erro ao finalizar compra: " + jqXHR.responseText);
        }
    });
}

$(function () {
    renderizarCarrinhoPreview();
    atualizarTotal()
    $('#zipcode').on('input', function () {
        let cep = $(this).val().replace(/\D/g, '');

        if (cep.length === 8) {
            $.ajax({
                url: `https://viacep.com.br/ws/${cep}/json/`,
                method: "GET",
                success: function (data) {
                    if (data.erro) {
                        showModal(404,"Cep não encontrado.");
                        return;
                    }
                    $('#zipcode').val(cep.replace(/(\d{5})(\d{3})/, '$1-$2'));
                    $('#street').val(data.logradouro);
                    $('#neighborhood').val(data.bairro);
                    $('#city').val(data.localidade);
                    $('#state').val(data.uf);
                    atualizarFrete()
                },
                error: function (error) {
                    showModal(404,error.responseText);
                }
            });
        }
    });
    $('#checkoutForm').on('submit', function(e) {
        e.preventDefault();

        const cart = JSON.parse(localStorage.getItem("cart")) || [];

        if (!cart.length) {
            showModal(404,"Seu carrinho está vazio.");
            return;
        }

        const formData = new FormData(this);
        const formObject = {};

        formData.forEach((value, key) => {
            formObject[key] = value;
        });
        formObject.cart = cart;
        enviarDados(formObject);
    });
});