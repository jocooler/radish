<?php
class Discount extends Endpoint {
  public $id;                   // discount ID
  protected $group1 = array();    // an array of product skus
  protected $group2 = array();    // an array of product skus
  protected $discount = 0;        // a number representing the discount
  protected $percentage = false;  // boolean flag. True = percentage. False = fixed.
  protected $floor = 0;           // minimum number to achieve the discount
  protected $ceiling = 0;         // maximum that can be discounted
  protected $bogo = false;        // boolean flag are we doing a buy x get x?
  protected $combinable = false;  // boolean flag. True = can be combined. False = can't.
  protected $stackable = false;   // can this coupon be used several times if the quantity thresholds are met more than once.
  protected $automatic = false;   // is the discount meant to be applied automatically or only with a coupon?
  protected $active = false;      // is the discount active?

  public $target;        // holds the target object.
  protected $valid = false; // does the discount meet all constraints?
  private $child;         // holds a reference to a child class with the discount methods.

  public function __construct($data = array()) {
    // this constructor is for creating new discounts.
    // call like $discount = new Discount();
    if (count($data)) {
      $this->set($data);
    }
  }

  public static function init($id, $target) {
    // this is the constructor for applying a discount
    // call like $discount = Discount::init($id, $this);
    // or $discount = Discount::init($id, $this->products[1223]['product']);
    // TODO
    // retrieveDiscountData
    // figure out which kind it is from the database query.
    $type = "Simple";
    if ($results['bogo']) {
      $type = "Bogo";
    } else if (count($results['group1']) > 0) {
      $type = "Quantity";
    }

    // construct a child of the proper type $child = new Simple_Discount();
    $child = new $type."_Discount"($id, $target);
    $child->set($results);
    // return an instance of the child class
    return $child;
  }

  public function execute() {
    //TODO this is used in creating and updating discounts.
  }


  abstract public function validate();
  abstract public function compute();

  protected function discountProduct(Product $product, $amount = $this->discount, $percentage = $this->percentage) {
    if ($percentage) {
      $product->price = $product->price * $amount/100;
    } else {
      $product->price = $product->price - $amount;
    }
  }

  protected function sortOnPrices(Product $a, Product $b) {
    return $a->price - $b->price;
  }

  // TODO helper validations go in here.
  // called by children.
  protected function basicValidation() {
    if ($this->active && $this->group1 && $this->discount) {
      return true;
    }
    return false;
  }

  protected function targetValidation() {
    if ($this->validTypes) {
      foreach ($validTypes as $type) {
        if (is_a($this->target, $type)) {
          return true;
        }
      }
    }
    return false;
  }

  protected function checkInGroup(array $products, array $group) {
    // determine if products is a list of products (multi-dimensional) or one dimensional
    $foundProducts = array();
    foreach ($products as $product) {
      if (in_array($product->sku, $group)) {
        array_push($foundProducts, $product);
      }
    }
    return $foundProducts;
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

  public function set_discount($discount) {
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
  // a simple discount takes a product group and a discount only.
  //x-z     // $3 off each x
  //x*(1-z) // 20% off each x
  private $validTypes = ["Product", "Transaction"];
  public function __construct($id, $target) {
    $this->set_id($id);
    $this->target = $target;
  }

  public function apply() {
    if (is_a($this->target, "Product")) {
      if ($this->validate()) {
        $this->discountProduct($this->target);
      }
    } else if (is_a($this->target, "Transaction")) {
      foreach ($this->target->products as $productHolder) {
        $product = $productHolder['product'];
        if ($this->validate()) {
          $this->discountProduct($product);
        }
      }
    }
  }

  public function validate() {

  }

}

Class Quantity_Discount extends Discount {
  // floor x-z*x  (max) // $3 off each x if you buy min or more
  // floor x-z  (max)   // $3 off if you buy x or more
  // floor x*(1-z) (max)// 20% off each x if you buy min or more
  private $validTypes = ["Transaction"];

  public function __construct($id, $target) {
    $this->set_id($id);
    $this->target = $target;
  }

  public function apply() {
  }

  public function validate() {

  }


}

Class Bogo_Discount extends Discount {
  /*
    buy floor of x get x-gap-z*x-gap         // buy 2 get $3 off each additional one
    buy min of x1 get x2-z*x2      // buy 2 of x1, get $3 off each x2
    buy min of x get x*(1-z)       // buy 2 get 20% off each additional one
    buy min of x1 get x2*(1-z)     // buy 2 of x1, get 20% off each x2

    buy min of x get x-z*x limit     // buy 2 get $3 off each additional one up to the limit
    buy min of x get x*(1-z) limit   // buy 2 get 20% off each additional one up to the limit
    buy min of x1 get x2-z*x2 limit  // buy 2 of x1, get $3 off each x2 up to the limit
    buy min of x1 get x2*(1-z) limit // buy 2 of x1, get 20% off each x2 up to the limit

    bogo with stackable limits
    same as bogo limits with a stackable flag.*/

  private $validTypes = ["Transaction"];
  private $possibleGroup1s = array();
  private $possibleGroup2s = array();

  public function __construct($id, $target) {
    $this->set_id($id);
    $this->target = $target;

    $this->possibleGroup1s = $this->checkInGroup($this->target->products, $this->group1);
    $this->possibleGroup2s = $this->checkInGroup($this->target->products, $this->group2);

    $this->possibleGroup1s = usort($this->possibleGroup1s, array($this, "sortOnPrices"));
    $this->possibleGroup2s = usort($this->possibleGroup2s, array($this, "sortOnPrices"));
  }

  public function apply() {
    if (count($this->group2) > 0) {
      // discount all in group2 that are less than group1 up to limit
    } else {
      // discount all in group1 up to limit

    }
  }

  public function validate() {
    // meet the floor of items in group 1
    // if there is  group 2, they have items in group two that are equal or lesser value

  }

}

?>
