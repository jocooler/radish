<?php
class Delete_Product extends Product {
  protected $get_query_string = "SELECT sku FROM products WHERE sku= :id";
  public function fetch_details() {
    $this->query = new Query($this->get_query_string, array('id'=>$this->sku));
    if (isset($this->query->results[0])){
      // actually delete
      $this->query->query = "DELETE FROM products WHERE sku=:id";
      $this->query->execute(array('id'=>$this->sku))
    } else {
      //product didn't exist.
      $response = new Response(410, array("error"=>"product didn't exist"));
      $response->send();
      die();
    }
  }
}

$request = new Request();
if (count($request->targets) > 1) {
  $response = new Response(400, array('error'=>"Only one delete target per request."));
  $response->send();
  die();
}

$product = new Delete_Product($targets[0], 'sku');

$response = new Response(204, $data);
$response->send();
?>
