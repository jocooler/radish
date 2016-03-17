<?php
class Security {
  public $allowed = false;
  public $user;
  public $user_id;

  private $request;
  private $password;
  private $secret;
  private $method;
  private $signature;

  public function __construct(Request $request){
    $this->request = $request;
    $this->method = $_SERVER['REQUEST_METHOD'];
    //TODO optimize order
    if ($this->get_user() &&
        $this->check_tampering() &&
        $this->check_permissions() &&
        $this->check_date()
      ) {
        $this->allowed = true;
      }
      // TODO maybe we should split here over to a response with the error code.
  }

  private function get_user() {
    if (!isset($_SERVER['PHP_AUTH_USER'])) {
      //TODO setup proper failure if not logged in
      header('WWW-Authenticate: Basic realm="Radish POS"');
      header($_SERVER['SERVER_PROTOCOL'] . ' 401 Unauthorized');
      echo 'You must authenticate to access this resource.';
      exit;
    } else {
      $this->user = $_SERVER['PHP_AUTH_USER'];
      $this->password = $_SERVER['PHP_AUTH_PW'];

      $user_query_template = 'SELECT username, password, secret, user_id FROM users WHERE username=:username';
      $query = new Query($user_query_template);
      $user_details = $query->execute(array('username'=>$this->username));

      if ($this->password != $user_details['password']){
        //TODO proper failure
        header('WWW-Authenticate: Basic realm="Radish POS"');
        header($_SERVER['SERVER_PROTOCOL'] . ' 401 Unauthorized');
        echo 'Incorrect credentials supplied.';
        exit;
      } else {
        $this->secret = $user_details['secret'];
        $this->user_id = $user_details['user_id'];
        return true;
      }
    }
  }

  private function check_date() {
    $max_age = new DateTime();
    $max_age->sub(new DateInterval('PT15M'));
    $timestamp = new DateTime($this->timestamp);
    if ($max_age < $timestamp && $timestamp < new DateTime()) {
      return true;
    }
    return false;
  }

  private function check_tampering() {
  	$computed_signature = $this->compute_signature();
  	if ($computed_signature == $this->request->parameters['signature'][0]) {
  		return true;
  	} else {
  		//TODO handle security issue
  		header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden', true, 403);
  		exit;
  	}
  }

  private function compute_signature() {
    /* message needs to be of the form:
    METHOD ENDPOINT TARGET1 TARGET2 TARGET3 PARAMETER1=VALUE PARAMETER2=VALUE PAYLOAD1=VALUE PAYLOAD2=VALUE SECRET_PASSPHRASE
    */
    $message = $this->method . ' ';
    $message = $this->request->endpoint . ' ';
    foreach ($this->request->targets as $target) {
      $message .= "$target ";
    }

    foreach ($this->request->parameters as $parameter=>$value) {
      //TODO parameters with multiple values are ; separated
      if ($parameter != 'signature') {
        $message .= "$parameter=" . implode(';', $value) . ' ';
      }
    }

    foreach ($this->request->body as $payload=>$value) {
      $message .= "$payload=$value" . implode(';', $value) . ' ';
    }

    $message .= "{$this->secret} ";
    $signature = md5($message);
    return $signature;
  }

  private function check_permissions() {
    //TODO redo this function
    $user_groups_template = 'SELECT group FROM users_to_groups WHERE user=:user_id';
    $groups_permissions_template = 'SELECT permission FROM permissions WHERE NAMETHISFIELD=:group_id AND type=group AND endpoint=:endpoint AND method=:method AND (target=:target OR target="all")';
    $user_permissions_template = 'SELECT permission FROM permissions WHERE NAMETHISFIELD=:user_id AND type=user AND endpoint=:endpoint AND method=:method AND (target=:target OR target="all")';
    $user_groups = new Query($user_groups_template, array('user'=>$this->user));

    foreach ($user_groups as $group_id) {
      $permissions_params =array('group_id'=>$group_id, 'endpoint'=>$this->endpoint, 'method'=>$this->method, 'target'=>$this->target);
      $permissions = new Query($groups_permissions_template, $permissions_params);
      if ($permissions) {
        return true;
      }
    }

    $permissions_params = array('user_id'=>$user_id, 'endpoint'=>$this->endpoint, 'method'=>$this->method, 'target'=>$this->target);
    $permissions = new Query($user_permissions_template, $permissions_params);
    if ($permissions) {
      return true;
    }

    return false;

    /*
    I think we need to:
    1. check user groups.
    2. check each group permissions.
      a. pass - check user denied permissions
      b. fail - check user-specific permissions.

    This flow can probably be optimized...we're making a LOT of queries.
    */
  }
}
?>
