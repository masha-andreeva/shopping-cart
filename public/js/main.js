function ShoppingCart() {

    this.addProductToCart = function(productId) {
        var productCount = $("#productCount_" + productId).val();
        $.ajax({
            url: "/shoppingCart/add",
            data: {productCount: productCount, productId: productId},
            type: "POST",
            dataType: "html",
            success: function(response) {
                $("#shoppingCartCount").html(response);
            }
        });
    }

    this.update = function(itemId) {
        var productCount = $("#productCount_" + itemId).val();
        $.ajax({
            url: "/shoppingCart/update",
            data: {productCount: productCount, itemId: itemId},
            type: "POST",
            dataType: "html",
            success: function() {
                location.reload();
            }
        });
    }
}

function MainPage() {
    this.shoppingCart = new ShoppingCart();
}

//global  object
var mainPage;

$(function() {
    //	init global object
    mainPage = new MainPage();

    // Floating Cart
    $("#productCartContainer").sticky({topSpacing: 50});

    //Allow input only numbers
    $(".product-count").keypress(function(e) {
        //if the letter is not digit then display error and don't type anything
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
            return false;
        }
    });
});