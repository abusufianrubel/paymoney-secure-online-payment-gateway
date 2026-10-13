"use strict";
$(document).on('submit', '#virtualCardPreferenceUpdateBtnForm', function() {
    $("#virtualCardPreferenceUpdateBtn").attr("disabled", true);
    $(".fa-spinner").removeClass('d-none');
    $("#virtualCardPreferenceUpdateBtnText").text(submitButtonText);
});