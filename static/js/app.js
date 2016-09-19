service = angular.module("shoppingcart.services", ["ngResource"]);
service.factory('ShoppingCart', function ($resource) {
  var cart_item = $resource('shopping_cart.php');
  return cart_item;
});

app = angular.module("shopping-cart-demo", ["monospaced.qrcode", "ngRoute", "shoppingcart.services"]);

app.config(function ($routeProvider) {
  $routeProvider
    .when('/', {templateUrl: 'static/views/shoppinglist.html', controller: ShoppingListController})
    .when('/invoice', {templateUrl: 'static/views/invoice.html', controller: CheckoutController});
  /*
    .when('/status', {templateUrl: '/views/status.html', controller: StatusController});
    */
});

function ShoppingListController($scope, $window, $location, $rootScope, ShoppingCart) {
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

  $scope.checkoutCart = function(){
    ShoppingCart.get({"action":"createinvoice"}, function(order){
      window.location.href = "index.html#/invoice?order_id=" + order.order_id; 
    });
  };
}

function CheckoutController($scope, $location, $interval, $rootScope, ShoppingCart) {
  //get order id from url
  current_p = $location.path();
  current_s = $location.search();

  var totalProgress = 100;
  var totalTime = 10*60; //10m
  $scope.progress = totalProgress;
  $scope.clock = totalTime;

  $scope.tick = function() {
    $scope.clock = $scope.clock-1;
    $scope.progress = Math.floor($scope.clock*totalProgress/totalTime);

    if($scope.progress == 0){
      $scope.invoice = ShoppingCart.get({"action":"getinvoice", "order_id":$scope.invoice.order_id});
      $scope.progress = totalProgress;
      $scope.clock = totalTime;
    }
  };

  if ( current_p == "/invoice" && typeof current_s.order_id != 'undefined'){
    $scope.invoice = ShoppingCart.get({"action":"getinvoice", "order_id":current_s.order_id});
    $scope.tick_interval  = $interval($scope.tick, 1000);
  }
}
