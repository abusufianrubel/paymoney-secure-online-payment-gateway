"use strict";
$(document).on('submit', '#virtualCardCategoryForm', function() {
    $("#virtualCardCategorySubmitBtn").attr("disabled", true);
    $(".fa-spinner").removeClass('d-none');
    $("#virtualCardCategorySubmitBtnText").text(submitButtonText);
});