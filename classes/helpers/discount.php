<?php
class Discount {
  public $discountType;
  public $id;
  /* kinds of discounts:
  x = product
  y = product group
  z = discount

  discounts can be either fixed or a percentage

  Product Discounts
  simple discounts:
  x*(1-z) // 20% off each x
  x-z     // $3 off each x
  y*(1-z) // 20% off each product in group y
  y-z     // $3 off each product in group y

  quantity discounts:

  bogo discounts:
  buy x get x-z*x         // buy 2 get $3 off each additional one
  buy x get x*(1-z)       // buy 2 get 20% off each additional one
  buy x1 get x2-z*x2      // buy 2 of x1, get $3 off each x2
  buy x1 get x2*(1-z)     // buy 2 of x1, get 20% off each x2
  buy x get y-z*y         // buy 2 of x, get $3 off each product in y
  buy x get y*(1-z)       // buy 2 of x, get 20% off each product in y
  buy y get x-x*z         // buy 2 of any product in y, get $3 off each x
  buy y get x*(1-z)       // buy 2 of any product in y, get 20% off each x
  buy y1 get y2-y2*z      // buy 2 of any product in y1, get $3 off each product in y2
  buy y1 get y2*(1-z)     // buy 2 of any product in y1, get 20% off each product in y2

  bogo with limits

  bogo with stackable limits





  but x1 get x1 z

  buy x get y discountZ
  buy x get discountZ
  discountZ
  group
  buy x of group y get discountZ
  buy x and y get discountZ

  // discountZ
  */

  public function execute($price) {
    return $price;
  }

}
?>
