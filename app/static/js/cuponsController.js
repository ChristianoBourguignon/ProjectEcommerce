$('#formCupom').on('submit', function (e) {
    e.preventDefault();
    console.log("Entrou no action do botao")
    let code = this.code.value;
    let discount_price = this.discount_percent.value;
    let discount_value = this.discount_value.value;
    let min_cart_value = this.min_cart_value.value;
    let expires_at = this.expires_at.value;
    let active = this.active.value;

    if(code == null || discount_price == null || discount_value == null || min_cart_value == null || expires_at == null || active == null){
        showModal(404,"É necessário preencher todos os campos.");
        return;
    }
    let cupom = {
        code: code,
        discount_percent: discount_price,
        discount_value: discount_value,
        min_cart_value: min_cart_value,
        expires_at: expires_at,
        active: active
    };
    criarCupom(cupom);
});

function criarCupom(cupom){
    if(cupom == null || cupom.length <= 0){
        showModal(404,"É necessário preencher todos os campos.");
        return;
    }
    console.log("Entrou no ajax")

    $.ajax({
        url: `/salvarCupom`,
        method: "POST",
        data: {
            cupom: JSON.stringify(cupom),
        },
        dataType: 'json',
        success: function (data) {
            if(data.code === 200){
                showModal(data.code,data.message)
            } else {
                showModal(data.code,data.message);
            }
        },
        error: function (error) {
            try {
                const response = JSON.parse(error.responseText);
                showModal(error.status, response.message);
            } catch (e) {
                showModal(error.status, "Erro ao criar o cupom: Erro inesperado.");
            }
        }
    });
}
function mostrarCupons(){

}