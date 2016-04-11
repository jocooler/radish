<?php
$request = new Request(array(), array('signature', 'timestamp'), true);
$target = $request->targets[0];
$name = $request->body['name'];

$query = new Query("UPDATE userGroups SET name=:name WHERE id=:id");
$query->execute(array('name'=>$name, 'id'=>$target));

$response = new Response(200, $query->lastId() . " updated.");
$response->send();
?>
