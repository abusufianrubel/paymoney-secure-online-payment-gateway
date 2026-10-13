'use strict';

if ($('.main-containt').find('#VirtualcardCreate').length) {

    $(function () {
        let preferredCurrency = $('#preferredCurrency option:first').val();
        let cardHolderId = $('#virtualcardHolderId option:first').val();
        getCardCreationFee(preferredCurrency, cardHolderId);

        $('#virtualcardHolderId').select2({
            templateResult: formatOption,
            templateSelection: formatSelection,
            escapeMarkup: function (markup) {
                return markup;
            }
        });

        function formatOption(option) {
            if (!option.id) return option.text;
            let userName = $(option.element).data('name');
            let address = $(option.element).data('address');
            return `<div>${userName} <br> <small>${address}</small></div>`;
        }

        function formatSelection(option) {
            if (!option.id) return option.text;
            return $(option.element).data('name');
        }
    });

    $('#preferredCurrency').on('change', function() {
        getCardCreationFee($(this).val(), $('#virtualcardHolderId').val());
    });

    $('#virtualcardHolderId').on('change', function() {
        getCardCreationFee($('#preferredCurrency').val(), $(this).val());
    });

    function getCardCreationFee(preferredCurrency, cardHolderId)
    {
        fetch(cardCreationFeeUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                _token,
                preferredCurrency: preferredCurrency,
                cardHolderId: cardHolderId
            })
        })
        .then(response => response.json())
        .then(data => {

            if (data.success) {
                $('#cardCreationFee').text(data.message);
                $('#virtualcardFormSubmitBtn').removeClass('d-none');
                $('#preferredCurrencyEmpty').addClass('d-none');

            } else {
                if (data.message != '') {
                    $('#cardCreationFee').text(data.message);
                } else {
                    $('#preferredCurrencyEmpty').removeClass('d-none');
                }
                $('#virtualcardFormSubmitBtn').addClass('d-none');
            }

        })
        .finally(() => {
            $("#virtualcardFormSubmitBtnText").text(buttonText);
            $(".spinner").addClass('d-none');
            $("#virtualcardFormSubmitBtn").attr("disabled", false);
        });
    }

    $(document).on('submit', '#virtualcardForm', function() {
        $("#virtualcardFormSubmitBtn").attr("disabled", true);
        $(".spinner").removeClass('d-none');
        $("#virtualcardFormSubmitBtnText").text(submitBtnText);
    });
}  

if ($('.main-containt').find('#VirtualcardIndex').length) {

  $(function () {
      $(".filter-panel").css('display', 'block');
  });

  document.querySelector('#virtualcardForm').addEventListener('submit', function (e) {
      e.preventDefault();
      const params = new URLSearchParams(new FormData(this));
      const url = `${filterUrl}?${params.toString().replace(/%5B/g, '[').replace(/%5D/g, ']')}`;
      window.location.href = url;
  });

}
