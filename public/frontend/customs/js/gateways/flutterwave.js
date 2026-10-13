
'use strict';

$('#payment-form').on('submit', function () {
    $('.spinner').removeClass('d-none');
    $("#flutterwave-button-submit").attr("disabled", true);
    $("#flutterwaveSubmitBtnText").text(submitting);
});

