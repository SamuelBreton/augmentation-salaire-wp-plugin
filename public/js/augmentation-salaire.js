jQuery(document).ready(function ($) {
    $('#augmentation-salaire-form').on('submit', function (e) {
        e.preventDefault();

        var amount = $('#amount').val();
        var year = $('#year').val();
        var nonce = $('#augmentation_salaire_nonce_field').val();

        $.ajax({
            url: augmentation_salaire_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'augmentation_salaire',
                amount: amount,
                year: year,
                security: nonce
            },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    $('#augmentation-salaire-result').html('Votre salaire actuel devrait être ' + response.data.adjustedAmount + ' €');
                } else {
                    $('#augmentation-salaire-result').html(response.data);
                }
            },
            error: function () {
                $('#augmentation-salaire-result').html('Erreur lors de l\'appel à l\'API.');
            }
        });
    });
});
