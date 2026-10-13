'use strict';

var isMasked = 'yes';
const modalElement = document.getElementById('exampleModal');
const modalInstance = new bootstrap.Modal(modalElement);
// Define the SVG icons
let eyeOnIcon = $('.eye-on');
let eyeOffIcon = $('.eye-off');
let loaderIcon = $('.loaderIcon');
$(eyeOffIcon).show();
let $btn = $("#sendOtpBtn");

$('#sendOtpBtn').on('click', function () {
    $(loaderIcon).removeClass('d-none'); 
    $(eyeOffIcon).addClass('d-none');

    fetch(sendOtpUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            virtualcardId: virtualcardId,
            _token
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            modalInstance.show(); 
        } else {
            swal({
                title: failedTitle,
                text: failedText,
                type: "error"
            });
            $(eyeOnIcon).removeClass('d-none');
            $(loaderIcon).addClass('d-none');
        }
    })
    .catch(error => {
        swal({
            title: failedTitle,
            text: errorText,
            type: "error"
        });
        
        $(eyeOnIcon).removeClass('d-none');
        $(loaderIcon).addClass('d-none');
    })
    .finally(() => {
        $btn.attr("disabled", false);
    });
});

// When modal is close without verifying
document.getElementById('exampleModal').addEventListener('hidden.bs.modal', function () {
    $(loaderIcon).addClass('d-none');
    if (isMasked == 'yes') {
        $(eyeOffIcon).removeClass('d-none');
    } else {
        $(eyeOnIcon).removeClass('d-none');
    }
});

// Check if the card is globally unmasked (check localStorage)
if (localStorage.getItem('cardUnmasked_' + virtualcardId ) === 'true') {

    const storedCardNumber = localStorage.getItem('cardNumber_' + virtualcardId);
    const storedCvc = localStorage.getItem('cvc_' + virtualcardId);
    const storedCardId = localStorage.getItem('cardId_' + virtualcardId);
    
    if (storedCardNumber && storedCardId === virtualcardId) {
        unmaskCard(storedCardNumber, storedCvc, storedCardId);
        $(eyeOnIcon).removeClass('d-none');
        $(eyeOffIcon).addClass('d-none');
    }
}

// Handle OTP verification and unmasking card
$('#verifyOtpBtn').on('click', () => {

    $('.spinner').removeClass('d-none');
    const otp = $('#verificationCode').val();

    fetch(verifyOtpUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ otp, _token }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.virtualcardId == virtualcardId) {
                unmaskCard(data.cardNumber, data.cvc, data.virtualcardId);
            }
            modalInstance.hide();
            $('.otp-input').val('');

        } else {
            swal({
                title: failedTitle,
                text: data.message ? data.message : invalidOTP,
                type: "error"
            });

            $('.otp-input').val('');
        }
    })
    .catch(error => {
        swal({
            title: failedTitle,
            text: errorText,
            type: "error"
        });
    })
    .finally(() => {
        $('.spinner').addClass('d-none');
    });
});

// Unmask card globally with the card number from the backend
function unmaskCard(cardNumber, cvc, cardId) {
    
    if (cardUnmasked) return;

    // Display real card number and cc
    $('#card-number').text(formatCardNumber(cardNumber));
    $('#card-cvc').addClass('text-xs');
    $('#card-cvc').text(cvc);

    cardUnmasked = true;
    isMasked = 'no';
    // Store the unmasked state and card number in localStorage
    localStorage.setItem('cardUnmasked_' + virtualcardId, 'true');
    localStorage.setItem('cardNumber_' + virtualcardId, cardNumber);
    localStorage.setItem('cvc_' + virtualcardId, cvc);
    localStorage.setItem('cardId_' + virtualcardId, cardId);
    localStorage.setItem('isMasked_' + virtualcardId, 'no');

    // Set a timeout to mask the card after 1 minute (60000ms)
    maskTimeout = setTimeout(maskCard, 60000);
}

// Masked the card number by clicking on the icon
eyeOnIcon.on('click', function() {
    maskCard();
});

// Mask the card again after 1 minute
function maskCard() {
    $('#card-number').text(maskCardNumber(localStorage.getItem('cardNumber_' + virtualcardId)));
    $('#card-cvc').removeClass('text-xs');
    $('#card-cvc').text('*'.repeat(localStorage.getItem('cvc_' + virtualcardId).length));
    cardUnmasked = false;
    isMasked = 'yes';
    $(eyeOffIcon).removeClass('d-none');
    $(eyeOnIcon).addClass('d-none');
    // Remove the global unmasked flag from localStorage
    localStorage.removeItem('cardUnmasked_' + virtualcardId);
    localStorage.removeItem('cardNumber_' + virtualcardId);
    localStorage.removeItem('cardId_' + virtualcardId);
    localStorage.removeItem('cvc_' + virtualcardId);
    localStorage.removeItem('isMasked_' + virtualcardId);
}

function maskCardNumber(cardNumber) {
    
    cardNumber = String(cardNumber);
    if (cardNumber.length >= 4) {
        const masked = '*'.repeat(cardNumber.length - 4);
        const lastFour = cardNumber.slice(-4);
        return masked + lastFour;
    }

    return cardNumber;
}

function formatCardNumber(cardNumber) 
{
    cardNumber = String(cardNumber);

    // Use a regular expression to insert spaces every 4 digits
    return cardNumber.replace(/(\d{4})(?=\d)/g, '$1 ');
}


var otpInputs = document.querySelectorAll(".otp-input");

function setupOtpInputListeners(inputs) {
    inputs.forEach(function (input, index) {
        input.addEventListener("paste", function (ev) {
            var clip = ev.clipboardData.getData("text").trim();
            if (!/^\d{6}$/.test(clip)) {
                ev.preventDefault();
                return;
            }

            var characters = clip.split("");
                inputs.forEach(function (otpInput, i) {
                otpInput.value = characters[i] || "";
            });

            enableNextBox(inputs[0], 0);
            inputs[5].removeAttribute("disabled");
            inputs[5].focus();
            updateOTPValue(inputs);
        });

        input.addEventListener("input", function () {
            var currentIndex = Array.from(inputs).indexOf(this);
            var inputValue = this.value.trim();

            if (!/^\d$/.test(inputValue)) {
                this.value = "";
                return;
            }

            if (inputValue && currentIndex < 5) {
                inputs[currentIndex + 1].removeAttribute("disabled");
                inputs[currentIndex + 1].focus();
            }

            if (currentIndex === 4 && inputValue) {
                inputs[5].removeAttribute("disabled");
                inputs[5].focus();
            }

            updateOTPValue(inputs);
        });

        input.addEventListener("keydown", function (ev) {
            var currentIndex = Array.from(inputs).indexOf(this);

            if (!this.value && ev.key === "Backspace" && currentIndex > 0) {
            inputs[currentIndex - 1].focus();
            }
        });
    });
}

function updateOTPValue(inputs) {
    var otpValue = "";
    inputs.forEach(function (input) {
        otpValue += input.value;
    });

    if (inputs === otpInputs) {
        document.getElementById("verificationCode").value = otpValue;
    } 
}

// Setup listeners for OTP inputs
setupOtpInputListeners(otpInputs);

// Initial focus on first OTP input field
otpInputs[0].focus();