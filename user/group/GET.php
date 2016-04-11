<?php
$request = new Request(array(), array('signature', 'timestamp'), false);
$query = new Query("SELECT * FROM userGroups WHERE 1");
$query->execute(array());

if (!$query->results) {
  $response = new Response();
  $response->set_header(204);
} else {
  $response = new Response(200, $query->results);
}
$response->send();
?>
