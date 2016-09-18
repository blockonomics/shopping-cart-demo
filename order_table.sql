CREATE TABLE IF NOT EXISTS `order_table` (
  `order_id` varchar(255) NOT NULL,
  `addr` varchar(255) NOT NULL,
  `txid` varchar(255) NOT NULL,
  `status` int(8) NOT NULL,
  `cart` text NOT NULL,
  `value` double(10,2) NOT NULL,
  PRIMARY KEY (`order_id`),
  UNIQUE KEY `order_table` (`addr`)
  )
