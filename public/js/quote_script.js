$(function () {
    $('.pu, .expedition, .transit, .commission')
        .keyup(function() {
            updateMontant($(this).closest('tr'));
            updateSousTotal();
            updateTotal();
        })
    ;

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
        $('.total').val(sousTotal + expedition + transit + commission);
    }
});