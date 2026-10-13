'use strict';

if ($('#defaultCurrency').val() == 1) {
    $('.defaultCurrencyDiv').show();
} else {
    $('.defaultCurrencyDiv').hide();
}

function restrictNumberToPrefdecimalOnInput(e)
{
    var type = $('#type').val();
    restrictNumberToPrefdecimal(e, type);
}

function formatNumberToPrefDecimal(num = 0)
{
    num = ((Math.abs(num)).toFixed(decimalFormat))
    return num;
}

$('#deposit_limit_form').on('submit', function() {
    $(".fa-spinner").removeClass('d-none');
    $("#deposit_limit_update").attr("disabled", true);
    $("#deposit_limit_update_text").text(depositLimitUpdateText);
});

$("#has_transaction").on('click', function() {
    
    if ($(".has_transaction").prop('checked') == false) {
        $('#has_transaction').val('No');
        $("#min_limit, #max_limit, #charge_percentage, #charge_fixed").prop("readonly", true);
    } else {
        $('#has_transaction').val('Yes');
        $("#min_limit, #max_limit, #charge_percentage, #charge_fixed").prop("readonly", false);
    }
});


//currency dropdown
$('.listItem').on('click',function() {
    
    if ($(".has_transaction").prop('checked') == false) {
        $('#has_transaction').val('No');
        $("#min_limit, #max_limit, #charge_percentage, #charge_fixed").prop("readonly", true);
    } else {
        $('#has_transaction').val('Yes');
        $("#min_limit, #max_limit, #charge_percentage, #charge_fixed").prop("readonly", false);
    }

    var currencyId       = $(this).attr('data-rel');
    var currencyName     = $(this).text();
    var defaultCurrency  = $(this).attr('data-default');

    if (defaultCurrency == 1) {
        $('.defaultCurrencyDiv').show();
    } else {
        $('.defaultCurrencyDiv').hide();
    }

    localStorage.setItem('currencyId',currencyId);
    localStorage.setItem('currencyName',currencyName);
    localStorage.setItem('defaultCurrency',defaultCurrency);

    $('.currencyName').text(currencyName);
    $('#currency_id').val(currencyId);
    $('#defaultCurrency').val(defaultCurrency);
    getFeesLimitDetails();
});

//on load
$(window).on('load',function()
{
    var previousUrl = document.referrer;
    var urlByOwn    = ADMIN_URL + '/settings/currency';

    if (previousUrl == urlByOwn) {
        localStorage.removeItem('currencyId');
        localStorage.removeItem('currencyName');
        localStorage.removeItem('defaultCurrency');
    } else {
        if (
            localStorage.getItem('currencyName') && 
            localStorage.getItem('currencyId') && 
            localStorage.getItem('defaultCurrency')
        ) {
            $('.currencyName').text(localStorage.getItem('currencyName'));
            $('#currency_id').val(localStorage.getItem('currencyId'));
            $('#defaultCurrency').val(localStorage.getItem('defaultCurrency'));
            getFeesLimitDetails();
        } else {
            getSpecificCurrencyDetails();
        }
    }

    if ($(".has_transaction").prop('checked') == false) {
        $("#min_limit, #max_limit, #charge_percentage, #charge_fixed").prop("readonly", true);
    }
});


//Window on load/click on list item get fees limit details
function getFeesLimitDetails()
{
    var currencyId           = $('#currency_id').val();
    var transaction_type     = $('#transaction_type').val();
    var checkDefaultCurrency = $('#defaultCurrency').val();
    var url                  = ADMIN_URL + '/settings/get-feeslimit-details';

    $.ajax({
        url : url,
        type : "post",
        data : {
            'currency_id':currencyId,
            'transaction_type':transaction_type,
            '_token':csrfToken
        },
        dataType : 'json',
        success:function(data)
        {
            if(data.status == 200) {
                if (checkDefaultCurrency == 1) {
                    $('.defaultCurrencyDiv').show();

                    $('.default_currency_label').html(isActivated);
                    $('.default_currency_side_text').text(defaultCurrencyActive);

                    $(".has_transaction").prop('checked', true).prop('disabled', true).val('Yes');

                    $("#min_limit, #max_limit, #charge_percentage, #charge_fixed").prop("readonly", false);

                } else {
                    $('.defaultCurrencyDiv').hide();
                    $('.default_currency_label').html(isActivated);
                    $('.default_currency_side_text').text('');
                    $("#has_transaction").prop('checked', false).removeAttr('disabled');
                    $('.has_transaction').val('No');
                    $("#min_limit, #max_limit, #charge_percentage, #charge_fixed").prop("readonly", true);
                }

                if (data.feeslimit.has_transaction == 'Yes') {
                    $(".has_transaction").prop('checked', true);
                    $('.has_transaction').val(data.feeslimit.has_transaction);
                    $("#min_limit, #max_limit, #charge_percentage, #charge_fixed").prop("readonly", false);
                } else {
                    $("#has_transaction").prop('checked', false);
                    $('.has_transaction').val('No');
                    $("#min_limit, #max_limit, #charge_percentage, #charge_fixed").prop("readonly", true);
                }

                $('#id').val(data.feeslimit.id);
                $('.min_limit').val(formatNumberToPrefDecimal(data.feeslimit.min_limit));

                if (data.feeslimit.max_limit != null) {
                    $('.max_limit').val(formatNumberToPrefDecimal(data.feeslimit.max_limit));
                } else {
                    $('.max_limit').val('');
                }

                $('.charge_percentage').val(formatNumberToPrefDecimal(data.feeslimit.charge_percentage));
                $('.charge_fixed').val(formatNumberToPrefDecimal(data.feeslimit.charge_fixed));
                $('#processing_time').val(data.feeslimit.processing_time);
            } else {
                if (checkDefaultCurrency == 1) {
                    $('.defaultCurrencyDiv').show();

                    $('.default_currency_label').html(isActivated);
                    $('.default_currency_side_text').text(defaultCurrencyActive);
                    $(".has_transaction").prop('checked', true).val('Yes');

                } else {
                    $('.defaultCurrencyDiv').hide();
                    $('.default_currency_label').html(isActivated);
                    $('.default_currency_side_text').text('');
                    $('#has_transaction').removeAttr('disabled');
                    $(".has_transaction").prop('checked', false).val('No');
                }

                $('#id').val('');
                $('.min_limit').val(formatNumberToPrefDecimal('1.00000000'));
                $('.max_limit').val('');
                $('.charge_percentage').val(formatNumberToPrefDecimal('0.00000000'));
                $('.charge_fixed').val(formatNumberToPrefDecimal('0.00000000'));
                $("#min_limit, #max_limit, #charge_percentage, #charge_fixed").prop("readonly", true);
            }
        },
        error: function(error) {
            Swal.fire(
                failedText,
                JSON.parse(error.responseText).message,
                'error'
            )
        }
    });
}

// Get Specific Currency Details
function getSpecificCurrencyDetails()
{
    var currencyId = $('#currency_id').val();
    var checkDefaultCurrency = $('#defaultCurrency').val();
    var transaction_type = $('#transaction_type').val();
    var url = ADMIN_URL + '/settings/get-specific-currency-details';

    $.ajax({
        url : url,
        type : "post",
        data : {
            'currency_id':currencyId,
            'transaction_type':transaction_type,
            '_token':csrfToken
        },
        dataType : 'json',
        success:function(data) {

            if (data.status == 200) {
                
                if (checkDefaultCurrency == 1) {
                    $('.defaultCurrencyDiv').show();

                    $('.default_currency_label').html(isActivated);
                    $('.default_currency_side_text').text(defaultCurrencyActive);
                    $(".has_transaction").prop('checked', true);
                    $('#has_transaction').attr('disabled', true).val('Yes');
                    $("#min_limit, #max_limit, #charge_percentage, #charge_fixed").prop("readonly", false);

                } else {
                    $('.defaultCurrencyDiv').hide();

                    $('.default_currency_label').html(isActivated);
                    $('.default_currency_side_text').hide();
                    $("#has_transaction").prop('checked', false).removeAttr('disabled');
                    $('.has_transaction').val('No');
                    $("#min_limit, #max_limit, #charge_percentage, #charge_fixed").prop("readonly", true);
                }

                if (data.feeslimit.has_transaction == 'Yes') {

                    $(".has_transaction").prop('checked', true);
                    $('.has_transaction').val('Yes');
                    $("#min_limit, #max_limit, #charge_percentage, #charge_fixed").prop("readonly", false);

                } else {

                    $("#has_transaction").prop('checked', false);
                    $('.has_transaction').val('No');
                    $("#min_limit, #max_limit, #charge_percentage, #charge_fixed").prop("readonly", true);
                }

                $('#id').val(data.feeslimit.id);
                $('.currencyName').text(data.currency.name);
                $('#currency_id').val(data.currency.id);
                $('.min_limit').val(formatNumberToPrefDecimal(data.feeslimit.min_limit));

                if (data.feeslimit.max_limit != null) {
                    $('.max_limit').val(formatNumberToPrefDecimal(data.feeslimit.max_limit));
                } else {
                    $('.max_limit').val('');
                }

                $('.charge_percentage').val(formatNumberToPrefDecimal(data.feeslimit.charge_percentage));
                $('.charge_fixed').val(formatNumberToPrefDecimal(data.feeslimit.charge_fixed));
                $('#processing_time').val(data.feeslimit.processing_time);
            } else {
                
                if (checkDefaultCurrency == 1) {
                    $('.defaultCurrencyDiv').show();

                    $('.default_currency_label').html(isActivated);
                    $('.default_currency_side_text').text(defaultCurrencyActive);
                    $('#has_transaction').removeAttr('disabled');

                } else {
                    $('.defaultCurrencyDiv').hide();

                    $('.default_currency_label').html(isActivated);
                    $('.default_currency_side_text').text('');
                }

                $('#id').val('');
                $('.currencyName').text(data.currency.name);
                $('#currency_id').val(data.currency.id);
                $(".has_transaction").prop('checked', false).val('No');
                $('.min_limit').val(formatNumberToPrefDecimal(formatNumberToPrefDecimal('1.00000000')));
                $('.max_limit').val('');
                $('.charge_percentage').val(formatNumberToPrefDecimal('0.00000000'));
                $('.charge_fixed').val(formatNumberToPrefDecimal('0.00000000'));
            }
        },
        error: function(error) {
            Swal.fire(
                failedText,
                JSON.parse(error.responseText).message,
                'error'
            )
        }
    });
}