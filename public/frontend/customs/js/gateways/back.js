"use strict";

$(document).ready(function () {
    $("#goBackButton").on("click", function () {
        window.history.back();
    });
});

if (window["ReactNativeWebView"]) {
    $("#goBackButton").addClass('d-none');
}
