<?php
require_once('helpers/person.php');

class User extends Person {
  protected $userGroup;
  protected $password;
  protected $salt;
  protected $passphrase;
  protected $username;


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
}
?>
