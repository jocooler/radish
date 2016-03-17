<?php
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

  /* Templates SQL Queries. These are for example only, please reimplement. */
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
  protected $get_products_query_string = "SELECT * FROM productsToTransactions WHERE transactionId=:transactionId ";
  protected $post_query_string;
  protected $put_query_string;
  protected $delete_query_string;
  protected $options_query_string;
  protected $accessible_fields = array('transactionId', 'transactionType', 'clerk', 'customer', 'total', 'time', 'discount', 'discountType', 'payment');
  protected $expected_parameters = array();
  protected $required_parameters = array();

  public function execute();


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
