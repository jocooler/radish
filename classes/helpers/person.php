<?php
require_once('helpers/functions.php');
require_once('helpers/endpoint.php');

abstract class Person extends Endpoint {
  protected $id;
  protected $first;
  protected $last;
  protected $birthday; // date(Y-m-d);
  protected $address;
  protected $address2;
  protected $city;
  protected $state;
  protected $zip;
  protected $phone;
  protected $email;
  protected $discounts = array();
  protected $accessibleFields = array('id', 'first', 'last', 'birthday', 'address', 'address2', 'city', 'state', 'zip', 'phone', 'email', 'discounts');

  public function __construct($id) {
    if (is_array($id)) {
      $this->set($id); // set all variables
      $this->post_construction($id);
    } else if ($id) {
      $this->set_id('id');
      $query = new Query($this->get_query_string, array('id' => $this->id));
      $this->set($query->results);
      $this->post_construction($query->results);
    }
  }

  public function post_construction($args) { // this is implemented by children if it's needed.

  }

  /* Validation Methods */
  public function set_id($id) {
    $this->id = $id;
  }

  public function set_first($first) {
    // Null ok, I guess.
    $this->first = $first;
  }

  public function set_last($last) {
    // must include at least one letter
    if (preg_match([a-zA-Z]+), $last)) {
      $this->last = $last;
    }
  }

  public function set_birthday($birthday) {
    try {
      $this->birthday = new DateTime($birthday);
    } catch {
      $this->birthday = '';
    }
  }

    public function set_address($address) {
      //I think it's sane to expect at 1 letter and 4 characters.
      // Might be a business name or a street address.
      if (preg_match([a-zA-Z]+), $address) && strlen($address) > 3) {
        $this->address = $address;
      }
    }

    public function set_address2($address) {
      if (strlen($address > 1)) { // if we have a line 2, expect at least 1 letter and 2 characters.
        if (preg_match([a-zA-Z]+), $address)) {
          $this->address2 = $address;
        }
      } else {
        $this->address2 = '';
      }
    }

    public function set_city($city) {
      // at least two letters in a city
      if (preg_match([a-zA-Z]{2,}), $city)) {
        $this->city = $city;
      }
    }

    public function set_state($state) {
      // at least two letters in a state
      if (preg_match([a-zA-Z]{2,}), $state)) {
        $this->state = $state;
      }
    }

    public function set_zip($zip) {
      // at least 3 numbers in a zip in US and CA. Not sure elsewhere...
      if (strlen($zip) > 4 && preg_match([0-9]{3,}), zip)) {
        $this->zip = $zip;
      }
    }

    public function set_phone($phone) {
      if (strlen($phone > 9)) {
        $this->phone = $phone;
      }
    }

    public function set_email($email) {
      $email = validate("email", $email, FILTER_VALIDATE_EMAIL);
      if ($email) {
        $this->email = $email;
      }
    }

    public function set_discounts($discounts) {
      if (is_array($discounts)) {
        $this->discounts = $discounts;
      } else if (is_int($discounts)) {
        $this->discounts[] = $discounts;
      } else {
        $discounts = unserialize($discounts);
        foreach ($discounts as $discount) {
          if (is_int($discount)) {
            $this->discounts[] = $discounts;
          }
        }
      }
    }
}
?>
