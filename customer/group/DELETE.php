<?php
$request = new Request(array(), array('signature', 'timestamp'), true);
$target = $request->targets[0];

$query = new Query("DELETE FROM customerGroups WHERE id=:id");
$query->execute(array('id'=>$target));

$response = new Response(200, $target . " deleted.");
$response->send();
?>
