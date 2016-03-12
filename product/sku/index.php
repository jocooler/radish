<?php
  $path = "../../classes/";
  //echo dirname(__FILE__);
  require($path . "product.php");
  require($path . "helpers/request.php");
  require($path . "helpers/security.php");
  require($path . "helpers/query.php");
  require($path . "helpers/response.php");
  require($_SERVER['REQUEST_METHOD'] . ".php");
?>
