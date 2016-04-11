<?php
$request = new Request(array(), array('signature', 'timestamp'), false);
$name = $request->body['name'];

$checkQuery = new Query('SELECT id FROM productGroups WHERE name=:name');
$checkQuery->execute(array('name'=>$name));
if (count($checkQuery->results)) {
  $response = new Response(409, array('message'=>"Conflict: duplicate entry $name"));
  $response->send();
  die();
}

$query = new Query("INSERT INTO productGroups (name, description) VALUES (:name, :description)");
$query->execute(array('name'=>$name, 'description'=>$description));

$response = new Response(205, array('message'=>'group posted ok.', 'id'=>$query->lastId()));
$response->send();
?>
