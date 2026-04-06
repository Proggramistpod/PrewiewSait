// Выполнение функции после загрузки страницы
var cart = {};

$(document).ready(function(){
    loadCart();
})

function loadCart(){
    if(localStorage.getItem('cart')){
        cart = JSON.parse(localStorage.getItem('cart'));
        var out='';
        for(var key in cart){
            out+=key+" ";
        }
        $.post(
            "core.php",{"action":"init","id_product":String(out)},showCart
        );
    }
    else{
        var mainCartElement = document.querySelector('.main-cart');
        mainCartElement.innerHTML = 'Корзина пуста!';
        mainCartElement.style.color = '#FFC000';
        mainCartElement.style.fontSize = '24px';
        mainCartElement.style.textAlign = 'center';
        mainCartElement.style.marginTop = '20px';
    }
}

function showCart(data){
    data = JSON.parse(data);
    var fullsum = 0;
    var table_cart = "<table class='table_cart'><tr><th>Товар</th><th>Цена</th><th>Количество</th><th>Сумма</th></tr>";
    for(var id in data){
        fullsum += Number(data[id].price);
        cart[data[id].id] = {'name':data[id].name, 'price':Number(data[id].price), 'count':1,'sum':Number(data[id].price)};
        table_cart += "<tr>";
        table_cart +='<td>'+data[id].name+'</td>';
        table_cart +='<td>'+data[id].price+'</td>';
        table_cart +='<td><div class="count_prod'+data[id].id+'">1</div>' +
            '<button class="count-product-min" type="submit" onclick="CountProduct(this.id,'+data[id].price+')" id="1'+data[id].id+'"></button>' +
            '<button class="count-product-max" type="submit" onclick="CountProduct(this.id,'+data[id].price+')" id="2'+data[id].id+'"></button></td>';
        table_cart +='<td class="sum_stroka'+data[id].id+'">'+data[id].price+'</td>';
        table_cart +='</tr>';
    }
    table_cart += "<tfoot><tr><td>Итоговая сумма:</td><td></td><td></td><td class='FullSum'>"+fullsum+"</td></tr></tfoot></table>";
    $('.main-cart').html(table_cart);
}

function CountProduct(button_id, price){
    var click_button = button_id[0];
    var count_update = button_id.slice(1);
    if (click_button == "1"){
        cart[count_update].count--;
        if (cart[count_update].count <= 1){
            cart[count_update].count = 1;
        }
    }
    else if(click_button == "2"){
        cart[count_update].count++;
    }
    cart[count_update].sum = price*cart[count_update].count;
    $('.sum_stroka'+count_update).html(cart[count_update].sum);
    $('.count_prod'+count_update).html(cart[count_update].count);
    var out = 0;
    for(var key in cart){
        out += cart[key].sum;
    }
    $('.FullSum').html(out);
}

function POSTData(){
    var strData = {
        product: "",
        price: "",
        count_product: "",
        summa: ""
    };
    var num = Math.floor(Math.random()*1000);
    var today = new Date();
    var dateSrc = today.toLocaleString('ru-RU', { year: 'numeric', month: 'numeric', day: 'numeric' });
    dateDst = dateSrc.split(".").reverse().join("-");
    strData.num_order = num;
    strData.date_order = dateDst;
    for(var key in cart){
        strData.product += cart[key].name+" ";
        strData.price += cart[key].price+" ";
        strData.count_product += cart[key].count+" ";
        strData.summa += cart[key].sum+" ";
    }
    $.post(
        "core.php",
        {"action":"inputData","Data_Order":strData},
        function(response) {
            if (response.status === 'ok') {
                localStorage.removeItem('cart');
                var mainCartElement = document.getElementById("main-cart");
                mainCartElement.innerHTML = "";
                mainCartElement.innerHTML = "Заказ оформлен! Детали заказа можно посмотреть в личном кабинете по кнопке История заказов.";
                mainCartElement.style.color = "#FFC000";
                mainCartElement.style.fontSize = "24px";
                mainCartElement.style.textAlign = "center";
                mainCartElement.style.marginTop = "20px";
            } else {
                alert("Ошибка при оформлении заказа: " + (response.message || "неизвестная ошибка"));
            }
        },
        "json"
    );
}