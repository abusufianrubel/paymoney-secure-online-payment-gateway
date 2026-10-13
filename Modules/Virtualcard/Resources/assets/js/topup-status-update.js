"use strict";
$(document).on('submit', '#TopupStatusUpdateForm', function() {
    $("#TopupStatusUpdateBtn").attr("disabled", true);
    $(".fa-spinner").removeClass('d-none');
    $("#TopupStatusUpdateBtnText").text(submitButtonText);
});

$(window).on('load', function() {
    topupStatusChange();
    withdrawalStatusChange();
});
$(document).on('change', '#fund_approval_status', function() {
    topupStatusChange();
});

function topupStatusChange() {
    var fund_approval_status = $('#fund_approval_status').val();
    $('#cancellation_reason').parent('div').find('.error').hide();
    if (fund_approval_status == 'Cancelled') {
        $('#cancellation_reason').attr('required', true);
        $('#cancellation_reason_div').show();
    } else {
        $('#cancellation_reason').attr('required', false);
        $('#cancellation_reason_div').hide();
    }
}

$('#withdrawalFundApprovalStatus').on('change', function() {
    withdrawalStatusChange();
});

function withdrawalStatusChange()
{
    var withdrawalFundApprovalStatus = $('#withdrawalFundApprovalStatus').val();
    $('#withdrawalCancellationReason').parent('div').find('.error').hide();
    if (withdrawalFundApprovalStatus == 'Cancelled') {
        $('#withdrawalCancellationReason').attr('required', true);
        $('#withdrawalCancellationReasonDiv').show();
    } else {
        $('#withdrawalCancellationReason').attr('required', false);
        $('#withdrawalCancellationReasonDiv').hide();
    }
}