<?php
//TODO not yet done
class Post_Product extends Product {
  protected $post_query_string = "INSERT INTO products (`sku`, `upc`, `name`, `manufacturer`, `category`, `wholesale`, `taxable`, `qoh`, `retail`, `discount`, `discount_type`) VALUES (:sku, :upc, :name, :manufacturer, :category, :wholesale, :taxable, :qoh, :retail, :discount, :discount_type)";
  protected $max_query_string = "SELECT MAX as max from products";
  protected $check_query_string = "SELECT sku FROM products WHERE upc = :upc";
  public $body;

  public function fetch_details() {
    $this->set($this->body);
    $post_query_parameters = array (
      'sku'   => $this->sku,
      'upc'   => $this->upc,
      'name'  => $this->name,
      'manufacturer'  => $this->manufacturer,
      'category'      => $this->category,
      'wholesale'     => $this->wholesale,
      'taxable'       => $this->taxable,
      'qoh'           => $this->qoh,
      'retail'        => $this->retail,
      'discount'      => $this->discount,
      'discount_type' => $this->discount_type
    );

  }
}

$request = new Request();

?>
