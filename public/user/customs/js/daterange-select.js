'use strict';


$(document).ready(function()
{
    var sDate = $('#startfrom').val() ?  moment($('#startfrom').val(), 'YYYY-MM-DD') : moment();
    var eDate = $('#endto').val() ? moment($('#endto').val(), 'YYYY-MM-DD') : moment().subtract(365, 'days');
    var sessionDate      = sessionDateFormateType;
    var sessionDateFinal = sessionDate.toUpperCase();

    $('#daterange-btn').daterangepicker({
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            startDate: sDate,
            endDate: eDate,

        }, function (start, end) {
            sDate = moment(start, 'MMMM D, YYYY').format(sessionDateFinal);
            $('#startfrom').val(sDate);
            eDate = moment(end, 'MMMM D, YYYY').format(sessionDateFinal);
            $('#endto').val(eDate);
            $('#daterange-btn p').html(sDate + ' - ' + eDate);
        }
    )
    
    if (startDate == '') {
        $('#daterange-btn p').html(dateRangePickerText);
    } else {
        startDate = moment(startDate, "YYYY-MM-DD").format(sessionDateFinal);
        endDate = moment(endDate, "YYYY-MM-DD").format(sessionDateFinal);
        $('#daterange-btn p').html(startDate + ' - ' + endDate);
    }

});