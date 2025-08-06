$('#formCupom').on('submit', function (e) {
    e.preventDefault();
    let code = this.code.value;
    let discount_price = this.discount_percent.value;
    let discount_value = this.discount_value.value;
    let min_cart_value = this.min_cart_value.value;
    let expires_at = this.expires_at.value;
    let active = this.active.value;

    if(code == null || discount_price == null || discount_value == null || min_cart_value == null || expires_at == null || active == null){
        showModal(404,"É necessário preencher todos os campos.");
    }
});

function criarCupom(code,discount_price, discount_value, min_cart_value, expires_at, active){
    if(code == null || discount_price == null || discount_value == null || min_cart_value == null || expires_at == null || active == null){
        showModal(404,"É necessário preencher todos os campos.");
    }
    let cupom = {
        code: code,
        discount_price: 10,
        discount_value: discount_value,
        min_cart_value: min_cart_value,
        expires_at: expires_at,
        active: active
    };
    $.ajax({
        url: `/salvarCupom`,
        method: "POST",
        data: {
            cupom: JSON.stringify(cupom),
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
            showModal(error.statusCode(),"Erro ao atualizar o status dos pedidos: " + error.responseText);
        }
    });
}
function mostrarCupons(){

}