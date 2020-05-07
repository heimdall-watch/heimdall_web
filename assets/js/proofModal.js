import $ from "jquery";
window.$ = $;

$('#modalJustif').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var justif = button.data('justif');
    var idPresence = button.data('id');
    var csrf = button.data('csrf');
    var excuse = button.data('excuse');
    var excuseValidated = button.data('validate');
    var status = button.data('status');

    // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
    // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
    var modal = $(this)
    modal.find('#justifImage').attr('src', justif)
    modal.find('#justifExcuse').text(excuse);
    modal.find('#justifStatus').html(status);

    var $form = modal.find('.modal-footer form');
    $form.attr('action', $form.attr('action').replace('ID', idPresence))
    $form.find('input[name="_token"]').val(csrf)
    console.log(excuseValidated)
    //si excuse acceptée : possibilité de refuser
    if (excuseValidated === 1) {
        $form.find('#stateValidate').hide();
        $form.find('#stateRefuse').show();
    }
    //si excuse refusée : possibilité d'accepter
    else if (excuseValidated === 0) {
        $form.find('#stateValidate').show();
        $form.find('#stateRefuse').hide();
    } else {
        $form.find('#stateValidate').show();
        $form.find('#stateRefuse').show();
    }
});