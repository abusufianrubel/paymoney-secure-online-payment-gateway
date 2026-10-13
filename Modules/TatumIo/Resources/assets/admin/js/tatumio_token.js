'use strict';

$(document).on('change', '#stableType', function() {
  var selectId = $(this).attr("id");
  var optionId = $(this).find("option:selected").attr("id");

  var newId = optionId.replace(/network-(\d+)/, 'token-$1');
  $('.address').addClass('d-none');
  $('#'+newId).removeClass('d-none'); 
});

$(document).on('change', '#type', function() {
  var asset = $(this).find("option:selected").val();
  $('#token-fees').text(asset);
});

$(document).on('submit', '#add-tatumio-network-form', function() {

  $("#tatumio-settings-submit-btn").attr("disabled", true);
  $(".fa-spinner").removeClass('display-spinner');
  $("#tatumio-settings-submit-btn-text").text(submitting);

  setTimeout(function(){
      $(".fa-spinner").addClass('display-spinner');
      $("#tatumio-settings-submit-btn").attr("disabled", false);
      $("#tatumio-settings-submit-btn-text").text(submit);
  }, 10000);
});

function restrictNumberToPrefdecimalOnInput(e)
{
    var type = $('#network').data('type')
    restrictNumberToPrefdecimal(e, type);
}

$(document).ready(function() {
  $('#total_supply, #decimals').on('input', function() {
      $(this).val($(this).val().replace(/[^0-9]/g, ''));
  });
});
