<?php
$request = new Request(array(), array('signature', 'timestamp'), true);
$target = $request->targets[0];

$query = new Query("UPDATE userGroups SET name=:name, description=:description WHERE id=:id");
$query->execute(array('name'=>$request->body['name'], 'id'=>$target, 'description'=>$request->body['description']));

$response = new Response(200, $query->lastId() . " updated.");
$response->send();
?>
