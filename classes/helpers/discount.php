<?php
class Discount {
  public $discountType;
  public $id;

  public function execute($price) {
    return $price;
  }

}
?>
