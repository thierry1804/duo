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

        $('.remove-item').on('click', function(e) {
            e.preventDefault();
            let path = $(this).attr('href');
            Swal.fire({
                title: "Confirmation",
                text: "Êtes-vous sûr de vouloir supprimer cet article du panier ?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Oui, supprimer",
                cancelButtonText: "Non, annuler"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: path,
                        type: 'DELETE',
                        success: function(response) {
                            window.location.reload();
                        }
                    });
                }
            });
        });
    });
});