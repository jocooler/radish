<?php
class Response {
  public $header = array();
  public $body;
  public $format = 'json';
  public $data;
  public $include = array();
  public $exclude = array();
  public $status = 200;

  public function __construct() {
    //TODO this overloading needs work - what about status, data, include? Or just data include?
    switch (func_num_args()) {
      case 0: //they will have to enter everything themselves.
        break;
      case 1: // just data.
        $this->construct_from_data(func_get_arg(0));
        break;
      case 2:
        if (!is_array(func_get_arg(1))) { // setting header and response manually
          $this->construct_from_header_body(func_get_arg(0), func_get_arg(1));
        } else { // status code and data
          $this->construct_from_status_data(func_get_arg(0), func_get_arg(1));
        }
        break;
      case 3: // data, include, format
        $this->construct_from_data_include_format(func_get_arg(0), func_get_arg(1), func_get_arg(2));
        break;
      case 4: // data, include, exclude, format
        $this->construct_from_data_include_exclude_format(func_get_arg(0), func_get_arg(1), func_get_arg(2), func_get_arg(3));
        break;
      default:
        //TODO error, wrong number of args.
    }
  }
  private function construct_from_data(array $data) {
    $this->data = $data;
    $this->set_header(200);
    $this->set_body();
  }

  private function construct_from_header_body(array $header, $body) {
    $this->header = $header;
    $this->body = $body;
  }

  private function construct_from_status_data($status, $data) {
    $this->set_header($status);
    $this->set_body($data);
  }

  private function construct_from_data_include_format(array $data, array $include, $format) {
    $this->data = $data;
    $this->include = $include;
    $this->format = $format;
    $this->set_header(200);
    $this->set_body();
  }

  private function construct_from_data_include_exclude_format(array $data, array $include, array $exclude, array $format) {
    $this->data = $data;
    $this->include = $include;
    $this->exclude = $exclude;
    $this->format = $format;
    $this->set_header(200);
    $this->set_body();
  }

  public function set_header($status = '', $type = "application/json"){
    if ($status == '') {
       $status = $this->status;
    }
    $this->header[] = "HTTP/1.1 $status {$this->getMessage($status)}";
    $this->header[] = "Content-Type: $type";
  }

  public function set_body(array $data = array(), array $include = array(), array $exclude = array()){
    if (count($data)) $this->data = $data;
    if (count($include)) $this->include = $include;
    if (count($exclude)) $this->exclude = $exclude;

    $this->data = $this->filter();
    if (!$this->format($this->data)) {
      //TODO it errored.
    }

  }

  public function filter(array $raw = array(), array $include = array(), array $exclude = array()) {

    if (count($raw) == 0) {
      $raw = $this->data;
    }
    if (count($include) == 0) {
      $include = $this->include;
    }
    if (count($exclude) == 0) {
      $exclude = $this->exclude;
    }
    return $raw; // filter needs to be handled in each endpoint, not in the response. TODO
    $filtered_data = $raw;
    if (count($include)) {
      $filtered_array = array_filter($filtered_data, function ($key) use ($include) {print_r($key); return in_array($key, $include);}, ARRAY_FILTER_USE_KEY);
    }

    if (count($exclude)) {
      $filtered_array = array_filter($filtered_data, function ($key) use ($exclude) {return !in_array($key, $exclude);}, ARRAY_FILTER_USE_KEY);
    }
    return $filtered_array;
  }

  public function format(array $data, $type = "json"){
    $this->format = $type;
    $format_function = 'format_' . $this->format;
    if (!$this->$format_function($data)) { //TODO maybe better with try catch
      return $this->format_json($data);
    }
    return true;
  }

  private function format_json(array $data){
    $formatted_json = json_encode($data);
    $this->body = $formatted_json;
    return true;
  }


  public function send(){
    foreach ($this->header as $header) {
      header($header);
    }
    echo $this->body;
  }

  private function getMessage($status) {
    //TODO list of status messages to go with each code...
    switch ($status) {
      case 200: return "OK"; break;
      case 201: return "Created"; break; // should contain Location:URI and link url
      case 202: return "Accepted"; break; // will process async. See location.
      case 204: return "No Content"; break; // put, post or delete. don't change the UI, cause there's nothing here.
      case 205: return "Reset Content"; break; // used after submitting form to tell UI to reset.
      case 206: return "Partial Content"; break; // probably won't use.
      case 300: return "Multiple Choices"; break; // another version is available
      case 301: return "Moved Permanently"; break;
      case 303: return "See Other"; break; // used for cannonicalizing URLs,
      case 304: return "Not Modified"; break; // no body
      case 307: return "Temporary Redirect"; break; // didn;t do the put/post/delete, please try again at Location: URI
      case 400: return "Bad Request"; break;
      case 401: return "Not Authorized"; break; // send WWW-Authenticate
      case 402: return "Payment Required"; break; // not used
      case 403: return "Forbidden"; break; // didn't want to do the job - because time of day or ip.
      case 404: return "Not Found"; break;
      case 405: return "Method Not Allowed"; break; // response header should include Allow: GET, POST etc.
      case 406: return "Not Acceptable"; break; // we don't have what you want. You should order the blue one.
      case 409: return "Conflict"; break; // when you can't delete something because it contains stuff.
      case 410: return "Gone"; break; // when we don't know where it is, it's just gone.
      case 412: return "Precondition Failed"; break; // something you asked for (like only change if...) isn't possible
      case 413: return "Request Entity Too Large"; break; // not used.
      case 414: return "Request-URI Too Long"; break;
      case 415: return "Unsupported Media Type"; break; // I don't understand what you're saying!
      case 416: return "Reqested Range Not Satisfiable"; break;
      case 500: return "Internal Server Error"; break;
      case 501: return "Not Implemented"; break; // you send a method we don't understand
      case 503: return "Service Unavailable"; break; //probably for mysql errors.
      default: return "I'm a Teapot";
    }
  }

}
?>
