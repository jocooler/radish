<?php

$request = new Request(array('include'));
//
$targets = $request->targets;
$data = array();

class Get_Product extends Product {
  protected $get_query_string = "SELECT * FROM products WHERE sku= :id";
  public function fetch_details() {
    $this->query = new Query($this->get_query_string, array('id'=>$this->sku));
    if (isset($this->query->results[0])){
      foreach ($this->query->results[0] as $field => $value) { // TODO this is hacky.
        $this->{$field} = $value;
      }
    } else {
      //TODO error hndin; product doesn't exist
    }
  }
}

foreach ($targets as $target) {
  $product = new Get_Product($target, 'sku');
  $data[$product->sku] = $product->get('all');
}
if (isset($request->parameters['include'])) {
  $response = new Response($data, $request->parameters['include'], 'json');
} else {
  $response = new Response(200, $data);
}
$response->send();
?>
