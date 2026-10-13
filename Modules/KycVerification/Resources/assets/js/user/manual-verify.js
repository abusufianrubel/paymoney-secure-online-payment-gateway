'use strict';

if ($('.main-containt').find('#identity-verify').length) {
    $('#identity-verify-form').on('submit', function () {
        $(".spinner").removeClass('d-none');
        $("#identity-verify-submit-btn").attr("disabled", true);
        $("#identity-verify-submit-btn-txt").text(submitButtonText);
    });
}

if ($('.main-containt').find('#address-verify').length) {
    $('#address-verify-form').on('submit', function () {
        $(".spinner").removeClass('d-none');
        $("#address-verify-submit-btn").attr("disabled", true);
        $("#address-verify-submit-btn-txt").text(submitButtonText);
    });
}