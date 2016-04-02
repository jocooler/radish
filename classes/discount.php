<?php
class Discount extends Endpoint {
  public $id;                   // discount ID
  private $group1 = array();    // an array of product skus
  private $group2 = array();    // an array of product skus
  private $discount = 0;        // a number representing the discount
  private $percentage = false;  // boolean flag. True = percentage. False = fixed.
  private $min = 0;             // minimum number to be applied
  private $max = 0;             // maximum times it can be applied
  private $combinable = false;  // boolean flag. True = can be combined. False = can't.
  private $stackable = false;   // can this coupon be used several times if the quantity thresholds are met more than once.
  private $automatic = false;   // is the discount meant to be applied automatically or only with a coupon?
  private $active = false;      // is the discount active?

  private $valid = false; // does the discount meet all constraints?
  private $child;         // holds a reference to a child class with the discount methods.

  public function __construct($discountId = false) {
    if (!$discountId || is_array($discountId)) {
      $this->updateDiscount($discountId);
    } else {
      $this->apply($discountId);
    }

  }

  private function updateDiscount($data) {

  }
  public function execute() {
    //TODO this is used in creating and updating discounts.
  }

  private function apply() {
    // TODO
    // retrieveDiscountData
    // construct a child of the proper type $child = new Discount_Blahblahblah;
    $this->child->apply();
  }

  abstract private function validate();
  abstract private function compute();

  // TODO helper validations go in here.
  // called by children.
  private function basicValidation() {
    if ($this->active && $this->group1 && $this->discount) {
      return true;
    }
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

  public function set_id($id) {
    if (is_int($id)) {
      $this->id = $id;
    }
  }

  public function set_group1(array $group1) {
    $valid = true;
    foreach ($group1 as $productId) {
      if (!is_int($productId)) {
        $valid = false;
      }
    }
    if ($valid) {
      $this->group1 = $group1;
    }
  }

  public function set_group2(array $group1) {
    $valid = true;
    foreach ($group2 as $productId) {
      if (!is_int($productId)) {
        $valid = false;
      }
    }
    if ($valid) {
      $this->group1 = $group2;
    }
  }

  public set_discount($discount) {
    if (is_numeric($discount)) {
      $this->discount = $discount;
    }
  }

  public function set_percentage(bool $percentage) {
    $this->percentage = $percentage;
  }

  public function set_min($min) {
    if (is_int($min)) {
      $this->min = $min;
    }
  }

  public function set_max($max) {
    if (is_int($max)) {
      $this->max = $max;
    }
  }

  public function set_combinable(bool $combinable) {
    $this->combinable = $combinable;
  }

  public function set_stackable(bool $stackable) {
    $this->stackable = $stackable;
  }

  public function set_automatic(bool $automatic) {
    $this->automatic = $automatic;
  }
  public function set_active(bool $active) {
    $this->active = $active;
  }
}

Class Simple_Discount extends Discount {
  private $validTypes = ["Product", "Transaction"];
  // a simple discount takes a product group and a discount only.
    //x-z     // $3 off each x
    //x*(1-z) // 20% off each x
}

Class Quantity_Discount extends Discount {
  private $validTypes = ["Transaction"];
  //min x-z*x   // $3 off each x if you buy min or more
  //min x-z     // $3 off if you buy x or more
  // min x*(1-z) // 20% off each x if you buy min or more
}

Class Bogo_Discount extends Discount {
  private $validTypes = ["Transaction"];
  /*
    buy min of x get x-z*x         // buy 2 get $3 off each additional one
    buy min of x1 get x2-z*x2      // buy 2 of x1, get $3 off each x2
    buy min of x get x*(1-z)       // buy 2 get 20% off each additional one
    buy min of x1 get x2*(1-z)     // buy 2 of x1, get 20% off each x2

    buy min of x get x-z*x limit     // buy 2 get $3 off each additional one up to the limit
    buy min of x get x*(1-z) limit   // buy 2 get 20% off each additional one up to the limit
    buy min of x1 get x2-z*x2 limit  // buy 2 of x1, get $3 off each x2 up to the limit
    buy min of x1 get x2*(1-z) limit // buy 2 of x1, get 20% off each x2 up to the limit

    bogo with stackable limits
    same as bogo limits with a stackable flag.*/
}

?>
