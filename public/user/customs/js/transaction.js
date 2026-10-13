'use strict';


// Transaction
$(document).ready(function () {
    var status = $('#status').val();
    var type = $('#type').val();
    var wallet = $('#wallet').val();
    if (startDate != '' || status != 'all' || type != 'all' || wallet != 'all') {
        $(".filter-panel").css('display', 'block');
    }

    $(".fil-btn").on('click', function () {
        $(this).find('img').toggle();
        $(".filter-panel").slideToggle(300);
    });
});