<?php
class Get_Product extends Product {
  protected $get_query_string = "SELECT * FROM products WHERE upc= :id";
  public function execute() {
    $this->query = new Query($this->get_query_string, array('id'=>$this->upc));
    if (isset($this->query->results[0])){
      $this->set($this->query->results[0]);
    } else {
      $this->upc = ''; // product didn't exist. Return a nulled result
    }
  }
}

$request = new Request(array('include'));
$targets = $request->targets;
$data = array();

foreach ($targets as $target) {
  $product = new Get_Product($target, 'upc');
  $data[$product->upc] = $product->get('all');
}

if (isset($request->parameters['include'])) {
  $response = new Response($data, $request->parameters['include'], 'json');
} else {
  $response = new Response(200, $data);
}

$response->send();
?>
