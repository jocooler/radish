<?php
  $path = "../../classes/";
  require($path . "product.php");
  require($path . "helpers/request.php");
  require($path . "helpers/security.php");
  require($path . "helpers/query.php");
  require($path . "helpers/response.php");
  require($path . "helpers/discount.php");

  try {
    require($_SERVER['REQUEST_METHOD'] . ".php");
  } catch (Exception $e) {
    $response = new Response(405, array());
    $response->send();
  }
?>
