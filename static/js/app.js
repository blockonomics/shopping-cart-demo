service = angular.module("shoppingcart.services", ["ngResource"]);
service.factory('ShoppingCart', function ($resource) {
  var cart_item = $resource('/shopping-cart-demo/shopping_cart.php');
  return cart_item;
});

app = angular.module("shopping-cart-demo", ["ngRoute", "shoppingcart.services"]);

app.config(function ($routeProvider) {
  $routeProvider
    .when('/', {templateUrl: '/shopping-cart-demo/static/views/shoppinglist.html', controller: ShoppingListController});
  /*
    .when('/checkout', {templateUrl: '/views/checkout.html', controller: CheckoutController})
    .when('/status', {templateUrl: '/views/status.html', controller: StatusController});
    */
});

function ShoppingListController($scope, $rootScope, ShoppingCart) {
  $scope.itemList = ShoppingCart.query({"action":"getitems"});
  $scope.cart = ShoppingCart.query({"action":"getcart"});
  $scope.total = 0;
  console.log($scope.itemList);

  $scope.addItem = function(code, quantity){
    $scope.total = 0;
    $scope.cart = ShoppingCart.query({"action":"add", "code":code, "quantity":quantity});
  };

  $scope.sum = function(value){
    $scope.total += value;
    $scope.total = +(Math.round($scope.total + "e+2")  + "e-2")
  };

  $scope.removeItem = function(code){
    $scope.cart = ShoppingCart.query({"action":"remove", "code":code});
  };

  $scope.emptyCart = function(){
    $scope.cart = ShoppingCart.query({"action":"empty"});
  };
}
