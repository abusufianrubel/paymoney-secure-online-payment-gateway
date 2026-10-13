"use strict";

$(document).ready(function () {
  if (typeof (Storage) !== "undefined") {
      const isDarkMode = localStorage.getItem('dark') === '1';
      
      // Ensure dark class is on body (already applied inline)
      $("body").toggleClass("dark", isDarkMode);
      $("#switch").toggleClass("switched", isDarkMode);
      
      // Set sun/moon images (backup in case inline script didn't run)
      if (isDarkMode) {
          $("img.sun").removeClass("img-none").show();
          $("img.moon").addClass("img-none").hide();
      } else {
          $("img.moon").removeClass("img-none").show();
          $("img.sun").addClass("img-none").hide();
      }
      
      $("#switch").on('click', function () {
          const isDark = $("body").toggleClass("dark").hasClass("dark");
          $(this).toggleClass("switched");
          $(this).find('img').toggle();
          localStorage.setItem('dark', isDark ? '1' : '0');
          localStorage.setItem('theme', isDark ? 'dark' : 'light');
      });
  }
});

$(document).ready(function () {
  $('.select2').select2();

  // User Select
  $(".usertype-module").on('click', function(){
    $(".usertype-module").removeClass("user-selected");
    $(this).addClass("user-selected");
  });

  // Gateway Select
  $(".gateway").on('click', function(){
    $(".gateway").removeClass("gateway-selected");
    $(this).addClass("gateway-selected");
  });

  // Privacy Page
  $(".privacy-nav").on('click', function () {
    $(".privacy-nav").removeClass("privacy-nav-active");
    $(this).addClass("privacy-nav-active");
  });

  // Input focus color
  $(".form-control").on('blur',function () {
    if ($(this).val() == '') {
      $(this).removeClass('not-focus-bg');
      $(this).addClass('focus-bgcolor');
    } else {
      $(this).removeClass('focus-bgcolor');
      $(this).addClass('not-focus-bg');
    }
  });

  $(window).scroll(function(){
    var scroll = $(window).scrollTop();
    if (scroll > 95) {
      $(".bg-white").addClass("shadow-sm");
    }
    else{
        $(".start-header").removeClass("shadow-sm");
    }
  })

});

// selcetbox color chang from option select
$('.list-parrent').on('change',function () {
  $(this).closest('.select2-extends').find('.select2-container .select2-selection--single').css('background', '#F3F2FF');
})

$("#eye-icon-show").on('click',function(){
  $(this).removeClass('di-block');
  $(this).addClass('d-none');
  $("#eye-icon-hide").removeClass('d-none');
  $("#eye-icon-hide").addClass('d-block');
  $('#password-div input').attr('type', 'text');
  $('#show_hide_password input').attr('type', 'text');
  $('#confirm-password-div input').attr('type', 'text');
})
$("#eye-icon-hide").on('click',function(){
  $(this).addClass('d-none');
  $("#eye-icon-show").removeClass('d-none');
  $("#eye-icon-show").addClass('d-block');
  $('#password-div input').attr('type', 'password');
  $('#show_hide_password input').attr('type', 'password');
  $('#confirm-password-div input').attr('type', 'password');
})

(function () {
    var carousels = function () {
      $(".owl-carousel1").owlCarousel({
        loop: true,
        center: true,
        margin: 0,
        responsiveClass: true,
        nav: false,
        responsive: {
          0: {
            items: 1,
            nav: false
          },
          680: {
            items: 2,
            nav: true,
            loop: true
          },
          991: {
            items: 3,
            nav: true
          }
        }
      });
    };

    (function ($) {
      carousels();
    })(jQuery);
  })();