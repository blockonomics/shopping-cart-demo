CREATE TABLE IF NOT EXISTS `product_table` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `image` text NOT NULL,
  `price` double(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_code` (`code`)
)

INSERT INTO `product_table` (`id`, `name`, `code`, `image`, `price`) VALUES
(1, 'Ball Point Pen', '3P01', '/shopping-cart-demo/static/images/pen.png',  0.03),
(2, 'Pink Eraser', 'PE02', '/shopping-cart-demo/static/images/eraser.png', 0.01),
(3, 'Color Pencil', 'GreenP03', '/shopping-cart-demo/static/images/pencil.png', 0.02);
