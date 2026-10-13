"use strict";

function restrictNumberToPrefdecimalOnInput(e) {
    var type = $('select#virtualCard').find(':selected').data('type') ?? 'fiat'
    restrictNumberToPrefdecimal(e, type);
}

function determineDecimalPoint() {
    var currencyType = $('select#virtualCard').find(':selected').data('type')
    if (currencyType == 'crypto') {
        $('.pFees, .fFees, .total_fees').text(CRYPTODP);
        $("#amount").attr('placeholder', CRYPTODP);
    } else if (currencyType == 'fiat') {
        $('.pFees, .fFees, .total_fees').text(FIATDP);
        $("#amount").attr('placeholder', FIATDP);
    }
}

// external.js file
function isNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    return (charCode > 31 && (charCode < 48 || charCode > 57)) ? false : true;
}

if ($('.main-containt').find('#topupCreate').length) {

    function getWallets(virtualCard) {
        return new Promise((resolve, reject) => {
            let token = $('[name="_token"]').val();
            $.post(cardWalletUrl, {
                "_token": token,
                'virtualCard': virtualCard,
            })
            .done((response) => {
                let options = '';
                if (response.success != '') {
                    options = `<option value="${response.success}" ${response.success == selectedWallet ? 'selected' : ''}>${response.success + " " + wallet}</option>`;
                    if (response.success != null) {
                        $('#display_topup_wallet').html(options);
                    }
                    $('#topup_wallet').val(response.success);
                    $('#paymentMethodSection').removeClass('d-none');
                    $('#paymentMethodEmpty').addClass('d-none');
                    $('#topupCreateSubmitBtn').removeClass('d-none');
                    checkAmountLimitAndFeesLimit();
                    resolve();
                } else {
                    $('#display_topup_wallet').val('');
                    $('#topup_wallet').val('');
                    $("#percentage_fee").val(0.00);
                    $("#fixed_fee").val(0.00);
                    $("#total_fee").val(0.00);
                    $('.pFees').html('0');
                    $('.fFees').html('0');
                    $(".total_fees").html('0.00');
                    $('#paymentMethodSection').addClass('d-none');
                    $('#paymentMethodEmpty').removeClass('d-none');
                    $('#topupCreateSubmitBtn').addClass('d-none');
                    resolve();
                }
            })
            .fail((error) => {
                reject(error);
            });
        });
    }

    $(window).on('load', function() {
        determineDecimalPoint();
        var virtualCard = $('#virtualCard').val();
        var displayTopupWallet = $('#display_topup_wallet option:selected').val();
        $('#topup_wallet').val(displayTopupWallet);
        setTimeout(function() {
            getWallets(virtualCard);
        }, 300);
    });

    var lastCurrencyType, currentCurrencyType;
    $('select').change(function() {
        lastCurrencyType = $(this).attr('data-old') !== typeof undefined ? $(this).attr('data-old') : "";
        currentCurrencyType = $("option:selected", this).data('type');
        $(this).attr('data-old', currentCurrencyType)
    }).change();

    // Code for Fees Limit check
    $(document).on('change', '#virtualCard', function() {
        if (lastCurrencyType !== currentCurrencyType) {
            $('#amount').val('');
        }
        $('.amount-limit-error').text('');
        determineDecimalPoint();
        var virtualCard = $('#virtualCard').val();
        getWallets(virtualCard);
    });

    $(document).on('change', '#display_topup_wallet', function() {
        $('#topup_wallet').val($(this).val());
    });

    //Fees Limit check on payment method change
    $(document).on('change', '#topup_wallet', function() {
        checkAmountLimitAndFeesLimit();
    });

    //Fees Limit check on amount input
    $(document).on('input', '#amount', $.debounce(1000, function() {
        checkAmountLimitAndFeesLimit();
    }));

    function checkAmountLimitAndFeesLimit() {
        var token = $('[name="_token"]').val();
        var amount = $('#amount').val().trim();
        var virtualCard = $('#virtualCard').val();
        var topup_wallet = $('#topup_wallet').val();

        if (amount != '') {
            $.post({
                url: feesLimitUrl,
                dataType: "json",
                data: {
                    "_token": token,
                    'amount': amount,
                    'virtualCard': virtualCard,
                    'topup_wallet': topup_wallet
                }
            }).done(function(response) {
                if (response.success.status == 200) {
                    $("#percentage_fee").val(response.success.feesPercentage);
                    $("#fixed_fee").val(response.success.feesFixed);
                    $("#total_fee").val(response.success.totalFees);

                    $(".total_fees").html(response.success.formattedTotalFees);
                    $('.pFees').html(response.success.formattedFeesPercentage);
                    $('.fFees').html(response.success.formattedFeesFixed);

                    $('.amountLimit').text('');
                    $('#topupCreateSubmitBtn').attr('disabled', false);
                } else {
                    if (amount == '') {
                        $('.amountLimit').text('');
                        $('#topupCreateSubmitBtn').attr('disabled', false);
                    } else {
                        $('.amountLimit').text(response.success.message);
                        $('#topupCreateSubmitBtn').attr('disabled', true);
                        return false;
                    }
                }
            });
        }
    }

    $('#topupCreateForm').on('submit', function() {
        $(".spinner").removeClass('d-none');
        $("#rightAngleSvgIcon").addClass('d-none');
        $("#topupCreateSubmitBtn").attr("disabled", true);
        $("#topupCreateSubmitBtnText").text(submitBtnText);
    });

    $(function() {
        $("#virtualCard").select2({
            templateResult: formatOption,
            templateSelection: formatOption
        });

        function formatOption(option) {
            if (!option.id) {
                return option.text;
            }

            var icon = $(option.element).data("icon");
            return $('<span>' + icon + ' ' + option.text + '</span>');
        }
    });
}

if ($('.main-containt').find('#topupCreateSingle').length) {

    function getWallets(virtualCard) {
        return new Promise((resolve, reject) => {
            let token = $('[name="_token"]').val();
            $.post(cardWalletUrl, {
                "_token": token,
                'virtualCard': virtualCard,
            })
            .done((response) => {
                let options = '';
                if (response.success != '') {
                    options = `<option value="${response.success}" ${response.success == selectedWallet ? 'selected' : ''}>${response.success + " " + wallet}</option>`;
                    if (response.success != null) {
                        $('#display_topup_wallet').html(options);
                    }
                    $('#topup_wallet').val(response.success);
                    $('#paymentMethodSection').removeClass('d-none');
                    $('#paymentMethodEmpty').addClass('d-none');
                    $('#topupCreateSubmitBtn').removeClass('d-none');
                    checkAmountLimitAndFeesLimit();
                    resolve();
                } else {
                    $('#display_topup_wallet').val('');
                    $('#topup_wallet').val('');
                    $("#percentage_fee").val(0.00);
                    $("#fixed_fee").val(0.00);
                    $("#total_fee").val(0.00);
                    $('.pFees').html('0');
                    $('.fFees').html('0');
                    $(".total_fees").html('0.00');
                    $('#paymentMethodSection').addClass('d-none');
                    $('#paymentMethodEmpty').removeClass('d-none');
                    $('#topupCreateSubmitBtn').addClass('d-none');
                    resolve();
                }
            })
            .fail((error) => {
                reject(error);
            });
        });
    }

    $(window).on('load', function() {
        determineDecimalPoint();
        var virtualCard = $('#virtualCard').val();
        var displayTopupWallet = $('#display_topup_wallet option:selected').val();
        $('#topup_wallet').val(displayTopupWallet);
        setTimeout(function() {
            getWallets(virtualCard);
        }, 300);
    });

    var lastCurrencyType, currentCurrencyType;
    $('select').change(function() {
        lastCurrencyType = $(this).attr('data-old') !== typeof undefined ? $(this).attr('data-old') : "";
        currentCurrencyType = $("option:selected", this).data('type');
        $(this).attr('data-old', currentCurrencyType)
    }).change();

    $(document).on('input', '#amount', $.debounce(1000, function() {
        checkAmountLimitAndFeesLimit();
    }));

    function checkAmountLimitAndFeesLimit() {
        var token = $('[name="_token"]').val();
        var amount = $('#amount').val().trim();
        var virtualCard = $('#virtualCard').val();
        var topup_wallet = $('#topup_wallet').val();

        if (amount != '') {
            $.post({
                url: feesLimitUrl,
                dataType: "json",
                data: {
                    "_token": token,
                    'amount': amount,
                    'virtualCard': virtualCard,
                    'topup_wallet': topup_wallet
                }
            }).done(function(response) {
                if (response.success.status == 200) {
                    $("#percentage_fee").val(response.success.feesPercentage);
                    $("#fixed_fee").val(response.success.feesFixed);
                    $("#total_fee").val(response.success.totalFees);

                    $(".total_fees").html(response.success.formattedTotalFees);
                    $('.pFees').html(response.success.formattedFeesPercentage);
                    $('.fFees').html(response.success.formattedFeesFixed);

                    $('.amountLimit').text('');
                    $('#topupCreateSubmitBtn').attr('disabled', false);
                } else {
                    if (amount == '') {
                        $('.amountLimit').text('');
                        $('#topupCreateSubmitBtn').attr('disabled', false);
                    } else {
                        $('.amountLimit').text(response.success.message);
                        $('#topupCreateSubmitBtn').attr('disabled', true);
                        return false;
                    }
                }
            });
        }
    }

    $('#topupCreateSingleForm').on('submit', function() {
        $(".spinner").removeClass('d-none');
        $("#rightAngleSvgIcon").addClass('d-none');
        $("#topupCreateSubmitBtn").attr("disabled", true);
        $("#topupCreateSubmitBtnText").text(submitBtnText);
    });
}

if ($('.main-containt').find('#topupConfirm').length) {
    $('#topupConfirmForm').on('submit', function () {
        $('#topupConfirmBtn').attr("disabled", true);
        $('#topupConfirmBackBtn').removeAttr('href');
    	$(".spinner").removeClass('d-none');
        $("#rightAngleSvgIcon").addClass('d-none');
    	$('#topupConfirmBtnText').text(confirmBtnText);

        setTimeout(function() {
            $('#topupConfirmBtn').attr("disabled", false);
    	    $(".spinner").addClass('d-none');
            $("#rightAngleSvgIcon").addClass('d-none');
    	    $('#topupConfirmBtnText').text(pretext);
        }, 5000);

    });
}
