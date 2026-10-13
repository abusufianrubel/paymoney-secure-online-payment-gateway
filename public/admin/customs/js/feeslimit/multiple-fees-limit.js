'use strict';

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

if ($('#defaultCurrency').val() == 1) {
    $('.defaultCurrencyDiv').show();
} else {
    $('.defaultCurrencyDiv').hide();
}

$('#deposit_limit_form').on('submit', function() {
    $(".fa-spinner").removeClass('d-none');
    $("#deposit_limit_update").attr("disabled", true);
    $("#deposit_limit_update_text").text(depositLimitUpdateText);
});

$(".has_transaction").on('click', function() {
    var paymentMethodId = $(this).data('method_id');
    
    if ($('#has_transaction_' + paymentMethodId).prop('checked') == true) {
        $('#has_transaction_' + paymentMethodId).val('Yes')
        $('#min_limit_' + paymentMethodId + ', #max_limit_' + paymentMethodId + ', #charge_percentage_' + paymentMethodId + ', #charge_fixed_' + paymentMethodId).prop("readonly", false);
    } else {
        $('#has_transaction_' + paymentMethodId).val('')
        $('#min_limit_' + paymentMethodId + ', #max_limit_' + paymentMethodId + ', #charge_percentage_' + paymentMethodId + ', #charge_fixed_' + paymentMethodId).prop("readonly", true);
    }
});

//on load
$(window).on('load', function() {
    var previousUrl = document.referrer;
    var urlByOwn = ADMIN_URL + '/settings/currency';
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
});

//currency dropdown
$('.listItem').on('click', function() {
    var currencyId = $(this).attr('data-rel');
    var type = $(this).attr('data-type');
    var currencyName = $(this).text();
    var defaultCurrency = $(this).attr('data-default');
    if (defaultCurrency == 1) {
        $('.defaultCurrencyDiv').show();
    } else {
        $('.defaultCurrencyDiv').hide();
    }
    localStorage.setItem('currencyId', currencyId);
    localStorage.setItem('currencyName', currencyName);
    localStorage.setItem('defaultCurrency', defaultCurrency);
    $('.currencyName').text(currencyName);
    $('#currency_id').val(currencyId);
    $('#type').val(type);
    $('#defaultCurrency').val(defaultCurrency);
    getFeesLimitDetails();
});

//Window on load/click on list item get fees limit details
function getFeesLimitDetails() {    
    var currencyId = $('#currency_id').val();
    var checkDefaultCurrency = $('#defaultCurrency').val();
    var transaction_type = $('#transaction_type').val();
    var module_alias = $('#module_alias').val();
    var url = ADMIN_URL + '/settings/get-feeslimit-details';
    $.ajax({
        url: url,
        type: "post",
        data: {
            'currency_id': currencyId,
            'transaction_type': transaction_type,
            'module_alias': module_alias,
            '_token': csrfToken
        },
        dataType: 'json',
        success: function(data) {
            if (data.status == 200) {
                var feesLimit = data.feeslimit;
                if (checkDefaultCurrency == 1) {
                    $('.defaultCurrencyDiv').show();
                    $('.default_currency_label').html(isActivated);
                    $('.default_currency_side_text').text(defaultCurrencyActive);
                    $(".has_transaction").prop('checked', true).prop('disabled', true).val('Yes');
                } else {
                    $('.defaultCurrencyDiv').hide();
                    $('.default_currency_label').html(isActivated);
                    $('.default_currency_side_text').text('');
                    $(".has_transaction").prop('checked', false).removeAttr('disabled').val('');
                }

                $.each(feesLimit, function(key, value) {
                    if (value.fees_limit != null) {
                        $('#min_limit_' + value.id).val(formatNumberToPrefDecimal(value.fees_limit.min_limit));
                        if (value.fees_limit.max_limit != null) {
                            $('#max_limit_' + value.id).val(formatNumberToPrefDecimal(value.fees_limit.max_limit));
                        } else {
                            $('#max_limit_' + value.id).val('');
                        }
                        $('#charge_percentage_' + value.id).val(formatNumberToPrefDecimal(value.fees_limit.charge_percentage));
                        $('#charge_fixed_' + value.id).val(formatNumberToPrefDecimal(value.fees_limit.charge_fixed));

                        $('#has_transaction_' + value.id).val(value.fees_limit.has_transaction);

                        if (value.fees_limit.has_transaction == 'Yes') {
                            $('#has_transaction_' + value.id).prop('checked', true);

                            $('#min_limit_' +  value.id + ', #max_limit_' +  value.id + ', #charge_percentage_' +  value.id + ', #charge_fixed_' +  value.id).prop("readonly", false);
                        } else {
                            $('#has_transaction_' + value.id).prop('checked', false);
                            $('#min_limit_' +  value.id + ', #max_limit_' +  value.id + ', #charge_percentage_' +  value.id + ', #charge_fixed_' +  value.id).prop("readonly", true);
                        }
                    } else {
                        $('#min_limit_' + value.id).val(formatNumberToPrefDecimal(
                        '1.00000000'));
                        $('#max_limit_' + value.id).val('');
                        $('#charge_percentage_' + value.id).val(formatNumberToPrefDecimal(
                            '0.00000000'));
                        $('#charge_fixed_' + value.id).val(formatNumberToPrefDecimal(
                            '0.00000000'));
                        $('#has_transaction_' + value.id).prop('checked', false);

                        $('#min_limit_' +  value.id + ', #max_limit_' +  value.id + ', #charge_percentage_' +  value.id + ', #charge_fixed_' +  value.id).prop("readonly", true);
                    }
                });
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
function getSpecificCurrencyDetails() {
    var currencyId = $('#currency_id').val();
    var checkDefaultCurrency = $('#defaultCurrency').val();
    var transaction_type = $('#transaction_type').val();
    var module_alias = $('#module_alias').val();
    var url = ADMIN_URL + '/settings/get-specific-currency-details';
    $.ajax({
        url: url,
        type: "post",
        data: {
            'currency_id': currencyId,
            'transaction_type': transaction_type,
            'module_alias': module_alias,
            '_token': csrfToken
        },
        dataType: 'json',
        success: function(data) {
            if (data.status == 200) {
                var feesLimit = data.feeslimit;
                
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
                
                $.each(feesLimit, function(key, value) {
                    if (value.fees_limit != null) {
                        $('#min_limit_' + value.id).val(formatNumberToPrefDecimal(value
                            .fees_limit.min_limit));
                        if (value.fees_limit.max_limit != null) {
                            $('#max_limit_' + value.id).val(formatNumberToPrefDecimal(value
                                .fees_limit.max_limit));
                        } else {
                            $('#max_limit_' + value.id).val('');
                        }
                        $('#charge_percentage_' + value.id).val(formatNumberToPrefDecimal(value
                            .fees_limit.charge_percentage));
                        $('#charge_fixed_' + value.id).val(formatNumberToPrefDecimal(value
                            .fees_limit.charge_fixed));

                        $('#has_transaction_' + value.id).val(value.fees_limit.has_transaction);
                        if (value.fees_limit.has_transaction == 'Yes') {
                            $('#has_transaction_' + value.id).prop('checked', true);

                            $('#min_limit_' +  value.id + ', #max_limit_' +  value.id + ', #charge_percentage_' +  value.id + ', #charge_fixed_' +  value.id).prop("readonly", false);
                        } else {
                            $('#has_transaction_' + value.id).prop('checked', false);
                            $('#min_limit_' +  value.id + ', #max_limit_' +  value.id + ', #charge_percentage_' +  value.id + ', #charge_fixed_' +  value.id).prop("readonly", true);
                        }
                    } else {
                        $('#min_limit_' + value.id).val(formatNumberToPrefDecimal(
                        '1.00000000'));
                        $('#max_limit_' + value.id).val('');
                        $('#charge_percentage_' + value.id).val(formatNumberToPrefDecimal(
                            '0.00000000'));
                        $('#charge_fixed_' + value.id).val(formatNumberToPrefDecimal(
                            '0.00000000'));

                        $('#has_transaction_' + value.id).prop('checked', false);
                        $('#min_limit_' +  value.id + ', #max_limit_' +  value.id + ', #charge_percentage_' +  value.id + ', #charge_fixed_' +  value.id).prop("readonly", true);
                    }
                });
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
                $(".has_transaction").prop('checked', false);
                $('.has_transaction').val('No');
                $('.min_limit').val('1.00000000');
                $('.max_limit').val('');
                $('.charge_percentage').val('0');
                $('.charge_fixed').val('0');
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