'use strict';

if (errorMessage) {
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "showDuration": "700"
    }
    if (errorMessageClass == 'alert-danger') {
        toastr.error(errorMessage);
    } else {
        toastr.success(errorMessage);
    }
}

$(document).ready(function() {
    new Fingerprint2().get(function(result, components)
    {
        $('#browser_fingerprint').val(result);
    });
});

$('#login-form').on('submit', function () {
    $('.spinner').removeClass('d-none');
    $('#rightAngleSvgIcon').addClass('d-none');
    $("#login-btn").attr("disabled", true);
    $("#login-btn-text").text(loginButtonText);
});

$('.demo-login').on('click', function () {
    let loginFor = $(this).data('login');

    if (loginFor == 'user') {
        $('#email_only').val('kyla@gmail.com');
        $('#password').val('123456');
    } else if(loginFor == 'merchant') {
        $('#email_only').val('irish@gmail.com');
        $('#password').val('123456');
    }

    $('#login-form').trigger('submit');
});
