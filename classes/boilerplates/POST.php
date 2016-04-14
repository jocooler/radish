$request = new Request(array(), array('signature', 'timestamp'), false); //TODO update this to use the new Request constructor.
$product = new Post_Product($_POST['upc'], 'upc');
$response = new Response(205, array('message'=>'product posted ok.', 'sku'=>$product->sku));
$response->send();
