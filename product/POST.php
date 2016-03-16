<?php
//TODO not yet done
class Post_Product extends Product {
  protected $post_query_string = "INSERT INTO products (`sku`, `upc`, `name`, `manufacturer`, `category`, `wholesale`, `taxable`, `qoh`, `retail`, `discount`, `discount_type`) VALUES (:sku, :upc, :name, :manufacturer, :category, :wholesale, :taxable, :qoh, :retail, :discount, :discount_type)";
  protected $max_query_string = "SELECT MAX as max from products";
  protected $check_query_string = "SELECT sku FROM products WHERE upc = :upc";
  public $body;

  public function fetch_details() {
    $this->set($this->body);
    $this->query = new Query($check_query_string, array('upc'=>$this->upc));

    if ($this->query->result) { // TODO test This
      // there already was a product
      $response = new Response(409, array("error"=>"product already exists. Please try a PUT request to the UPC."));
      die();
    } else {

      $max_query = new Query($max_query_string);
      $max_query->execute(array());
      $this->sku = $max_query->result;

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
}

$request = new Request(array(), array('signature', 'timestamp'), false);

?>
