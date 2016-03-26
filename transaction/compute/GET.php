<?php
class Compute_Transaction extends Transaction {
  public function execute() {
    $this->set($this->body);

    foreach ($this->products as $product) {
      $product['product']->applyDiscounts();
    }
  }
}

$request = new Request(array(), array('signature', 'timestamp'), false); //TODO update this to use the new Request constructor.
$transaction = new Compute_Transaction();
$response = new Response(200, array(serialize($transaction->get('all'))));
$response->send();
?>
