<?php
  $path = "../classes/";
  require_once($path . "helpers/request.php");
  require_once($path . "helpers/security.php");
  require_once($path . "helpers/query.php");
  require_once($path . "helpers/response.php");
  require_once($path . "discount.php"); // require the main class here so that the endpoint files can't be run on their own.

  try {
    require($_SERVER['REQUEST_METHOD'] . ".php");
  } catch (Exception $e) {
    $response = new Response(405, array());
    $response->send();
  }
?>
