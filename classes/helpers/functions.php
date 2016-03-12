<?php
function validate($name, $var, $filter, $options = array()) {
  $filtered = filter_var($var, $filter, $options);
  if ($filtered) {
    return $filtered;
  } else {
    throw new Exception("$name of incorrect type. Must pass $filter");
  }
}
?>
