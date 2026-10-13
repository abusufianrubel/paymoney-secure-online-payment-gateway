'use strict';

$(document).ready(function() {
    $('#paymentMethodForm').on('submit', function() {

        const selectedValue = $('input[name="method"]:checked').val();
        localStorage.setItem('checkboxValue', selectedValue);

        $("#paymentMethodSubmitBtn").attr("disabled", true);
        $(".spinner").removeClass('d-none');
        $("#paymentMethodSubmitBtnText").text(paymentMethodSubmitBtnText);

        setTimeout(function () {
            $(".spinner").addClass('d-none');
            $("#paymentMethodSubmitBtn").attr("disabled", false);
            $("#paymentMethodSubmitBtnText").text(pretext);

        }, 2000);
    });

    var storedValue = localStorage.getItem('checkboxValue');
    
    if (storedValue) {
        localStorage.removeItem('checkboxValue');
        $('#'+storedValue).addClass('gateway-selected');
    } else {
        $('input[type="checkbox"]').first().prop('checked', true);
        $('.gateway').first().addClass('gateway-selected');
    }


});
