
"use strict";

// Apply dark class to body immediately to prevent flash
if (localStorage.getItem('dark') === '1') {
  document.addEventListener('DOMContentLoaded', function() {
    document.body.classList.add('dark');
  });
}

