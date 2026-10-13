"use strict";
$(document).on('submit', '#virtualCardProviderForm', function() {
    $("#virtualCardProviderSubmitBtn").attr("disabled", true);
    $(".fa-spinner").removeClass('d-none');
    $("#virtualCardProviderSubmitBtnText").text(submitButtonText);
});