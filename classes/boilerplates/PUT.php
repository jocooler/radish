$request = new Request();
$targets = $request->targets;

if (count($request->targets) > 1) {
  $response = new Response(400, array('error'=>"Only one put target per request."));
  $response->send();
  die();
}

$product = new Put_Product($targets[0], 'upc');
$product->body = $request->body;

$response = new Response(204, array());
$response->send();
