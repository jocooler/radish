<?php
require_once('helpers/functions.php');
require_once('helpers/endpoint.php');

abstract class Product extends Endpoint {
  protected $category;
  protected $discountType;
  protected $discount;
  protected $manufacturer;
  protected $name;
  protected $qoh;
  protected $retail;
  protected $sku;
  protected $taxable;
  protected $upc;
  protected $wholesale;

  /* These are here as templates. Please re-implement them in each endpoint. */
  protected $get_query_string = "SELECT * FROM products WHERE sku= :id";

  protected $post_query_string = "INSERT INTO products (`sku`, `upc`, `name`, `manufacturer`, `category`, `wholesale`, `taxable`, `qoh`, `retail`, `discount`, `discount_type`) VALUES (:sku, :upc, :name, :manufacturer, :category, :wholesale, :taxable, , :qoh, :retail, :discount, :discount_type)";

  protected $put_query_string = "UPDATE products SET sku=:sku, upc=:upc, name=:name, manufacturer=:manufacturer, category=:category, wholesale=:wholesale, taxable=:taxable, qoh=:qoh, retail=:retail, discount=:discount, discount_type=:discount_type WHERE sku=:sku";

  protected $delete_query_string = "DELETE FROM products WHERE sku=:sku";

  protected $options_query_string = array(''); // TODO

  protected $accessible_fields = array('category', 'discountType', 'discount', 'manufacturer', 'name', 'qoh', 'retail', 'sku', 'taxable', 'upc', 'wholesale');

  public function __construct ($identifier, $identifier_type) {
    switch ($identifier_type) {
      case 'sku':
        $this->set_sku($identifier);
        break;
      case 'upc':
        $this->set_upc($identifier);
        break;
      default:
        throw "Product Class requires a valid identifier and an identifier type of sku or upc to initialize.";
    }
    $this->fetch_details();
  }

  public function set_sku($sku) {
    //TODO: Validate sku
    // I think skus can be anything.
    $this->sku = $sku;
  }

  public function set_upc($upc) {
    // Maybe we need to make this a configuration option, what types of barcodes to allow.
    // For now, we'll decide only UPC, EAN13, and ISBN, which are 12 and 13 digits, respectively.
    if (preg_match('/^[\d]{12,13}$/', $upc)) {
      $this->upc = $upc;
    } else {
      $upc = '';
    }
    return $upc;
  }

  public function set_category($category) {
    //TODO do we need to validate categories
    if ($category) {
      $this->category = $category;
    }
    return $category;
  }

  public function set_discount_type($discount_type) {
    // TODO do we need to validate discount types?
    //$discount_type = validate("discount_type", $discount_type, FILTER_VALIDATE_FLOAT);
    if ($discount_type) {
      $this->discount_type = $discount_type;
    }
    return $discount_type;
  }

  public function set_discount($discount, $discount_type = FALSE) {
    if ($discount_type != FALSE) {
      $this->set_discount_type($discount_type);
    }

    $discount = validate("discount", $discount, FILTER_VALIDATE_FLOAT);
    if ($discount) {
      $this->discount = $discount;
    }
    return $discount;
  }

  public function set_manufacturer($manufacturer) {
    //TODO do we need to validate manufacturer?
    $this->manufacturer = $manufacturer;
    return $manufacturer;
  }

  public function set_name($name) {
    //TODO Do we need to validate the name?
    $this->name = $name;
    return $name;
  }

  public function set_qoh($qoh) {
    $qoh = validate("qoh", $qoh, FILTER_VALIDATE_INT);
    if ($qoh) {
      $this->qoh = $qoh;
    }
    return $qoh;
  }

  public function set_retail($retail) {
    $retail = validate("retail", $retail, FILTER_VALIDATE_FLOAT);
    if ($retail) {
      $this->retail = $retail;
    }
    return $retail;
  }

  public function set_taxable($taxable) {
    $taxable = validate("taxable", $taxable, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    $this->taxable = $taxable;
    return $taxable;
  }

  public function set_wholesale($wholesale) {
    $wholesale = validate("wholesale", $wholesale, FILTER_VALIDATE_FLOAT);
    if ($wholesale) {
      $this->wholesale = $wholesale;
    }
    return $wholesale;
  }

  public function set(array $values) {
    //takes a 2d array and sets the values in it.
    foreach ($values as $key=>$value) {
      $set_function = "set_" . $key;
      if (method_exists($this, $set_function)) {
         $this->$set_function($value);
      }
    }
  }
}

?>
