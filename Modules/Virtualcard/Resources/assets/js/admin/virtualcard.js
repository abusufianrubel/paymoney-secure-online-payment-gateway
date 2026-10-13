'use strict';

function restrictNumberToPrefdecimalOnInput(e) {
    restrictNumberToPrefdecimal(e, currency.type);
}

function formatNumberToPrefDecimal(num = 0)
{
    let decimal_format = (currency.type == 'fiat') ? decimalFormate : caryptoFormate;
    return ((Math.abs(num)).toFixed(decimal_format))
}

// Index
if ($('.content').find('#index-section').length) {

    document.querySelector('form').addEventListener('submit', function (e) {
        e.preventDefault();
        const params = new URLSearchParams(new FormData(this));
        const url = `${SITE_URL}/${ADMIN_PREFIX}/virtualcards?${params.toString().replace(/%5B/g, '[').replace(/%5D/g, ']')}`;
        window.location.href = url;
    });
    
    $('#csv').on('click', function(event)
    {
        event.preventDefault();
    
        var startfrom = $('#startfrom').val();
        var endto = $('#endto').val();
        var brand = $('#brand').val();
        var currency = $('#currency').val();
        var status = $('#status').val();
        
        
        window.location = SITE_URL+"/"+ADMIN_PREFIX+"/virtualcards/csv?filter[from]="+startfrom
                            +"&filter[to]="+endto
                            +"&filter[brand]="+brand
                            +"&filter[currency]="+currency
                            +"&filter[status]="+status;
    });
    
    
    $('#pdf').on('click', function(event)
    {
    
        var startfrom = $('#startfrom').val();
        var endto = $('#endto').val();
        var brand = $('#brand').val();
        var currency = $('#currency').val();
        var status = $('#status').val();
        
        
        window.location = SITE_URL+"/"+ADMIN_PREFIX+"/virtualcards/pdf?filter[from]="+startfrom
                            +"&filter[to]="+endto
                            +"&filter[brand]="+brand
                            +"&filter[currency]="+currency
                            +"&filter[status]="+status;
    
    });
}

// Edit
if ($('.content').find('#edit-section').length) {

    let providerId = null;
    let oldVirtualcardCurrencyCode = '';

    function restrictNumberToPrefdecimalOnInput(e) {
        var type = $('select#currencyCode').find(':selected').data('type')
        restrictNumberToPrefdecimal(e, type);
    }

    function formatNumberToPrefDecimal(num = 0)
    {
        var type = $('select#currencyCode').find(':selected').data('type');  
        let decimal_format = (type == 'fiat') ? decimalFormate : caryptoFormate;

        return ((Math.abs(num)).toFixed(decimal_format))
    }

    function getProviderCurrencies(providerId, oldVirtualcardCurrencyCode) {
        return new Promise((resolve, reject) => {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "GET",
                url: ADMIN_URL + "/virtualcard/providers/" + providerId + "/get-currencies",
                dataType: 'json',
                success: function(response) {
                    let options = '';
                    $.each(response.currencies, (index, value) => {
                        options += `<option value="${value.code}" data-type="${value.type}" ${value.code == oldVirtualcardCurrencyCode ? 'selected' : ''}>${value.code}</option>`;
                    });
                    $('#currencyCode').html(options);
                    resolve(response);
                },
                error: function(error) {
                    reject(error);
                }
            });
        });
    }

    $('#virtualcardProviderId').on('change', function() {
        providerId = $(this).val();
        oldVirtualcardCurrencyCode = $('#oldVirtualcardCurrencyCode').val();
        getProviderCurrencies(providerId, oldVirtualcardCurrencyCode);
    });

    $('#cardExpiryDate').daterangepicker({
        "singleDatePicker": true,
        locale: {
            format: 'DD-M-YYYY'
        }
    });

    $(document).on('submit', '#virtualCardUpdateBtnForm', function() {
        $("#virtualCardUpdateBtn").attr("disabled", true);
        $(".fa-spinner").removeClass('d-none');
        $("#virtualCardUpdateBtnText").text(submitButtonText);
    });

    $(function () {
        const $cardInput = $("#cardNumber");
        const $cardLogo = $("#cardLogo");

        // Card detection logic
        function getCardType(number) {
            const cardPatterns = {
                visa: /^4/,
                mastercard: /^5[1-5]/,
                amex: /^3[47]/,
                discover: /^6(?:011|5)/,
                diners: /^3(?:0[0-5]|[68])/,
                jcb: /^(?:2131|1800|35)/,
                unionpay: /^(62)/,
            };

            for (const [card, pattern] of Object.entries(cardPatterns)) {
                if (pattern.test(number)) return card;
            }
            return "";
        }

        // Input event listener
        $cardInput.on("input", function () {
            let value = $cardInput.val().replace(/\D/g, ""); // Remove non-numeric
            value = value.replace(/(.{4})/g, "$1 ").trim(); // Add space every 4 digits
            $cardInput.val(value);

            // Detect card type
            const cardType = getCardType(value.replace(/\s/g, ""));

            if (cardType && logoUrls[cardType]) {
                $cardLogo.attr("src", logoUrls[cardType]).show(); // Show logo
                $cardInput.css("padding-left", "55px"); // Adjust padding for logo space
            } else {
                $cardLogo.hide(); // Hide logo
                $cardInput.css("padding-left", "15px"); // Reset padding when no card type detected
            }
        });

        $cardInput.trigger("input");
    });

    $(window).on('load', function() {
        providerId = $('#virtualcardProviderId').val();
        oldVirtualcardCurrencyCode = $('#oldVirtualcardCurrencyCode').val();
        
        getProviderCurrencies(providerId, oldVirtualcardCurrencyCode).then(() => {
            var amount = formatNumberToPrefDecimal($('#amount').val());
            var cardSpendingLimit = formatNumberToPrefDecimal($('#cardSpendingLimit').val());
            $('#amount').val(amount);
            $('#cardSpendingLimit').val(cardSpendingLimit);
        })
    });
}

// Show
if ($('.content').find('#show-section').length) {

    $('.card-warning').on('click', function(e){
        e.preventDefault();
        let url = $(this).attr('href');
        let titleText = $(this).text();

        if (titleText == 'Decline') {
            $('.modal-title').text(declineTitle);
            $('.modal-text').text(declineText);
        } else {
            $('.modal-title').text(approveTitle);
            $('.modal-text').text(approveText);
        }
    
        $('#card-modal-yes').attr('href', url);
        $('#card-warning-modal').modal('show');
    });

    // Card Action - Active/Deactive Functionality
    $(document).on("click", ".card-action", function (e) {
        e.preventDefault();

        let action = $(this).attr('data-action');
        let cardId = $(this).attr('data-cardid');

        let actionText = action == 'active' ? actionActiveText : actionDeactiveText;
        let actionButtonText = action == 'active' ? actionActiveButtonText : actionDeactiveButtonText;

        Swal.fire({
            title: actionTitle,
            text: actionText,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#635BFF",
            cancelButtonColor: "#DF3B3B",
            confirmButtonText: actionButtonText
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: cardActionUrl,
                    type: "POST",
                    data: {
                        action: action,
                        virtuacardId: cardId
                    },
                    beforeSend: function() {
                        Swal.fire({
                            title: pleaseWaitText,
                            text: loadingText,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false, 
                            willOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function (response) {
                        if (response.status) {
                            Swal.fire({
                                title: response.title,
                                text: response.message,
                                icon: "success"
                            }).then(() => {
                                location.reload(); 
                            });
                        } else {
                            Swal.fire(errorText, response.message, "error");
                        }
                    },
                    error: function (xhr) {
                        Swal.fire(somethingWentWrongText, "error");
                    }
                });
            }
        });
    });

    // Get the virtual card ID
    const url = new URL(window.location.href);
    const pathSegments = url.pathname.split('/');
    const virtualcardId = pathSegments.pop();

    let cardSpendingLimit = [];

    // Get the spending limits for the virtual card
    fetch(spendingControlLimitUrl+`?virtualcardId=${virtualcardId}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data) {
            cardSpendingLimit = data;
            renderSpendingLimits();
        } else {
            Swal.fire({
                title: errorText,
                text: unexpectedErrorText,
                icon: "error"
            }).then(() => {
                location.reload(); 
            });
        }
    })
    .catch(error => {
        Swal.fire({
            title: errorText,
            text: unexpectedErrorText,
            icon: "error"
        }).then(() => {
            location.reload(); 
        });
    });
    
    // Get all the selectors
    const spendingLimitContainer = document.querySelector('.spendingLimits');
    const amountInput = document.getElementById('amount');
    const intervalSelect = document.getElementById('interval');
    const addControlButton = document.querySelector('.btn.btn-secondary');
    const spendingControlBtn = document.querySelector('.spendingControlBtn'); 

    const SpendingIntervals = {
        EVERY_AUTHORIZATION: "per_authorization",
        EVERY_DAY: "daily",
        EVERY_WEEK: "weekly",
        EVERY_MONTH: "monthly",
        EVERY_YEAR: "yearly",
        ALL_TIME: "all_time"
    };

    // Function to format the spending limit list item
    function formatListItem(limit) {
        let limitItemText = "";
        
        switch (limit.interval) {
            case SpendingIntervals.EVERY_DAY:
                limitItemText = `💳${limitText} | <strong>${currency.symbol} ${parseFloat(limit.amount).toFixed(2)}</strong> ${limitPerText} ${dayText}`;
                break;
    
            case SpendingIntervals.EVERY_WEEK:
                limitItemText = `💳${limitText} | <strong>${currency.symbol} ${parseFloat(limit.amount).toFixed(2)}</strong> ${limitPerText} ${weekText}`;
                break;
    
            case SpendingIntervals.EVERY_MONTH:
                limitItemText = `💳${limitText} | <strong>${currency.symbol} ${parseFloat(limit.amount).toFixed(2)}</strong> ${limitPerText} ${monthText}`;
                break;
    
            case SpendingIntervals.EVERY_YEAR:
                limitItemText = `💳${limitText} | <strong>${currency.symbol} ${parseFloat(limit.amount).toFixed(2)}</strong> ${limitPerText} ${yearText}`;
                break;
    
            case SpendingIntervals.ALL_TIME:
                limitItemText = `💳${limitText} | <strong>${currency.symbol} ${parseFloat(limit.amount).toFixed(2)}</strong> ${limitInText} ${totalText}`;
                break;
    
            case SpendingIntervals.EVERY_AUTHORIZATION:
                limitItemText = `💳${limitText} | <strong>${currency.symbol} ${parseFloat(limit.amount).toFixed(2)}</strong> ${limitPerText} ${authorizationText}`;
                break;
    
            default:
                limitItemText = `💳${limitText} | <strong>${currency.symbol} ${parseFloat(limit.amount).toFixed(2)}</strong> ${limitPerText} ${authorizationText}`;
                break;
        }
    
        return limitItemText;
    }

    // Function to render the spending limits list
    function renderSpendingLimits() {

        // Clear existing list before re-rendering
        spendingLimitContainer.innerHTML = ''; 
    
        cardSpendingLimit.forEach((limit, index) => {
            spendingLimitContainer.innerHTML += `
                <li class="list-group-item d-flex align-items-center f-14" data-index="${index}">
                    <div class="flex-grow-1">
                    ${formatListItem(limit)}
                    </div>
                    <button class="btn-close" aria-label="Close"></button>
                </li>
            `;
        });
    }
    
    // Removing a spending limit
    spendingLimitContainer.addEventListener('click', function (event) {
        if (event.target.classList.contains('btn-close')) {
            const index = event.target.closest('li').getAttribute('data-index');
            cardSpendingLimit.splice(index, 1);
            renderSpendingLimits(); 
        }
    });
    
    // Function to add a new spending limit
    addControlButton.addEventListener('click', function (event) {
        event.preventDefault();
    
        const amountValue = amountInput.value.trim();
        const intervalValue = intervalSelect.value;
    
        // Validate input (ensure amount is not empty or negative)
        if (!amountValue || parseFloat(amountValue) <= 0) {
            alert(validAmountText);
            return;
        }

        // Request for duplicate checking
        fetch(spendingControlExistCheckUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                _token,
                amountValue,
                intervalValue,
                virtualcardId,
                cardSpendingLimit
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status) {
                $('.spendingControlError').text(data.message);
                $('.spendingControlError').removeClass('d-none');
                $('.spendingControlBtn').attr("disabled", true);

            } else {
                $('.spendingControlError').text('');
                $('.spendingControlError').addClass('d-none');
                $('.spendingControlBtn').attr("disabled", false);

                 // Format the amount with a currency symbol (assuming USD, modify as needed)
                const formattedAmount = `${parseFloat(amountValue).toFixed(2)}`;
            
                // Add the new spending limit to the array
                cardSpendingLimit.push({ amount: formattedAmount, interval: intervalValue });
            
                // Re-render the list
                renderSpendingLimits();
            
                // Clear input fields after adding
                amountInput.value = '';
                intervalSelect.selectedIndex = 0;
            }
        });

    }); 

    // Save the spending limits to the database
    spendingControlBtn.addEventListener('click', function (event) {
        event.preventDefault();

        $('.fa-spin').removeClass('d-none');
        $(this).attr("disabled", true);
        $('.spendingControlBtnText').text(updatingText);

        fetch(spendingControlLimitUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                _token,
                virtualcardId: virtualcardId,
                cardSpendingLimit: cardSpendingLimit
            })
        }).then(response => response.json())
        .then(data => {
            if (data.status) {
       
                $('.spendingControlError').text('');
                $('.spendingControlError').addClass('d-none')

                Swal.fire({
                    title: data.title,
                    text: data.message,
                    icon: "success"
                }).then(() => {
                    location.reload(); 
                });

            } else {
                $('.spendingControlError').text(data.message);
                $('.spendingControlError').removeClass('d-none');
            }
        })
        .catch(error => {
            Swal.fire({
                title: errorText,
                text: unexpectedErrorText,
                icon: "error"
            });
        })
        .finally(() => {
            $('.fa-spin').addClass('d-none');
            $(this).attr("disabled", false);
            $('.spendingControlBtnText').text(saveChangeText);
        });
    });
}
