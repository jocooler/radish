<?php
require_once('helpers/functions.php');
require_once('helpers/endpoint.php');
require_once('product');

class Transaction extends Endpoint {
  /* Endpoint specific variables */
  protected $transactionId;
  protected $transactionType;
  protected $clerk;
  protected $clerkId;
  protected $customer;
  protected $customerId;
  protected $total;
  protected $time;
  protected $discount;
  protected $discountType;
  protected $payment;
  protected $paymentType;

  protected $products = array(); // array of Products, probably. product id, product name, quantity, regular price, special price

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
  protected $accessible_fields = array('transactionId', 'transactionType', 'clerk', 'customer', 'total', 'time', 'discount', 'discountType', 'payment', 'products');
  protected $expected_parameters = array();
  protected $required_parameters = array();

  public function execute();

  public function getProducts();
  /* Validation Methods */


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
