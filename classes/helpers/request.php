<?php

class Request {
  public $parameters = array(); // this is an array of arrays. If something only has one value, it can be accessed by $Request->parameters['key'][0].
  public $targets = array();
  public $body = array();
  public $target_required;
  public $allowed;
  public $error;
  public $endpoint;

  private $request;
  private $required;
  private $optional;
  private $get;

  public function __construct(array $optional = array(), array $required = array('signature', 'timestamp'), $target_required = true) {
    $this->request = $_SERVER['REQUEST_URI'];
    $this->required = $required;
    $this->target_required = $target_required;
    $this->optional = $optional;
    $this->endpoint = substr($this->request, 0, strrpos($this->request, '/'));
    $this->get = $_GET;

    if (!$this->parse_request()) {
      //TODO error handling
      echo $this->error;
      die();
    }

    if (!$this->check_security()) {
      // TODO error handling;
      echo $this->error;
      die();
    }
  }

  private function parse_request() {
    // http://site.dev/api/product/upc/772029384;800759102242?signatre= timestamp=
    // [REQUEST_URI] => /api/product/upc/772029384;800759102242?include=sku;name
    // [QUERY_STRING] => 772029384;800759102242&include=sku;name

    foreach ($this->get as $key => $value) {
      if ($value == '') {
        $keys = explode(';', $key);
        $this->targets = array_merge($this->targets, $keys);
      } else if (in_array($key, $this->required) || in_array($key, $this->optional)) {
        $this->parameters[$key] = explode(';', $value);
      }
    }

    if (!$this->ensure_requirements()) {
      $this->error = "Not all required parameters were supplied.";
      return false;
    }

    if ($_SERVER['REQUEST_METHOD'] = 'PUT') {
      parse_str(file_get_contents("php://input"),$this->body);
    } else if ($_SERVER['REQUEST_METHOD'] = 'POST') {
      $this->body = $_POST;
    }

    return true;
  }

  private function ensure_requirements() {
    foreach ($this->required as $required) {
      if (!array_key_exists($required, $this->parameters)) {
        return false;
      }
    }
    if ($this->target_required && count($this->targets) == 0) {
      return false;
    }
    return true;
  }

  private function check_security() {
    //TODO implement security.
    //$security = new Security($this);
    $this->allowed = true; //$security->allowed;
    return $this->allowed;
  }

}

?>
