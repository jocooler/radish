<?php


$app['file'] = __FILE__;
$app['root'] = substr($app['file'], 0, strpos($app['file'], 'api/') + 4);

//each class of thing has to have a regex to parse its arguments.
// skus: ^([0-9a-zA-Z_\-;])+


require($app['root'] . "/include.php");

if ($app['include']) {
  print_r($app['include']);
  $included_fields = 'p.';
  $included_fields .= join(', p.', $app['include']);
} else {
  $included_fields = '*';
}

/* execute query and get results */
$mysql = connect_db();

for ($i =0; $i<count($app['targets']); $i++) {

  $result = $mysql->query("SELECT " . $included_fields . " FROM products p, burlingtonProducts sp WHERE upc = " . $mysql->escape_string($app['targets'][$i]) . " AND sp.sku = p.sku");
  echo "SELECT " . $included_fields . " FROM products p, burlingtonProducts sp WHERE upc = " . $mysql->escape_string($app['targets'][$i]) . " AND sp.sku = p.sku";
  while ($row = $result->fetch_assoc()) {
    $results[] = ($row);
  }
}

$results = json_encode($results);

echo($results);
?>
