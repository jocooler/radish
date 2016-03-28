<?php
class Discount extends Endpoint { // TODO endpoint extension
  public $discountType;
  public $id;
  private $group1;
  private $group2;
  private $discount;
  private $percentage;
  private $min;
  private $max;
  private $stackable;
  private $automatic;
  private $active;

  private $branch;

  Discount::apply();

  public function __construct($discountId = '') {
    //retrieveDiscountData
  }

  private function apply_to_product($product) {
    if (is_array($product)) {
      foreach ($product as $p) {
        $this->apply_to_product($p);
      }
    } else if (is_a($product, "Product")) {
      $this->validate($product);
      $product->price = $this->compute_new_price($product->price);
    } else {
      throw ("Discount::apply_to_product expects a product or array of products.");
    }
  }

  private function apply_to_transaction_total($transaction) {
    if (is_a($transaction, "Transaction")) {
      // should we compute a discountable total?
      // or should we apply it to all products that are discountable?
      // Let's just be naive, because transaction dicounts can be applied only to the whole transaction.
      $transaction->final = $this->compute_new_price($transaction->final);
    } else {
      throw ("Discount::apply_to_transaction_total expects a transaction.");
    }
  }

  private function apply_to_valid_products($transaction) {
    if (is_a($transaction, "Transaction")) {
      $this->validate($transaction->products);
      foreach ($transaction->products as $product_wrapper) {
        $this->compute_new_price($product_wrapper['product']->price);
      }
    } else {
      throw ("Discount::apply_to_valid_products expects a transaction.")
    }
  }

  /* kinds of discounts:
  x = product group of 1+ products
  z = discount

  discounts can be either fixed or a percentage

  Product Discounts
  simple discounts:
  x*(1-z) // 20% off each x
  x-z     // $3 off each x

  quantity discounts:
  min x*(1-z) // 20% off each x if you buy min or more
  min x-z*x   // $3 off each x if you buy min or more
  min x-z     // $3 off if you buy x or more

  bogo discounts:
  buy min of x get x-z*x         // buy 2 get $3 off each additional one
  buy min of x get x*(1-z)       // buy 2 get 20% off each additional one
  buy min of x1 get x2-z*x2      // buy 2 of x1, get $3 off each x2
  buy min of x1 get x2*(1-z)     // buy 2 of x1, get 20% off each x2

  bogo with limits
  buy min of x get x-z*x limit     // buy 2 get $3 off each additional one up to the limit
  buy min of x get x*(1-z) limit   // buy 2 get 20% off each additional one up to the limit
  buy min of x1 get x2-z*x2 limit  // buy 2 of x1, get $3 off each x2 up to the limit
  buy min of x1 get x2*(1-z) limit // buy 2 of x1, get 20% off each x2 up to the limit

  bogo with stackable limits
  same as bogo limits with a stackable flag.

  I think we need these fields:
  productGroup1, productGroup2, discount, discountType (percentage = true, fixed is false), minimum, limit/max, stackable
  */

  public function apply($products) {
    //products: array(sku123=>array('product'=>Product->price, 'quantity'=>2))
  }

  public function execute($price) {
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
