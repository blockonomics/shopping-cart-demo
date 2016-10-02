service = angular.module("shoppingcart.services", ["ngResource"]);
service.factory('Products', function ($resource) {
  var cart_item = $resource('php/productlist.php');
  return cart_item;
});

service.factory('AddItem', function ($resource) {
  var cart_item = $resource('php/additem.php');
  return cart_item;
});

service.factory('RemoveItem', function ($resource) {
  var cart_item = $resource('php/removeitem.php');
  return cart_item;
});

service.factory('Cart', function ($resource) {
  var cart_item = $resource('php/cart.php');
  return cart_item;
});

service.factory('EmptyCart', function ($resource) {
  var cart_item = $resource('php/emptycart.php');
  return cart_item;
});

service.factory('CreateInvoice', function ($resource) {
  var cart_item = $resource('php/createinvoice.php');
  return cart_item;
});

service.factory('Invoice', function ($resource) {
  var cart_item = $resource('php/invoice.php');
  return cart_item;
});

app = angular.module("shopping-cart-demo", ["monospaced.qrcode", "ngRoute", "shoppingcart.services"]);

app.config(function ($routeProvider) {
  $routeProvider
    .when('/', {templateUrl: 'views/shoppinglist.html', controller: ShoppingListController})
    .when('/invoice', {templateUrl: 'views/invoice.html', controller: CheckoutController})
    .when('/status', {templateUrl: 'views/status.html', controller: StatusController});
});

function ShoppingListController($scope, $window, $location, $rootScope, 
    Products, AddItem, RemoveItem, EmptyCart, Cart, CreateInvoice) {
  $scope.itemList = Products.query();
  $scope.cart = Cart.query();
  $scope.total = 0;
  console.log($scope.itemList);

  $scope.addItem = function(code, quantity){
    $scope.total = 0;
    $scope.cart = AddItem.query({"code":code, "quantity":quantity});
  };

  $scope.sum = function(value){
    $scope.total += value;
    $scope.total = +(Math.round($scope.total + "e+2")  + "e-2")
  };

  $scope.removeItem = function(code){
    $scope.cart = RemoveItem.query({"code":code});
  };

  $scope.emptyCart = function(){
    $scope.cart = EmptyCart.query();
  };

  $scope.checkoutCart = function(){
    CreateInvoice.get({}, function(order){
      window.location.href = "index.html#/invoice?order_id=" + order.order_id; 
    });
  };
}

function CheckoutController($scope, $location, $interval, $rootScope, Invoice) {
  //get order id from url
  current_p = $location.path();
  current_s = $location.search();

  var totalProgress = 100;
  var totalTime = 10*60; //10m
  $scope.progress = totalProgress;
  $scope.clock = totalTime;

  $scope.getJson = function(data){
    return JSON.parse(data);
  };

  $scope.tick = function() {
    $scope.clock = $scope.clock-1;
    $scope.progress = Math.floor($scope.clock*totalProgress/totalTime);

    if($scope.progress == 0){
      $scope.invoice = Invoice.get({"order_id":$scope.invoice.order_id});
      $scope.progress = totalProgress;
      $scope.clock = totalTime;
    }
  };

  if ( current_p == "/invoice" && typeof current_s.order_id != 'undefined'){
    Invoice.get({"order_id":current_s.order_id}, function(data){
      $scope.invoice = data;

      if($scope.invoice.status == -1){
        $scope.tick_interval  = $interval($scope.tick, 1000);
      }

      //Websocket
      var ws = new WebSocket("wss://www.blockonomics.co/payment/" + $scope.invoice.addr + "?timestamp=" + $scope.invoice.timestamp);

      ws.onmessage = function (evt) {
        //Refresh invoice from server
        $interval(function(){
          $scope.invoice = Invoice.get({"order_id":$scope.invoice.order_id});

          if ($scope.tick_interval)
            $interval.cancel($scope.tick_interval);
        }, 5000, 1);
      }
    });
  }
}

function StatusController($scope, $window, $location, $rootScope) {
  $scope.checkStatus = function(){
    window.location.href = "index.html#/invoice?order_id=" + $scope.order_id; 
  };
}
