<?php
abstract class Endpoint {
  protected $query;
  protected $get_query_string;
  protected $post_query_string;
  protected $put_query_string;
  protected $delete_query_string;
  protected $options_query_string;
  protected $accessible_fields = array();
  protected $expected_parameters = array();
  protected $required_parameters = array();

  public function execute();

  public function __get($name) {
    // use this so that we can still fetch properties but can't set them directly.
    // this can also take an array of properties to get.
    // used like $product->wholesale or $product->all

    if (is_array($name)) {
      //if it's an array, cycle through it and get each of the properties recursively.
      // this is mostly for the $product->get alias, like $product->get(array('wholesale','retail'))
      $return_values = array();
      foreach($name as $property) {
        $return_values[$property] = $this->__get($property);
        //$this->{$property};
      }
      return $return_values;
    } else if ($name == 'all') {
      // Use the all keyword to get a set of predefined properties.
      return $this->__get($this->accessible_fields);
    } else {
      // we're getting a single value, so just grab it if it's in our whitelist.
       if (in_array($name, $this->accessible_fields)) {
        return $this->$name;
       } else {
        return false;
      }
    }
  }

  public function get($name) {
    //an alias for __get. Call it like $product->get('all');
    return $this->__get($name);
  }

  public function set(array $values) {
    //takes a 2d array and sets the values in it.
    foreach ($values as $key=>$value) {
      $set_function = "set_" . $key;
      if (method_exists($this, $set_function)) {
         $this->$set_function($value);
      }
    }
  }

  public function get_expected_parameters() {
    return $this->expected_parameters;
  }

  public function set_expected_parameters(array $parameters) {
    $this->expected_parameters = $parameters;
    return $this->expected_parameters;
  }

  public function get_required_parameters() {
    return $this->required_parameters;
  }

  public function set_required_parameters(array $parameters) {
    $this->required_parameters = $parameters;
    return $this->required_parameters;
  }
}
?>
