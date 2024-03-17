$(function () {
    $('.pu, .expedition, .transit, .commission')
        .keyup(function() {
            updateMontant($(this).closest('tr'));
            updateSousTotal();
            updateTotal();
        })
        .click(function () {
            $(this).select();
        })
    ;

    $('button#quote').click(function(e) {
        e.preventDefault();
        let data = $('form').serialize();
        console.log(data);
        $.ajax({
            url: savePath,
            data: data,
            method: 'POST',
            success: function (response) {
                console.log(response);
            }
        });
    });

    let updateMontant = function(cible) {
        let pu = cible.find('.pu').val();
        let qty = cible.find('.qty').text();
        let total = pu * parseFloat(qty);
        cible.find('.montant').val(total);
    };

    let updateSousTotal = function() {
        let sousTotal = $('.montant').map(function() {
            return $(this).val();
        }).get().reduce(function(a, b) {
            return parseFloat(a) + parseFloat(b);
        });

        $('.sous-total').val(sousTotal);
    }

    let updateTotal = function() {
        let sousTotal = parseFloat($('.sous-total').val());
        let expedition = parseFloat($('.expedition').val());
        let transit = parseFloat($('.transit').val());
        let commission = parseFloat($('.commission').val());
        let button = $('button#quote');
        $('.total').val(sousTotal + expedition + transit + commission);
        button.prop('disabled', true);
        if (sousTotal + expedition + transit + commission > 0) {
            button.prop('disabled', false);
        }
    }

    $('.pu')
        .map(function() {
            updateMontant($(this).closest('tr'));
            updateSousTotal();
            updateTotal();
        })
    ;
});