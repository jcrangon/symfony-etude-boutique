var steps = $(".step");
    var $checkoutStep = parseInt($(".checkout-step").attr('data-step'));
    var delay = 1000;
    console.dir(steps);
    if($checkoutStep === 1){
        setTimeout(function() {
            $('.step1').addClass('done')
        }, 500); 
    }
    if($checkoutStep === 2){
        setTimeout(function() {
            $('.step1').addClass('done')
        }, 500);
        setTimeout(function() {
            $('.step2').addClass('done')
        }, 1500);
    }
    if($checkoutStep === 3){
        setTimeout(function() {
            $('.step1').addClass('done')
        }, 500);
        setTimeout(function() {
            $('.step2').addClass('done')
        }, 1500);
        setTimeout(function() {
            $('.step3').addClass('done')
        }, 2500);
    }
    if($checkoutStep === 4){
        setTimeout(function() {
            $('.step1').addClass('done')
        }, 500);
        setTimeout(function() {
            $('.step2').addClass('done')
        }, 1500);
        setTimeout(function() {
            $('.step3').addClass('done')
        }, 2500);
        setTimeout(function() {
            $('.step4').addClass('done')
        }, 3500);
    }

    // click sur une etape
    if($('#checkout-step-finder')){
        let maxStep = parseInt($('#checkout-step-finder').attr('data-checkoutstep'));

        $(".step1").on('click', function(e) {
            if(maxStep >= 1) {
                window.location.assign($(this).attr('data-url'));
            }
        });

        $(".step2").on('click', function(e) {
            if(maxStep >= 2) {
                window.location.assign($(this).attr('data-url'));
            }
        });

        $(".step3").on('click', function(e) {
            if(maxStep >= 3) {
                window.location.assign($(this).attr('data-url'));
            }
        });

        $(".step4").on('click', function(e) {
            if(maxStep >= 4) {
                window.location.assign($(this).attr('data-url'));
            }
        });
    }