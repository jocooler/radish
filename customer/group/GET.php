<?php
$request = new Request(array(), array('signature', 'timestamp'), false);
$query = new Query("SELECT * FROM customerGroups WHERE 1");
$query->execute(array());

$response = new Response(200, $query->results);
$response->send();
?>
