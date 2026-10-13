'use strict';

if ($('.main-containt').find('#disputeCreate').length) {
    // Disable Submit Button
    $('#disputeCreateForm').on('submit', function () {
        $(".spinner").removeClass('d-none');
        $("#disputeSvgIcon").addClass('d-none');
        $("#disputeCreateSubmitBtn").attr("disabled", true);
        $("#disputeCreateSubmitBtnText").text(submitButtonText);
    });
}
if ($('.main-containt').find('#disputeDiscussion').length) {

    var paginate = 1;

    loadMoreData(paginate);

    $('#load-more').on('click', function() {
        var page = $(this).data('paginate');
    
        loadMoreData(page);
        $(this).data('paginate', page+1);
    });

    function loadMoreData(paginate) {
        
        $.ajax({
            
            url:  disputeReplyLoadUrl,
            type: 'get',
            datatype: 'html',
            data: {
                'page': paginate
            },
            beforeSend: function() {
                $('#load-more').text(loadingText);
            }
        })
        .done(function(data) {
            
            if(data.length == 0) {
                $('.invisible').removeClass('invisible');
                $('#load-more').hide();
                return false;
            } else {
                $('#load-more').text(loadMoreText);
                $('#dispute-replies').append(data);
            }
        })
    }

    $('#disputeStatusChangeForm').on('submit', function () {
        $("#disputeStatusChangeLoader").removeClass('d-none');
        $("#disputeStatusChangeSubmitBtn").attr("disabled", true);
        $("#disputeStatusChangeSubmitBtnText").text('');
    });

    $('#disputeReplyForm').on('submit', function () {
        $("#disputeReply").removeClass('d-none');
        $("#disputeReplySubmitBtn").attr("disabled", true);
        $("#disputeReplySubmitBtnText").text('');
    });

    const actualBtn = $('#file');
    $(document).on('change', '#file', function () {
        let fileExtension = actualBtn.val().replace(/^.*\./, '');
        let fileInput = actualBtn[0];

        if (!extensions.includes(fileExtension)) {
            fileInput.value = '';
            $('.file-error').addClass('error').text(extensionsValidationMessage);
            $('#fileSpan').fadeIn('slow').delay(2000).fadeOut('slow');
            return false;
        } else {
            $('.file-error').text('');
            return true;
        }
    });

}

if ($('.main-containt').find('#disputeIndex').length) {

    $(document).ready(function () {

        let status = $('#status').val();

        if (startDate != '' || status != 'all') {
            $(".filter-panel").css('display', 'block');
        }
        
        $(".fil-btn").on('click', function () {
            $(this).find('img').toggle();
            $(".filter-panel").slideToggle(300);
        });
    });
}
