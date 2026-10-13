'use strict';

function restrictNumberToPrefdecimalOnInput(e) {
    var type = $('select#withdrawalWallet').find(':selected').data('type')
    restrictNumberToPrefdecimal(e, type);
}

// Create Section
if ($('.main-containt').find('#WithdrawalCreate').length) {

    let virtualcardId = '';

    function getWithdrawalWallet(virtualcardId)
    {
        return new Promise((resolve, reject) => {
            $.ajax({
                method: 'GET',
                url: getWithdrawalWalletUrl,
                data: {
                    'virtualcardId': virtualcardId,
                },
                dataType: "json",
            }).done(function (response) {
                let options = '';
                $.map(response.wallets, function (wallet) {
                    options += `<option value="${wallet.wallet_id}" ${wallet.wallet_id == withdrawalWalletId ? 'selected' : ''} data-type="${wallet.type}">${wallet.code}</option>`;
                });
                if (response.wallets.length > 0) {
                    $('#withdrawalWallet').html(options);
                }
                resolve(response);
            }).fail(function (error) {
                const errorMessage = JSON.parse(error.responseText).message;
                Swal.fire(failedText, errorMessage, 'error').then((result) => {
                    if (result.isConfirmed) {
                        window.location.reload();
                    }
                });
                reject(new Error(errorMessage));
            });
        });
    }

    function retrieveFeesLimit(cardId, amount, withdrawalWalletId)
    {
        
        $.ajax({
            method: 'POST',
            url: retrieveFeesLimitUrl,
            data: {
                '_token': token,
                'userId': userId,
                'virtualcardId': cardId,
                'requestedFund': amount,
                'withdrawalWallet': withdrawalWalletId,
            },
            dataType: "json",
        }).done(function (data) {
            if (data.success.status == 200) {
                $("#percentage_fee").val(data.success.response.feesPercentage);
                $("#fixed_fee").val(data.success.response.feesFixed);
                $("#total_fee").val(data.success.response.totalFees);

                $(".total_fees").html(data.success.response.formattedTotalFees);
                $('.pFees').html(data.success.response.formattedFeesPercentage);
                $('.fFees').html(data.success.response.formattedFeesFixed);
            }
        });
        
    }

    function checkAmountValidation(cardId, amount, withdrawalWalletId)
    {
        $.post({
            url: checkAmountLimitUrl,
            dataType: "json",
            data: {
                '_token': token,
                'userId': userId,
                'virtualcardId': cardId,
                'requestedFund': amount,
                'withdrawalWallet': withdrawalWalletId,
            }
        }).done(function(response) {
            if (response.status == 200) {
                $('.amountLimit').text('');
                $('#withdrawalButton').attr('disabled', false);
            } else {
                $('.amountLimit').text(response.message);
                $('#withdrawalButton').attr('disabled', true);
            }
        });
        
    }

    $(function () {
        $("#virtualcardId").select2({
            templateResult: formatOption,
            templateSelection: formatOption,
            escapeMarkup: function (markup) {
                return markup;
            }
        });

        function formatOption(option) {
            if (!option.id) {
                return option.text;
            }
            var icon = $(option.element).data("icon"); 
            return $('<span>' + icon + ' ' + option.text + '</span>');
        }
    });

    $('#withdrawalCreateForm').on('submit', function() {
        $(".spinner").removeClass('d-none');
        $("#rightAngleSvgIcon").addClass('d-none');
        $("#withdrawalButton").attr("disabled", true);
        $("#withdrawalButtonText").text(submitBtnText);
    });

    $('#withdrawalConfirmForm').on('submit', function () {
        $('#withdrawalConfirmBtn').attr("disabled", true);
        $('#withdrawalConfirmBackBtn').removeAttr('href');
        $(".spinner").removeClass('d-none');
        $("#rightAngleSvgIcon").addClass('d-none');
        $('#withdrawalConfirmBtnText').text(submitBtnText);

        setTimeout(function() {
            $('#withdrawalConfirmBtn').attr("disabled", false);
            $(".spinner").addClass('d-none');
            $("#rightAngleSvgIcon").addClass('d-none');
            $('#withdrawalConfirmBtnText').text(submitBtnText);
        }, 300);

    });

    $('#requestedFund').on('input', $.debounce(1000, function() {

        let amount = $(this).val().trim();
        let withdrawalWalletId = $('#withdrawalWallet').val();
        let cardId = $('#virtualcardId').val();

        if (amount == '' || isNaN(amount) || amount <= 0) {
            amount = 0;
        }

        retrieveFeesLimit(cardId, amount, withdrawalWalletId);
        checkAmountValidation(cardId, amount, withdrawalWalletId);
    }));

    $(window).on('load', function () {

        virtualcardId = $('#virtualcardId').val();
        
        getWithdrawalWallet(virtualcardId)
        .then(response => {

            let amount = $('#requestedFund').val().trim();
            let withdrawalWalletId = $('#withdrawalWallet').val();

            if (amount == '' || isNaN(amount) || amount <= 0) {
                amount = 0;
            }

            retrieveFeesLimit(virtualcardId, amount, withdrawalWalletId);
        });
    });

    $('#virtualcardId').on('change', function() {
        virtualcardId = $(this).val();
        getWithdrawalWallet(virtualcardId);
    });
}

if ($('.main-containt').find('#WithdrawalCreateSingle').length) {

    function checkAmountValidation(cardId, amount, withdrawalWalletId)
    {
        $.post({
            url: checkAmountLimitUrl,
            dataType: "json",
            data: {
                '_token': token,
                'userId': userId,
                'virtualcardId': cardId,
                'requestedFund': amount,
                'withdrawalWallet': withdrawalWalletId,
            }
        }).done(function(response) {
            if (response.status == 200) {
                $('.amountLimit').text('');
                $('#withdrawalButton').attr('disabled', false);
            } else {
                $('.amountLimit').text(response.message);
                $('#withdrawalButton').attr('disabled', true);
            }
        });
        
    }

    function getWithdrawalWallet(virtualcardId)
    {
        return new Promise((resolve, reject) => {
            $.ajax({
                method: 'GET',
                url: getWithdrawalWalletUrl,
                data: {
                    'virtualcardId': virtualcardId,
                },
                dataType: "json",
            }).done(function (response) {
                let options = '';
                $.map(response.wallets, function (wallet) {
                    options += `<option value="${wallet.wallet_id}" ${wallet.wallet_id == withdrawalWalletId ? 'selected' : ''} data-type="${wallet.type}">${wallet.code}</option>`;
                });
                if (response.wallets.length > 0) {
                    $('#withdrawalWallet').html(options);
                }
                resolve(response);
            }).fail(function (error) {
                const errorMessage = JSON.parse(error.responseText).message;
                Swal.fire(failedText, errorMessage, 'error').then((result) => {
                    if (result.isConfirmed) {
                        window.location.reload();
                    }
                });
                reject(new Error(errorMessage));
            });
        });
    }

    function retrieveFeesLimit(cardId, amount, withdrawalWalletId)
    {
        
        $.ajax({
            method: 'POST',
            url: retrieveFeesLimitUrl,
            data: {
                '_token': token,
                'userId': userId,
                'virtualcardId': cardId,
                'requestedFund': amount,
                'withdrawalWallet': withdrawalWalletId,
            },
            dataType: "json",
        }).done(function (data) {
            if (data.success.status == 200) {
                $("#percentage_fee").val(data.success.response.feesPercentage);
                $("#fixed_fee").val(data.success.response.feesFixed);
                $("#total_fee").val(data.success.response.totalFees);

                $(".total_fees").html(data.success.response.formattedTotalFees);
                $('.pFees').html(data.success.response.formattedFeesPercentage);
                $('.fFees').html(data.success.response.formattedFeesFixed);
            }
        });
        
    }

    $(window).on('load', function () {
    
        let virtualcardId = '';

        virtualcardId = $('#virtualcardId').val();
        
        getWithdrawalWallet(virtualcardId)
        .then(response => {

            let amount = $('#requestedFund').val().trim();
            let withdrawalWalletId = $('#withdrawalWallet').val();

            if (amount == '' || isNaN(amount) || amount <= 0) {
                amount = 0;
            }

            retrieveFeesLimit(virtualcardId, amount, withdrawalWalletId);
        });
    });

    $('#requestedFund').on('input', $.debounce(1000, function() {

        let amount = $(this).val().trim();
        let withdrawalWalletId = $('#withdrawalWallet').val();
        let cardId = $('#virtualcardId').val();

        if (amount == '' || isNaN(amount) || amount <= 0) {
            amount = 0;
        }

        retrieveFeesLimit(cardId, amount, withdrawalWalletId);
        checkAmountValidation(cardId, amount, withdrawalWalletId);
    }));

    $('#withdrawalCreateSingleForm').on('submit', function() {
        $(".spinner").removeClass('d-none');
        $("#rightAngleSvgIcon").addClass('d-none');
        $("#withdrawalButton").attr("disabled", true);
        $("#withdrawalButtonText").text(submitBtnText);
    });

    $('#withdrawalConfirmForm').on('submit', function () {
        $('#withdrawalConfirmBtn').attr("disabled", true);
        $('#withdrawalConfirmBackBtn').removeAttr('href');
        $(".spinner").removeClass('d-none');
        $("#rightAngleSvgIcon").addClass('d-none');
        $('#withdrawalConfirmBtnText').text(submitBtnText);

        setTimeout(function() {
            $('#withdrawalConfirmBtn').attr("disabled", false);
            $(".spinner").addClass('d-none');
            $("#rightAngleSvgIcon").addClass('d-none');
            $('#withdrawalConfirmBtnText').text(submitBtnText);
        }, 300);

    });
}

// Confirm Section
if ($('.main-containt').find('#WithdrawalConfirm').length) {

    $('#withdrawalConfirmForm').on('submit', function () {
        $('#withdrawalConfirmBtn').attr("disabled", true);
        $('#withdrawalConfirmBackBtn').removeAttr('href');
        $(".spinner").removeClass('d-none');
        $("#rightAngleSvgIcon").addClass('d-none');
        $('#withdrawalConfirmBtnText').text(submitBtnText);
    
        setTimeout(function() {
            $('#withdrawalConfirmBtn').attr("disabled", false);
            $(".spinner").addClass('d-none');
            $("#rightAngleSvgIcon").addClass('d-none');
            $('#withdrawalConfirmBtnText').text(submitBtnText);
        }, 3000);
    });
}