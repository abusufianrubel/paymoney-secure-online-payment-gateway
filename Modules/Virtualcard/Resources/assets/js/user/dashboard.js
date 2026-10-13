'use strict';
var totalSlides = document.querySelectorAll('.swiper .swiper-slide').length;
document.addEventListener('DOMContentLoaded', function () {
    var swiper = new Swiper('.swiper', {
        slidesPerView: totalSlides === 1 ? 1 : 1.2,
        breakpoints: {
            900: {
                slidesPerView: totalSlides === 1 ? 1 : 1.2,
            },
             750:{
                slidesPerView: totalSlides === 1 ? 1 : 1,
            } ,
            550:{
                slidesPerView: totalSlides === 1 ? 1 : 1.2,
            } ,
            0:{
                slidesPerView: totalSlides === 1 ? 1 : 1,
            } ,
           
        },
        navigation: {
          nextEl: ".swiper-button-next",
          prevEl: ".swiper-button-prev",
          disabledClass: "slidesss",
        },
        spaceBetween: 32,
        freeMode: {
            enabled: true,
            sticky: true,
            momentum: false
        },
        
        on: {
            slideChange: function() {
                var activeIndex = this.activeIndex;
                updateDisplayedInfo(activeIndex);
            }
        }
    });

    function updateDisplayedInfo(index) {
        const activeSlide = swiper.slides[swiper.activeIndex];
        if (activeSlide) {
            let totalBalance = activeSlide.querySelector('#totalBalance')?.value || "0.00";
            let cardType = activeSlide.querySelector('#cardType')?.value || "---";
            let cardCurrency = activeSlide.querySelector('#cardCurrency')?.value || "---";
            let cardStatus = activeSlide.querySelector('#cardStatus')?.value || "";
            let topupCreateLink = activeSlide.querySelector('#cardTopupLink')?.value || "";
            let withdrawalCreateLink = activeSlide.querySelector('#cardWithdrawalLink')?.value || " ";

            document.getElementById("selected-card-amount").innerText = totalBalance;
            document.getElementById("selected-card-type").innerText = cardType;
            document.getElementById("selected-card-currency").innerText = cardCurrency ;
            document.getElementById('topup-link').href = topupCreateLink;
            document.getElementById('withdrawal-link').href = withdrawalCreateLink;
            let statusElement = document.getElementById("selected-card-status");
            if (cardStatus.trim()) {
                statusElement.innerText = cardStatus;
                statusElement.classList.add("bg-success","status-text", "rounded-pill", "gilroy-Semibold");
                statusElement.classList.remove("no-status-text");
            } else {
                statusElement.innerText = "---";
                statusElement.classList.remove("bg-success","status-text", "rounded-pill", "gilroy-Semibold");
                statusElement.classList.add("no-status-text", "gilroy-bold");
            }
            }
    }
    swiper.emit('slideChange');
});