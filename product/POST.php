<?php
//TODO not yet done
class Post_Product extends Product {
  protected $post_query_string = "INSERT INTO products (`sku`, `upc`, `name`, `manufacturer`, `category`, `wholesale`, `taxable`, `qoh`, `retail`, `discount`, `discount_type`) VALUES (:sku, :upc, :name, :manufacturer, :category, :wholesale, :taxable, :qoh, :retail, :discount, :discount_type)";
  protected $max_query_string = "SELECT MAX as max from products";
  protected $check_query_string = "SELECT sku FROM products WHERE upc = :upc";
  public $body;

  public function execute() {
    $this->set($this->body);
    $check_query = new Query($check_query_string, array('upc'=>$this->upc));

    if ($check_query->result) { // TODO test This
      // there already was a product
      $response = new Response(409, array("error"=>"product already exists. Please try a PUT request to the UPC."));
      die();
    } else {

      $max_query = new Query($max_query_string);
      $max_query->execute(array());
      $this->set_sku($max_query->result);

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

      $this->query = new Query($post_query_string, $post_query_parameters);
    }
  }
}

$request = new Request(array(), array('signature', 'timestamp'), false);
$product = new Post_Product($_POST['upc'], 'upc');
$response = new Response(205, array('message'=>'product posted ok.', 'sku'=>$product->sku));
$response->send();

?>
