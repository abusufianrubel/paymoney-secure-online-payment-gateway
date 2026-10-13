'use strict';

if ($('.content').find('#withdrawalIndexBox').length) {

    document.querySelector('form').addEventListener('submit', function (e) {
        e.preventDefault();
        const params = new URLSearchParams(new FormData(this));
        const url = `${SITE_URL}/${ADMIN_PREFIX}/virtualcard/withdrawals?${params.toString().replace(/%5B/g, '[').replace(/%5D/g, ']')}`;
        window.location.href = url;
    });

    $('#csv').on('click', function(event)
    {
        event.preventDefault();

        var startfrom = $('#startfrom').val();
        var endto = $('#endto').val();
        var currency = $('#currency').val();
        var status = $('#status').val();
        var user = $('#user').val();
        
        
        window.location = SITE_URL+"/"+ADMIN_PREFIX+"/virtualcard/withdrawals/csv?filter[from]="+startfrom
                            +"&filter[to]="+endto
                            +"&filter[status]="+status
                            +"&filter[currency]="+currency
                            +"&filter[user]="+user;
    });
    
    $('#pdf').on('click', function(event)
    {
        event.preventDefault();

        var startfrom = $('#startfrom').val();
        var endto = $('#endto').val();
        var currency = $('#currency').val();
        var status = $('#status').val();
        var user = $('#user').val();
        
        
        window.location = SITE_URL+"/"+ADMIN_PREFIX+"/virtualcard/withdrawals/pdf?filter[from]="+startfrom
                            +"&filter[to]="+endto
                            +"&filter[status]="+status
                            +"&filter[currency]="+currency
                            +"&filter[user]="+user;
    });

}

if ($('.content').find('#withdrawalEditBox').length) {

    $('#status').on('change', function() {
        $('#cancellation_reason').parent('div').find('.error').hide();
        if ($(this).val() == 'Cancelled') {
            $('#cancellation_reason').attr('required', true);
            $('#cancellation_reason_div').show();
        } else {
            $('#cancellation_reason').attr('required', false);
            $('#cancellation_reason_div').hide();
        }
    });

    $(window).on('load', function() {
        if ($('#status').val() == 'Cancelled') {
            $('#cancellation_reason').attr('required', true);
            $('#cancellation_reason_div').show();
        } else {
            $('#cancellation_reason').attr('required', false);
            $('#cancellation_reason_div').hide();
        }
    });

    $(document).on('submit', '#withdrawalStatusUpdateForm', function() {
        $("#withdrawalStatusUpdateBtn").attr("disabled", true);
        $(".fa-spinner").removeClass('d-none');
        $("#withdrawalStatusUpdateBtnText").text(submitButtonText);
    });
}