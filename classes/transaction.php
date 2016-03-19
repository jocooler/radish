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
  protected $userId;
  protected $customer; // TODO should be a customer object.
  protected $customerId;
  protected $total;
  protected $time;
  protected $discount; // TODO discount object
  protected $discountType;
  protected $payment;
  protected $paymentType;

  protected $products = array(); // array of Products, probably. product id, product name, quantity, regular price, special price
  protected $product_skus = array(); //sku=>(discount=>'',discountType=>'')

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
          t.discountType,
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
      "INSERT INTO `transactions`(`transactionId`, `userId`, `customerId`, `total`, `time`, `discount`, `discountType`, `paymentType`, `transactionType`)
      VALUES (:transactionId, :userId, :customerId, :total, :time, :discount, :discountType, :paymentType, :transactionType)"; // if we tweak this to do multiple values statements, doing the select statments once will be faster than doing them many times.
  protected $post_products_query_string =
      "INSERT INTO `productsToTransactions`(`sku`, `transactionId`, `quantity`, `originalPrice`, `discount`, `discountType`)
      VALUES (:sku, :transactionId, :quantity, :originalPrice, :discount, :discountType)";

  protected $put_query_string = ''; // we aren't using PUT with transactions
  protected $delete_query_string = ''; // we aren't using DELETE with transactions
  protected $options_query_string = ''; //TODO
  protected $accessible_fields = array('transactionId', 'transactionType', 'user', 'customer', 'total', 'time', 'discount', 'discountType', 'payment', 'products');
  protected $expected_parameters = array();
  protected $required_parameters = array(); // we need either IDs or types.

  public function execute(); //just a reminder.

  public function set_products(array $products) {
    foreach ($this->product_skus as $sku=>$discounts) {
      $product = new Product($sku);
      $product->set_discount($discounts['discount'], $discounts['discountType']);
      $this->products[] = $product;
    }
  }

  /* Validation Methods */
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

  public function set_user(User $user) {
    $this->user = $user;
    $this->userId = $user->userId;
    return true;
  }

  public function set_customer(Customer $customer) {
    $this->customer = $customer;
    $this->customerId = $customer->customerId;
    return true;
  }

  public function set_total() {
    //compute total
  }

  public function set_time($time) {

  }

  public function set_discount(Discount $discount) {
    // Discount
    // discountType
  }

  public function set_payment($payment) {
    //int
    //string
  }




/*
  `transactionId` int(11) NOT NULL,
  `clerkId` int(11) DEFAULT NULL,
  `customerId` int(11) DEFAULT NULL,
  `total` decimal(9,2) DEFAULT NULL,
  `time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `discount` decimal(9,2) DEFAULT NULL,
  `discountType` int(11) DEFAULT NULL,
  `payment` int(11) DEFAULT NULL,
  `transactionType` int(11) DEFAULT NULL
*/
}
?>
