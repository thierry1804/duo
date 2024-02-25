$(function () {
    $(document).ready(function() {
        $('.addToCart').on('click', function(e) {
            e.preventDefault();
            let path = $(this).attr('data-path');
            let cible = $(this);
            cible.closest('div').addClass('rotating');
            $.ajax({
                url: path,
                type: 'POST',
                success: function(response) {
                    $('#wishlist').html(response);
                    cible.closest('div').removeClass('rotating');
                }
            });
        });
    });
});