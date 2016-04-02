<?php
class Discount extends Endpoint { // TODO endpoint extension
  public $id;           // discount ID
  private $group1;      // an array of product skus
  private $group2;      // an array of product skus
  private $discount;    // a number representing the discount
  private $percentage;  // boolean flag. True = percentage. False = fixed.
  private $min;         // minimum number to be applied
  private $max;         // maximum times it can be applied
  private $combinable;  // boolean flag. True = can be combined. False = can't.
  private $stackable;   // can this coupon be used several times if the quantity thresholds are met more than once.
  private $automatic;   // is the discount meant to be applied automatically or only with a coupon?
  private $active;      // is the discount active?

  private $valid = false; // does the discount meet all constraints?

  public function __construct($discountId = '') {
    //retrieveDiscountData
  }
  public function execute() {
    
  }

  public function apply($target) {
    if (is_a($target, "Product")) {
      $this->apply_to_product($target);
    } else if (is_a($target, "Transaction")) {
      $this->apply_to_transaction($target);
    } else if (is_array($target)) {
      $this->apply_to_product_array($target);
    }
  }

  private function apply_to_product(Product $product) {
    $this->validate($product);
    $product->price = $this->compute_new_price($product->price);
  }

  private function apply_to_transaction(Transaction $transaction) {
    $discountable = $this->compute_discountable_total($transaction);
    $transaction->final = $this->compute_new_price($discountable);
  }

  private function apply_to_product_array(array $products) { // takes in transaction->products
    foreach ($products as $product_wrapper) {
      if (!is_a($product_wrapper['product'], "Product")) {
        throw ('Discount::apply_to_valid_products expects an array of products from $transaction->products.');
      }
    }
    $this->validate($products);
    foreach ($transaction->products as $product_wrapper) {
      $this->compute_new_price($product_wrapper['product']->price);
    }
  }

  public function compute_new_price($price) {
    /* This needs to be a decision tree. */
    if (!$this->valid) {
      return $price;
    }
    /* kinds of discounts:
    x = product group of 1+ products
    z = discount

    discounts can be either fixed or a percentage
    */

    /* simple discounts:
    x*(1-z) // 20% off each x
    x-z     // $3 off each x
    */
    return $this->simple_percentage($price);
    return $this->simple_fixed($price);

    /*
    quantity discounts:
    min x*(1-z) // 20% off each x if you buy min or more
    min x-z*x   // $3 off each x if you buy min or more
    min x-z     // $3 off if you buy x or more
    */
    return $this->simple_percentage($price);
    return $this->simple_fixed($price);

    /*
    bogo discounts:
    buy min of x get x-z*x         // buy 2 get $3 off each additional one
    buy min of x get x*(1-z)       // buy 2 get 20% off each additional one
    buy min of x1 get x2-z*x2      // buy 2 of x1, get $3 off each x2
    buy min of x1 get x2*(1-z)     // buy 2 of x1, get 20% off each x2
    */
    return $this->bogoFixed($price); //TODO how to apply this?
    return $this->bogoPercentage($price);
    // TODO probably add a counter and check if it's more than Min. If so, apply it.

    /*
    bogo with limits
    buy min of x get x-z*x limit     // buy 2 get $3 off each additional one up to the limit
    buy min of x get x*(1-z) limit   // buy 2 get 20% off each additional one up to the limit
    buy min of x1 get x2-z*x2 limit  // buy 2 of x1, get $3 off each x2 up to the limit
    buy min of x1 get x2*(1-z) limit // buy 2 of x1, get 20% off each x2 up to the limit
    */

    /*
    bogo with stackable limits
    same as bogo limits with a stackable flag.
    */

    /*
    I think we need these fields:
    productGroup1, productGroup2, discount, discountType (percentage = true, fixed is false), minimum, limit/max, stackable
    */
    return $price;
  }

  public static function sort(array $discounts, array $products) {
    //discounts: array(Discount1, Discount2...)
    //products: array(sku123=>array('product'=>Product->price, 'quantity'=>2))
    foreach ($discounts as $discount) {
      // we need to figure out the order in which to apply discounts.
      // I think it's:
      // products fixed, highest to low
      // products percentage, highest to low
      // transaction fixed, highest to low
      // transaction percentage, high to low
      // user fixed, high to low
      // user percentage, high to low
      // bogo needs to check value of items.

      /* more accurately:
      1. Count how many products each discount applies to.
        a. get the group number for each discount.
        b. select * from productsToGroups where sku = Product->sku AND groups IN (each discount group number)
        c. TODO: some discounts may not be combined. This should be taken into account.
        TODO maybe we need a super field for things like employee discounts that can be combined potentially even if others cannot.
        TODO super discounts might include a birthday discount off of a sale item.
        TODO maybe we need to rethink birthday money as a payment and not as a discount.
      2. If there are ties
        a. percentages first
          i. sort percentages descending
        b. fixed next
          i. sort fixed descending
      */

    }
  }

}
?>
