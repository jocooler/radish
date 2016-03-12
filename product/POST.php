<?php
return $response->write("create a new product");
protected $post_query_string = array(
  "INSERT INTO products (`sku`, `upc`, `name`, `manufacturer`, `category`, `wholesale`, `taxable`) VALUES (:sku, :upc, :name, :manufacturer, :category, :wholesale, :taxable)",

  "INSERT INTO burlingtonProducts (`sku`, `qoh`, `retail`, `discount`, `discount_type`) VALUES (:sku, :qoh, :retail, :discount, :discount_type)"
);
?>
