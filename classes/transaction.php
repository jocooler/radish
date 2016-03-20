<?php
require_once('helpers/functions.php');
require_once('helpers/endpoint.php');
require_once('product.php');
require_once('user.php');
require_once('customer.php');

class Transaction extends Endpoint {
  /* Endpoint specific variables */
  protected $transactionId;
  protected $transactionType;
  protected $transactionTypeId;
  protected $transactionTypeEffect;
  protected $user; // TODO should be a user object.
  protected $customer; // TODO should be a customer object.
  protected $total;
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
      $product = new Product($id, $productData['idType']);
      if (array_key_exists($product->sku, $this->products)) {
        $this->products[$product->sku]['quantity'] += $productData['quantity'];
      } else {
        $this->products[$product->sku]['product'] = $product;
        $this->products[$product->sku]['quantity'] = $productData['quantity'];
      }
    }
    return true;
  }

  public function set_transactionType($type) {
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
        return true;
      }
    }

    return false;
  }

  public function set_transactionTypeEffect($effect) {
    $effect = intval($effect);
    $possibleValues(-1,0,1);
    if (in_array($effect, $possibleValues)) {
      $this->transactionTypeEffect = $effect;
      return true;
    }
    return false;
  }

  public function set_user(User $user) { //TODO take in a user id from security and make a user out of it. Or have security create the user.
    // $user = $this->create_user($user);
    $this->user = $user;
    $this->userId = $user->userId;
    return true;
  }

  public function set_customer(Customer $customer) { //TODO take in a customer name or id and make a customer out of it.
    $this->customer = $customer;
    $this->customerId = $customer->customerId;
    return true;
  }

  public function set_total($total) {
    if (is_numeric($total)) {
      $this->total = $total + 0;
    }
    return true;
  }

  public function set_time($time) {
    $dateTime = new DateTime($time);
    $this->time = $dateTime->getTimestamp();
    return true;
  }

  public function set_discounts(array $discounts) {
    foreach ($discounts as $discount) {
      if (is_a($discount,"Discount")) { //TODO take in discount ids and create discounts from them
        $this->discounts[] = $discount;
      }
    }
    return true;
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

    if (count($payment) > 0) {
      return true;
    }

    return false;
  }

}
?>
