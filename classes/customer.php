<?php
require_once('helpers/person.php');

class Customer extends Person {
  protected $customerGroup;

  protected $query = "";
  protected $get_query_string = "SELECT * FROM customers WHERE id=:id";
  protected $post_query_string = "INSERT INTO customers ";// TODO
  protected $put_query_string = "UPDATE WHERE id=:id"; // TODO 
  protected $delete_query_string = "DELETE FROM customers WHERE id=:id";
  protected $options_query_string = ""; // TODO

  $this->accessible_fields[] = 'customerGroup';

  public function __construct($id) {
    if (is_array($id)) {
      $this->set($id);
    } else if ($id) {
      $this->set_id('id');
      $query = new Query($this->get_query_string, array('id' => $this->id));
      $this->set($query->results);
    }
  }
  /* Validation Methods  - These are mostly in Person.php */

  public function set_customerGroup($group) {
    if (is_int($group) {
      $this->customerGroup = $group;
    } else {
      $cusomter_group_query = "SELECT id FROM customerGroups WHERE name LIKE %:group%";
      $query = new Query($customer_group_query, array('group'=>$group));
      if (is_int($query->results['id'])) {
        $this->set_customerGroup($query->results['id']);
      }
      //TODO error handling
    }
  }
}
?>
