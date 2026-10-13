'use strict';

$('#provider-form').on('submit', function () {
    $('#provider-submit-btn').attr("disabled", true);
    $('.fa-spin').removeClass("d-none");
    $('#provider-submit-btn-text').text(submitButtonText);
})