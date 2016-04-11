<?php
class Source extends Endpoint {
  protected $id;
  protected $name;
  protected $phone;
  protected $email;
  protected $notes;

  /* Validation Classes */

  public function set_id($id) {
    if (is_int($id)) {
      $this->id = $id;
    }
  }

  public function set_name($name) {
    if (!is_null($name)) {
      $this->name = $name;
    }
  }

  public function set_phone($phone) {
    $this->phone = $phone;
  }

  public function set_email($email) {
    $email = validate("email", $email, FILTER_VALIDATE_EMAIL);
    if ($email) {
      $this->email = $email;
    }
  }

  public function set_notes($notes) {
    $this->notes = $notes;
  }
}

?>
