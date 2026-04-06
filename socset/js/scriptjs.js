var cart = {};

function func(click_id) {
    if (cart[click_id] == undefined) {
        cart[click_id] = 1;
    } else {
        cart[click_id]++;
    }
    saveCart();
}

function saveCart() {
    localStorage.setItem('cart', JSON.stringify(cart));
}

function loadCart() {
    if (localStorage.getItem('cart')) {
        cart = JSON.parse(localStorage.getItem('cart'));
    }
}

$(document).ready(function(){
    loadCart();
});