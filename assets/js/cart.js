$(document).ready(function () {
  // ajout dans le panier
  
  // recupération de l'element destiné à
  // recevoir la modale de connexion
  let signInElement = document.getElementById("signInModal");
  let signInModal = null;

  // Creation de la modale (instruction sur la page de doc bootstrap)
  if (signInElement) {
      signInModal = new bootstrap.Modal(signInElement, {
          focus: true,
      });
  }
  let $addToCartBtn = $("#add-to-cart-btn");

  let $needsConnexion = parseInt($addToCartBtn.attr('data-needsconnection'));

  let $formCartAdd = $(".formCartAdd");
  // console.log($needsConnexion);

  if ($formCartAdd) { 
      $formCartAdd.on('submit', function (evt) { 
        evt.preventDefault();
        //recupérons l'id du produit à ajouter au panier:
        let $productId = $('#addToCartForm .hidden-id').val();

        if ($needsConnexion) {
          // on est pas logué, on ouvre la modale:
              signInModal.show();
        } else {
          // On lance l'ajax d'ajout.
          $.ajax({
            method: "POST",
            url: jsData.urls.addToCart,
            data: {
                productId: $productId,
            },
            dataType: "json",
          })
          .done(function (resp) {
              // succès de la requête asynchrone:
              // console.log( "ajax success" );
              // console.log(resp);

            if (resp.success) {
              let count = parseInt($('#navbarSupportedContent .badge').text());
              $('#navbarSupportedContent .badge').html(count + 1);
            }
          })
          .fail(function (jqXHR, textStatus) {
              // échec de la requête asynchrone:
              // console.log( "Request failed: " + textStatus );
              // console.log(jqXHR);
          });
        }
    })
  }

  $('.cart-delete-link').on('click', function (e) {
    $(this).parent().submit();
  })
})