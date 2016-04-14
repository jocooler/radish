<?php
class Get_Discount extends Discount {
  // nothing to do here, it's all in the base class.
  // TODO consider deleting this class
}

$request = new Request();
$targets = $request->targets;
$data = array();

foreach ($targets as $target) {
  $discount = new Get_Discount($target);
  $data[$discount->id] = $discount->get('all');
}

$response = new Response(200, $data);
$response->send();
?>
