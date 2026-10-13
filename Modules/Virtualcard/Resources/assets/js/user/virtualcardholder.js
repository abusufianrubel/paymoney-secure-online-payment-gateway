'use strict';

// Virtualcard Holder Create
if ($('.main-containt').find('#VirtualcardHolderCreate').length) {

    function showHideCardHolderForm(cardHolderType) {

        if (cardHolderType === 'Business') {
            $('#individualForm').addClass('d-none');
            $('#businessForm').removeClass('d-none');
        } else if (cardHolderType === 'Individual') {
            $('#businessForm').addClass('d-none');
            $('#individualForm').removeClass('d-none');
        }
    }


    $('#type').on('change', function () {
        showHideCardHolderForm($(this).val());
    });

    $(window).on('load', function () {
        const initialType = $('#type').val();
        showHideCardHolderForm(initialType);
    });


    $('#dateOfBirth').daterangepicker({
        "singleDatePicker": true,
        locale: {
            format: 'DD-M-YYYY'
        }
    });

}
$(document).on('submit', '#businessForm', function() {
    $("#businessFormSubmitBtn").attr("disabled", true);
    $(".spinner").removeClass('d-none');
    $("#businessFormSubmitBtnText").text(submitBtnText);
});

$(document).on('submit', '#individualForm', function() {
    $("#individualFormSubmitBtn").attr("disabled", true);
    $(".spinner").removeClass('d-none');
    $("#individualFormSubmitBtnText").text(submitBtnText);
});

// Virtualcard Holder Index
if ($('.main-containt').find('#VirtualcardHolderIndex').length) {

    $(function() {
        $(".filter-panel").css('display', 'block');
    });

    document.querySelector('form').addEventListener('submit', function (e) {
        e.preventDefault();
        const params = new URLSearchParams(new FormData(this));
        const url = `${filterUrl}?${params.toString().replace(/%5B/g, '[').replace(/%5D/g, ']')}`;
        window.location.href = url;
    });
}
