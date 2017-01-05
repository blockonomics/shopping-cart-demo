# Shopping-cart-demo
Demo shopping website that uses blockonomics payments API, hosted at http://shopping.blockonomics.co 

# Requirements
php, php-sqli and mysql-server   
`sudo apt-get install php php-sqli mysql-server`      

Web server supporting php like nginx, apache. For a simple php server you can run      
`php -S localhost:8000`


# Setup

##Blockonomics merchant setup 
* Complete [merchant wizard](https://www.blockonomics.co/merchants)
* Set [Your server_url]/shopping-cart-demo/php/callback.php?secret=[your secret] as the callback url
* Obtain api key from Wallet Watcher > Settings

##Server Setup
* Clone the git repository into the ROOT of your web server.
* Change the file *php/config.php* to set your db credentials, blockonomics api key and secret 
* Run your web server
* Navigate to shopping-cart-demo/php/setup.php in your browser (Should show __Database setup is done__)
* Navigate to shopping-cart-demo/index.html in browser and enjoy shopping !

----
All contributions are welcome. Please create a pull request and we will be
happy to merge it in.
