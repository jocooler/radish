<?php
class Discount extends Endpoint {
  // TODO come up with some iterative method to ensure that the best discount combination is applied.
  // TODO we're not sorting discounts any more. So ensure that that is correct elsewhere.
  public $id;                     // discount ID
  protected $group1 = array();    // an array of product skus
  protected $group1Exclusive = false;
  protected $group2 = array();    // an array of product skus
  protected $group2Exclusive = false;
  protected $discount = 0;        // a number representing the discount
  protected $percentage = false;  // boolean flag. True = percentage. False = fixed.
  protected $floor = 0;           // minimum number to achieve the discount
  protected $ceiling = 0;         // maximum that can be discounted
  protected $bogo = false;        // boolean flag are we doing a buy x get x?
  protected $combinable = false;  // boolean flag. True = can be combined. False = can't.
  protected $stackable = false;   // can this coupon be used several times if the quantity thresholds are met more than once.
  protected $max = 1;             // maximum times discount can be applied.
  protected $automatic = false;   // is the discount meant to be applied automatically or only with a coupon?
  protected $active = false;      // is the discount active?

  public $target;        // holds the target object.
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
    $child = new $type."_Discount"($id, $target, $query);
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
      $product->price -= $product->price * $amount/100;
    } else {
      $product->price -= $amount;
    }
    $product->price = max($product->price, 0); // ensure the price is above 0 for sanity.
    $product->discounts[] = $this->id;
  }

  protected function sortOnPrices(Product $a, Product $b) {
    return $a->price - $b->price;
  }

  // helper validations go in here.
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

  protected function checkCombinable($group) {
    if (!$this->combinable) {
      foreach ($group as $elementKey => $product) {
        if ($product->price !== $product->retail) {
          unset($group[$elementKey]);
        }
      }
    }
    return true;
  }

  protected function checkInGroup(array $products, array $group, $exclusive = false) {
    $foundProducts = array();
    foreach ($products as $product) {
      if (in_array($product->sku, $group) === $exclusive) { // true if both are true or both are false.
        array_push($foundProducts, $product);
      }
    }
    return $foundProducts;
  }

  public function set_id($id) {
    if (is_int($id)) {
      $this->id = $id;
    }
  }

  public function set_group1($group) {
    if (is_array($group)) {
      $valid = true;
      foreach ($group as $key=>$productId) {
        if (!is_int($productId)) {
          $valid = false;
        }
      }
      if ($valid) {
        $this->group = $group;
      }
    } else {
      $groupQuery = "SELECT productId, exclusive FROM productsToGroups WHERE productGroupId = :groupId";
      $query = new Query($groupQuery, array('groupId'=>$group));
      $this->set_group1($query->results['productId']);
      $this->group1Exclusive = $query->results['exclusive'];
    }
  }

  public function set_group2($group) {
    if (is_array($group)) {
      $valid = true;
      foreach ($group as $key=>$productId) {
        if (!is_int($productId)) {
          $valid = false;
        }
      }
      if ($valid) {
        $this->group = $group;
      }
    } else {
      $groupQuery = "SELECT productId, exclusive FROM productsToGroups WHERE productGroupId = :groupId";
      $query = new Query($groupQuery, array('groupId'=>$group));
      $this->set_group2($query->results['productId']);
      $this->group2Exclusive = $query->results['exclusive'];
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
  private $validTypes = ["Product", "Transaction"];
  private $products = array();

  public function __construct($id, $target, $query) {
    $this->set_id($id);
    $this->target = $target;
    $this->set($query->results);

    if (is_a($this->target, "Product")) { // it's a single product
      $this->products[] = $this->target;
    } else if (is_a($this->target, "Transaction") && $this->percentage && !$this->combinable) { // it's percentage discount to be applied to every non-discounted product.
      foreach ($this->target->products as $product) {
        $this->products[] = $product;
      }
    }

    $this->products = $this->checkInGroup($this->products, $this->group1, $this->group1Exclusive);
    $this->products = usort($this->products, array($this, "sortOnPrices"));
  }

  public function apply() {
    if (!$this->validate) {
      return false;
    }
    if (count($this->products)) { // discount all the products.
      foreach ($this->products as $product) {
        $this->discountProduct($product);
      }
    } else {
      if ($this->percentage) {
        $this->target->final -= $this->target->final * $this->discount/100;
      } else {
        $this->target->final -= $this->discount;
      }
      $this->target->final = max($this->target->final, 0); // ensure that the total doesn't go below 0, because that's nonsensical.
    }
  }

  public function validate() {
    if ($this->targetValidation() && $this->basicValidation() && $this->checkCombinable(&$this->products)) {
      return true;
    }
  }

}

Class Quantity_Discount extends Discount {
  private $validTypes = ["Transaction"];

  public function __construct($id, $target, $query) {
    $this->set_id($id);
    $this->target = $target;
    $this->set($query->results);

    $this->possibleGroup1s = $this->checkInGroup($this->target->products, $this->group1, $this->group1Exclusive);
    $this->possibleGroup1s = usort($this->possibleGroup1s, array($this, "sortOnPrices"));

  }

  public function apply() {
    if (!$this->validate) {
      return false;
    }
    $max = min($this->max, count($possibleGroup1s));
    for ($i=0; $i<$max; $i++) {
      $this->discountProduct($possibleGroup1s[$i]);
    }
  }

  public function validate() {
    if ($this->targetValidation() &&
        $this->basicValidation() &&
        $this->checkCombinable(&$this->possibleGroup1s) &&
        count($this->possibleGroup1s) > $this->floor) {
      return true;
    }
  }


}

Class Bogo_Discount extends Discount {
  /*
    BOGOs are identified by the $bogo flag.
    The items that must be purchased are held in $group1.
    The items that are discounted are held in $group2, or $group1, if $group2 is empty.
    The minimum number of full-price items is held in $floor.
    The number of discounted items is held in $ceiling.
    If there is a $stackable flag, the discount can be applied more than once if there are qualifying items.
    If there is a $max, it limits the number of times the discount can be applied (not the number of items). Requires $stackable.
  */

  private $validTypes = ["Transaction"];
  private $possibleGroup1s = array();
  private $possibleGroup2s = array();

  public function __construct($id, $target, $query) {
    $this->set_id($id);
    $this->target = $target;

    $this->set($query->results);

    $this->possibleGroup1s = $this->checkInGroup($this->target->products, $this->group1, $this->group1Exclusive);
    $this->possibleGroup1s = usort($this->possibleGroup1s, array($this, "sortOnPrices"));

    if (count($this->group2 > 0)) {
      $this->possibleGroup2s = $this->checkInGroup($this->target->products, $this->group2, $this->group2Exclusive);
      $this->possibleGroup2s = usort($this->possibleGroup2s, array($this, "sortOnPrices"));
    }
  }

  public function apply() {
    $nonDiscounted = array();
    $numberOfApplications = 1;
    if (count($this->group2) > 0) {// discount all in group2 that are less than group1 up to ceiling max number of times.
      $buy = &$this->possibleGroup1s; // use a reference here so that any changes are reflected anywere.
      $get = &$this->possibleGroup2s;
      if ($this->stackable) {
        $numberOfApplications = min(floor($buy/$this->floor), ceil($get/$this->ceiling, $this->max)); // the number of applications is the lesser of: the number of group1s/buy rounded down, OR the number of group2s/get rounded up, OR the max number of applications.
      }
    } else { // discount all in group1 up to ceiling
      $buy = &$this->possibleGroup1s; // here it's very important to use a reference
      $get = &$this->possibleGroup1s; // so that buy and get are the same internally.
      if ($this->stackable) {
        $numberOfApplications = floor($buy / ($this->floor + $this->ceiling)); // compute the number of complete applications we can do
        if ($buy % ($this->floor + $this->ceiling) > $this->floor) {  // see if we can meet the "buy" requirements of another discount
          $numberOfApplications += 1;
        }
        $numberOfApplications = min($this->max, $numberOfApplications); // check to be sure we're within the maximum number of applications
      }
    }

    for ($i=0; $i<$numberOfApplications && count($buy)>=$this->floor; $i++) {  // apply the discount the number of times, if the number of by items is ok
      for ($j=0; $j<$this->floor; $j++) { // find the "buy" items, put them in an array just for fun.
        // since this array is sorted, we know it will be the most expensive items on top.
        $product = array_splice($buy, 0, 1)[0]
        $nonDiscounted[] = $product;
        $max_price = $product->price;
      }
      for ($k=0; $k<$this->ceiling; $k++) { // discount the get items.
        $discounted_product = array_splice($get, 0, 1)[0];
        while ($discountedProduct->price > $max_price && count($get) > 0) { // If the price is more than the max price, go through the list until one is cheaper.
          $discounted_product = array_splice($get, 0, 1)[0];
        }
        if ($dicounted_product->price <= $max_price) { // ensure that the price is less than the max_price.
          $this->discountProduct($discounted_product);
        }
      }
      if (count($buy) < 1 || count($get) < 1) { // we ran out of products and can quit.
        break;
      }
    }
  }

  public function validate() {
    if ($this->targetValidation() &&
        $this->basicValidation() &&
        $this->checkCombinable(&$this->possibleGroup1s) &&
        $this->checkCombinable(&$this->possibleGroup2s)) {
      return true;
    }
  }

}

?>
