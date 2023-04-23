$(document).ready(function () {

  /**** Click sur un bouton de selection d'un mode de transport (page order, etape 2)  ****/
  let $shippingMethodForm = $(".shipping-toggle");
    if($shippingMethodForm){
        $(".shipping-toggle .shipping-method-radio").on('click', function(e){
            $shippingMethodForm.parent().submit();
        })
  }

  $(".shipping-toggle input[type=radio]").on('click', function (e) {
    e.stopPropagation();
  })

  $(".shipping-toggle").on('click', function (e) {
    $(this).find('input[type=radio]').trigger('click');
  })

  /**** Click sur un bouton de selection d'un mode de paiement (page order, etape 3)  ****/
  let $paymentMethodForm = $("#paymentMethodForm")
  if($paymentMethodForm){
      $(".payment-method-radio").on('click', function(e){
          $paymentMethodForm.submit();
      })
  }

  
})