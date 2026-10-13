'use strict';

// For index file
$(function() {
    $(".filter-panel").css('display', 'block');
});

document.querySelector('form').addEventListener('submit', function (e) {
    e.preventDefault();
    const params = new URLSearchParams(new FormData(this));
    const url = `${filterUrl}?${params.toString().replace(/%5B/g, '[').replace(/%5D/g, ']')}`;
    window.location.href = url;
});
