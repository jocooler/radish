<?php
require_once('helpers/person.php');

class User extends Person {
  protected $userGroup;
  protected $password;
  protected $salt;
  protected $passphrase;
  protected $username;
  private $passwordLength = 128;
  private $usernameMinLength = 8;
  private $passphraseWords = 6;


  protected $query = "";
  protected $get_query_string = "SELECT * FROM customers WHERE id=:id";
  protected $post_query_string = "INSERT INTO customers ";// TODO
  protected $put_query_string = "UPDATE WHERE id=:id"; // TODO
  protected $delete_query_string = "DELETE FROM customers WHERE id=:id";
  protected $options_query_string = ""; // TODO

  $this->accessible_fields[] = 'customerGroup';

  public function __construct($id) {
    parent::__construct($id);
  }

  public function post_construction($args) { // set customer specific parameters here.
    if (isset($args['userGroup'])) {
      $this->set_userGroup($args['userGroup']);
    }
  }


  public function hash_password($password) {
    return hash_pbkdf2('sha_512', $password, $this->salt, 64000, $this->passwordLength);
  }

  public function compare_password($password) {
    // TODO update security to use this.
    if ($this->hash_password($password) === $this->password) {
      return true;
    } else {
      return false;
    }
  }

  public function generate_passphrase() {
    // TODO use Paragonie for better rand, perhaps.
    $passphrase = array();
    $file = new SplFileObject(dirname(dirname(__FILE__)) . 'assets/diceware.list');
    for ($i=0; $i<=$this->passphraseWords; $i++) {
      $rand = rand(1, 7777);
      $file->seek($rand);
      $passphrase[] = $file->current();
    }

    $passphrase = implode(' ', $passphrase);
    $this->set_passphrase($passphrase);
    return $passphrase;
  }

  public function generate_salt() {
    $salt = md5($this->generate_passphrase());
    return $salt;
  }

  /* Validation Methods  - These are mostly in Person.php */

  public function set_userGroup($group) {
    if (is_int($group) {
      $this->userGroup = $group;
    } else {
      $user_group_query = "SELECT id FROM userGroups WHERE name LIKE %:group%";
      $query = new Query($user_group_query, array('group'=>$group));
      if (is_int($query->results['id'])) {
        $this->set_customerGroup($query->results['id']);
      }
      //TODO error handling
    }
  }

  public function set_password($password) {
    if (strlen($password) === $this->passwordLength) {
      $this->password = $password;
    }
  }

  public function set_salt($salt) {
    if (strlen($salt) === 32) { // salts are an md5 hash, so 32 chars.
      $this->salt = $salt;
    }
  }

  public function set_passphrase($passphrase) {
    if (strlen($passphrase) < $this->passphraseWords * 3) {
      $this->passphrase = $passphrase;
    }
  }

  public function set_username($username) {
    if (strlen($username) >= $this->usernameMinLength) {
      $this->username = $username;
    }
  }

}
?>
