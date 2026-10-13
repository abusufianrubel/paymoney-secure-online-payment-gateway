'use strict';

$('#kyc-mandatory').on('change', function () {
    if ($(this).val() == 'Yes') {
        $('#kyc-required-box').removeClass('d-none');
        $('#kyc-required-for').attr('required', true);
    } else {
        $('#kyc-required-box').addClass('d-none');
        $('#kyc-required-for').attr('required', false);
    }
})

$('#setting-form').on('submit', function () {
    $('#settings-submit-btn').attr("disabled", true);
    $('.fa-spin').removeClass("d-none");
    $('#settings-submit-btn-text').text(submitBtnText);
})