'use strict';

const a = document.getElementById('megaMenuId');
const items = document.getElementById('megaMenuChildId');
const closeBtn = document.getElementById('closeMegaMenuId');
a.addEventListener("click", (e) => {
    e.preventDefault();
    items.classList.toggle("open");
});
closeBtn.addEventListener("click", () => {
    items.classList.remove("open");
});