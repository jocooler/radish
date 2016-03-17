<?php
class Put_Product extends Product {
  protected $put_query_string = "UPDATE products SET sku=:sku, upc=:upc, name=:name, manufacturer=:manufacturer, category=:category, wholesale=:wholesale, taxable=:taxable, qoh=:qoh, retail=:retail, discount=:discount, discount_type=:discount_type WHERE sku=:sku";
  protected $get_query_string = "SELECT * FROM products WHERE sku= :id";
  public $body;

  public function execute() {
    $get_query_parameters = array ('id'=>$this->sku);
    // 1. get current details.
    $this->query = new Query($this->get_query_string, $get_query_parameters);
    if (isset($this->query->results[0])){
      $this->set($this->query->results[0]);
      // 2. update any new details
      $this->set($this->body);
      $put_query_parameters = array (
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
      $put_query = new Query($this->put_query_string, $put_query_parameters);
      // TODO error checking.
    } else {
      //product didn't exist.
      $response = new Response(410, array("error"=>"product didn't exist"));
      $response->send();
      die();
    }
  }
}

$request = new Request();
$targets = $request->targets;

if (count($request->targets) > 1) {
  $response = new Response(400, array('error'=>"Only one put target per request."));
  $response->send();
  die();
}

$product = new Put_Product($targets[0], 'sku');
$product->body = $request->body;

$response = new Response(204, array());
$response->send();
?>
