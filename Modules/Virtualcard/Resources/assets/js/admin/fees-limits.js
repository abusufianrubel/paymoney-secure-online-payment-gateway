"use strict";

$(window).on('load', function() {
    providerId = $('#virtualcard_provider_id').val();
    getProviderCurrencies(providerId);
});

$('#virtualcard_provider_id').on('change', function() {
    providerId = $(this).val();
    getProviderCurrencies(providerId);
});

function getProviderCurrencies(providerId)
{
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: "GET",
        url: SITE_URL + "/" + ADMIN_PREFIX + "/card/fees-limits/provider/" + providerId + "/currencies",
        data: {
        },
        dataType: 'json',
        success: function(response) {
            let options = '';

            $.each(response.currencies, (index, value) => {
                options +=
                    `<option ${value.code == selectedCurrency ? 'selected' : ''} value="${value.code}" data-type="${value.type}">${value.code}</option>`;
            });

            $('#currencyCode').html(options);
        }
    });
}

$(document).on('submit', '#virtualCardFeesForm', function() {
    $("#virtualCardFeesSubmitBtn").attr("disabled", true);
    $(".fa-spinner").removeClass('d-none');
    $("#virtualCardFeesSubmitBtnText").text(submitButtonText);
});

function restrictNumberToPrefdecimalOnInput(e)
{
    var type = $('select#currencyCode').find(':selected').data('type')
    restrictNumberToPrefdecimal(e, type);
}