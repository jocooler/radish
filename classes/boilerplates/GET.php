$request = new Request(array('include'));
$targets = $request->targets;
$data = array();

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
