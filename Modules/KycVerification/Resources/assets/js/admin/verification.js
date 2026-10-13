'use strict';

$(".select2").select2();

$('#verify-form').on('submit', function () {
    $('#verify-btn').attr("disabled", true);
    $('.fa-spin').removeClass("d-none");
    $('#verify-btn-txt').text(updateBtnTxt);
})

