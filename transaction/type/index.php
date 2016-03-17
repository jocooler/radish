<?php
  $path = "../../classes/";
  require($path . "transaction.php");
  require($path . "helpers/request.php");
  require($path . "helpers/security.php");
  require($path . "helpers/query.php");
  require($path . "helpers/response.php");
  require($_SERVER['REQUEST_METHOD'] . ".php");
?>
