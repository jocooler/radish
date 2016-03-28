<?php
require_once('helpers/functions.php');
require_once('helpers/endpoint.php');
require_once('product.php');
require_once('user.php');
require_once('customer.php');

class Transaction extends Endpoint {
  /* Endpoint specific variables */
  protected $id;
  protected $type;
  protected $typeId;
  protected $typeEffect;
  protected $user; // TODO should be a user object.
  protected $customer; // TODO should be a customer object.
  protected $total;
  protected $final; //price after discounts. Initially set equal to total.
  protected $time;
  protected $discounts = array(); // array of Discounts
  protected $payment = array();

  protected $products = array(); // array(sku123=>array('product'=>Product, 'quantity'=>2))

  /* Template SQL Queries. These are for example only, please reimplement in each endpoint. */
  protected $query;
  protected $get_query_string =
      "SELECT
          t.transactionId,
          tt.transactionType,
          u.name as clerk,
          c.name as customer,
          t.total,
          t.time,
          t.discount,
          p.paymentType as payment
      FROM
          transactions t,
          transactionTypes tt,
          users u,
          customers c,
          paymentTypes p
      WHERE
          tt.id = t.transactionType AND
          u.id = t.userId AND
          c.id = t.customerId AND
          p.id = t.paymentType AND
          t.transactionId = :transactionId";
  protected $get_products_query_string = "SELECT * FROM productsToTransactions WHERE transactionId = :transactionId";

  protected $post_query_string =
      "INSERT INTO `transactions`(`transactionId`, `userId`, `customerId`, `total`, `time`, `discounts`, `paymentType`, `transactionType`)
      VALUES (:transactionId, :userId, :customerId, :total, :time, :discounts, :paymentType, :transactionType)"; // if we tweak this to do multiple values statements, doing the select statments once will be faster than doing them many times.
  protected $post_products_query_string =
      "INSERT INTO `productsToTransactions`(`sku`, `transactionId`, `quantity`, `originalPrice`, `actualPrice`)
      VALUES (:sku, :transactionId, :quantity, :originalPrice, :actualPrice)";

  protected $put_query_string = ''; // we aren't using PUT with transactions
  protected $delete_query_string = ''; // we aren't using DELETE with transactions
  protected $options_query_string = ''; //TODO
  protected $accessible_fields = array('transactionId', 'transactionType', 'user', 'customer', 'total', 'time', 'discounts', 'payment', 'products');
  protected $expected_parameters = array();
  protected $required_parameters = array(); // we need either IDs or types.


  public function __contstructor($id = false) {
    if ($id !== false) {
      $this->set_transactionId($id);
    }
    $this->execute();
  }

  public function execute(); //just a reminder.

  public function check_total() {
    $this->discounts = Discount::sort_discounts($this->discounts);
    foreach ($this->discounts as $discount) {
      $discount->apply($this->products);
    }

    foreach ($products as $product) {
      $price = $product->retail;
    }
  }

  /* Validation Methods */
  public function set_products(array $products) {
    //products arrive as array('id'=>array('type' => 'idType', 'quantity' => 'quantity'));
    foreach ($products as $id=>$productData) {
      if (!is_a($productData['product'], "Product")) {
        $product = new Product($id, $productData['idType']);
      }
      if (array_key_exists($product->sku, $this->products)) {
        $this->products[$product->sku]['quantity'] += $productData['quantity'];
      } else {
        $this->products[$product->sku]['product'] = $product;
        $this->products[$product->sku]['quantity'] = $productData['quantity'];
      }
    }
  }

  public function set_id($id) {
    $id = validate("id", $id, FILTER_VALIDATE_INT);
    if ($id) {
      $this->transactionId = $id;
    }
  }

  public function set_type($type) {
    if (is_string($type)) {
      $query = new Query("SELECT * FROM transactionTypes");
      $query->execute(array());
    } else if (is_int($type)) {
      $query = new Query("SELECT * FROM transactionTypes WHERE id=:id");
      $query->execute(array('id'=>$type));
    }

    foreach ($query->results as $transactionType) {
      if ($transactionType['name'] == $type && $this->set_transactionTypeEffect($transactionType['effect'])) {
        $this->transactionType = $transactionType['name'];
        $this->transactionTypeId = $transactionType['id'];
      }
    }
  }

  public function set_typeEffect($effect) {
    $effect = intval($effect);
    $possibleValues(-1,0,1);
    if (in_array($effect, $possibleValues)) {
      $this->transactionTypeEffect = $effect;
    }
  }

  public function set_user($user) {
    if (!is_a($user, 'User')) {
      $user = new User($user);
    }
    $this->user = $user;
  }

  public function set_customer($customer) {
    if (!is_a($customer, 'Customer')) {
      $customer = new Customer($customer);
    }
    $this->customer = $customer;
  }

  public function set_total($total) {
    if (is_numeric($total)) {
      $this->total = $total + 0;
    }
  }

  public function set_time($time) {
    $dateTime = new DateTime($time);
    $this->time = $dateTime->getTimestamp();
  }

  public function set_discounts(array $discounts) {
    foreach ($discounts as $discount) {
      if (!is_a($discount,"Discount")) {
        $discount = new Discount($discount);
      }
      $this->discounts[] = $discount;
    }
  }

  public function set_payment($payment) {
    $query = new Query("SELECT * FROM paymentTypes");
    $query->execute(array());
    $results = $query->results;

    foreach ($payment as $method=>$amount) { //TODO maybe make payments something that can have modifiers applied, for example, exchange rates
      if (is_numeric($amount) && (
              $results['name'] == $method ||
              $results['id'] == $method
        )) {
        $this->payment[] = array($results['id'] => array('name' => $results['name'], 'amount' => $amount + 0);
      }
    }
  }

}
?>
