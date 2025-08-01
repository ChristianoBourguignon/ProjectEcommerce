function showModal(code, message) {
    const modal = new bootstrap.Modal(document.getElementById('alertModal'));
    const $modal = $('#alertModal');
    const $title = $('#alertModalLabel');
    const $body = $('#alertModalBody');
    const $header = $modal.find('.modal-header');

    let bgClass, titleText;

    switch (code) {
        case 200:
            bgClass = 'bg-success';
            titleText = 'Sucesso';
            break;
        case 400:
        case 404:
            bgClass = 'bg-danger text-white';
            titleText = 'Erro';
            break;
        case 201:
            bgClass = 'bg-warning text-dark';
            titleText = 'Aviso';
            break;
        default:
            bgClass = 'bg-primary text-white';
            titleText = 'Informação';
    }
    $header.removeClass('bg-success bg-danger bg-warning bg-primary text-dark text-white').addClass(bgClass);
    $title.text(titleText);
    $body.html(message);

    modal.show();
}
