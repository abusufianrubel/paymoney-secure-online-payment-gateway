'use strict';

var sDate;
var eDate;

$(window).on('load', function (e) {

    $(".select2").select2({});
});

$(document).on('keyup keypress', '#user_input', function (e) {
    if (e.type=="keyup" || e.type=="keypress") {
        var user_input = $('form').find("input[type='text']").val();
        if(user_input.length === 0) {
            $('#user_id').val('');
            $('#error-user').html('');
            $('form').find("button[type='submit']").prop('disabled',false);
        }
    }
});

$('#user_input').autocomplete({
    source:function(req,res) {
        if (req.term.length > 0) {
            $.ajax({
                url: userSeachRoute,
                dataType:'json',
                type:'get',
                data:{
                    search:req.term
                },
                success:function (response)
                {
                    $('form').find("button[type='submit']").prop('disabled',true);
                    if(response.status == 'success') {
                        res($.map(response.data, function (item)
                        {
                            return {
                                id : item.user_id,
                                first_name : item.first_name,
                                last_name : item.last_name,
                                value: item.first_name + ' ' + item.last_name
                            }
                        }));
                    } else if(response.status == 'fail') {
                        $('#error-user').addClass('text-danger').html(userNotFound);
                    }
                }
            })
        } else {
            $('#user_id').val('');
        }
    },
    select: function (event, ui)
    {
        var e = ui.item;
        $('#error-user').html('');
        $('#user_id').val(e.id);
        $('form').find("button[type='submit']").prop('disabled',false);
    },
    minLength: 0,
    autoFocus: true
});
