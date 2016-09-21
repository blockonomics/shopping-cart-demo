<?php
include_once("config.php");
$db_conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS);
$result = $db_conn->query('CREATE DATABASE IF NOT EXISTS '.$DB_NAME);

if(!$result) 
{
  die($db_conn->error.__LINE__);
}

mysqli_select_db($db_conn, $DB_NAME) or die( "Unable to select database. Run setup first.");

$result = $db_conn->query('CREATE TABLE IF NOT EXISTS product_table (id INT(8) NOT NULL AUTO_INCREMENT, name VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, image TEXT NOT NULL, price DOUBLE(10,2) NOT NULL, PRIMARY KEY (id), UNIQUE KEY product_code (code))');

if(!$result) 
{
  die($db_conn->error.__LINE__);
}

$db_conn->query('INSERT INTO product_table (id, name, code, image, price) VALUES
  (1, "Ball Point Pen", "3P01", "images/pen.png",  0.03),
  (2, "Pink Eraser", "PE02", "images/eraser.png", 0.01),
  (3, "Color Pencil", "GreenP03", "images/pencil.png", 0.02)');

$result = $db_conn->query('CREATE TABLE IF NOT EXISTS order_table (
  order_id varchar(255) NOT NULL,
  addr varchar(255) NOT NULL,
  txid varchar(255) NOT NULL,
  status int(8) NOT NULL,
  cart text NOT NULL,
  value double(10,2) NOT NULL,
  bits_payed int(8) NOT NULL,
  PRIMARY KEY (order_id),
  UNIQUE KEY order_table (addr))');

if(!$result) 
{
  die($db_conn->error.__LINE__);
}

echo "<html><body><h4> Database setup is done. </h4></body></html>";
?>
