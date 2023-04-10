$(document).ready(function () {
    // console.log(jsData.urls.login);

    /*
    Exemple d'utilisation de l'objet ajax de jquery:

    $.ajax({

        url: 'https://jsonplaceholder.typicode.com/users',

        method: 'GET',

        data: {},

        dataType: 'json',

        beforeSend: function (xhr, settings) {

            console.group('Envoi Ajax 2 : ');

            console.log('Callback beforeSend() - Objet jqXHR : ', xhr)

            console.log('Callback beforeSend() - Objet settings : ', settings);

        },

    })

    .done(function(response, status, xhr){

        console.log('Méthode .done() - Réponse du server : ', response);

        console.log('Méthode .done() - Etat de le reqête : ', status);

        console.log('Méthode .done() - Objet jqXHR : ', xhr);

        console.log('Méthode .done() - response headers : ', xhr.getAllResponseHeaders());

    })

    .fail(function(xhr, status, errorText){

        console.log('Méthode .fail() - objet jqXHR : ', xhr);

        console.log('Méthode .fail() - état de la reqête : ', status);

        console.log('Méthode .fail() - erreur : ', errorText);

        console.log('Méthode .fail() - code erreur : ', xhr.status);

        console.log('Méthode .fail() - response headers : ', xhr.getAllResponseHeaders());

    })

    .always(function(xhr, status){

        console.log('Méthode .always() - objet jqXHR: ', xhr);

        console.log('Méthode .always() - status: ', status);

    });*/

    $(".create-product").on('click', function(e) {
        $.ajax({

            url: '//localhost:8000/create-product',
    
            method: 'GET',
    
            data: {},
    
            dataType: 'json',
    
            beforeSend: function (xhr, settings) {
    
                //console.group('Envoi Ajax 2 : ');
    
                //console.log('Callback beforeSend() - Objet jqXHR : ', xhr)
    
                //console.log('Callback beforeSend() - Objet settings : ', settings);
    
            },
    
        })
    
        .done(function(response, status, xhr){
    
            console.log('Méthode .done() - Réponse du server : ', response);
    
            console.log('Méthode .done() - Etat de la reqête : ', status);
    
            console.log('Méthode .done() - Objet jqXHR : ', xhr);
    
            console.log('Méthode .done() - response headers : ', xhr.getAllResponseHeaders());

            $('.success').fadeOut();
            $('.error').fadeOut();

            if(response.success) {
                $('.success').fadeIn().html('Nouveau produit correctement créé !');
            } else {
                let html = '<ul>';
                response.errors.map(el => { html += `<li>${el}</li>`});
                html += '</ul>'
                $('.error').fadeIn().html(html);
            }
    
        })
    
        .fail(function(xhr, status, errorText){
    
            console.log('Méthode .fail() - objet jqXHR : ', xhr);
    
            console.log('Méthode .fail() - état de le reqête : ', status);
    
            console.log('Méthode .fail() - erreur : ', errorText);
    
            console.log('Méthode .fail() - code erreur : ', xhr.status);
    
            console.log('Méthode .fail() - response headers : ', xhr.getAllResponseHeaders());
    
        })
    
        .always(function(xhr, status){
    
            //console.log('Méthode .always() - objet jqXHR: ', xhr);
    
            //console.log('Méthode .always() - status: ', status);
    
        });
    })

    
})