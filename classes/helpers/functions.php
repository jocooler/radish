<?php
function validate($name, $var, $filter, $options = array()) {
  $filtered = filter_var($var, $filter, $options);
  if ($filtered !== false) {
    return $filtered;
  } else if ($options == FILTER_NULL_ON_FAILURE && !$filtered){
    //supposed to be boolean, and is false.
    return $filtered;
  } else {
    throw new Exception("$name of incorrect type. Must pass $filter");
  }
}
?>
