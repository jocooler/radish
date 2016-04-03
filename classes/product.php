<?php
require_once('helpers/functions.php');
require_once('helpers/endpoint.php');
require_once('discount.php');

abstract class Product extends Endpoint {
  /* Endpoint specific variables */
  protected $category;
  protected $manufacturer;
  protected $name;
  protected $qoh;
  protected $retail;
  protected $price; // includes any discounts. Intially equal to retail.
  protected $sku;
  protected $taxable;
  protected $upc;
  protected $wholesale;
  protected $discounts = array();

  /* SQL Queries. These are here as templates. Please re-implement them in each endpoint. */
  protected $get_query_string = "SELECT * FROM products WHERE sku=:id";
  protected $post_query_string = "INSERT INTO products (`sku`, `upc`, `name`, `manufacturer`, `category`, `wholesale`, `taxable`, `qoh`, `retail`) VALUES (:sku, :upc, :name, :manufacturer, :category, :wholesale, :taxable, , :qoh, :retail)";
  protected $put_query_string = "UPDATE products SET sku=:sku, upc=:upc, name=:name, manufacturer=:manufacturer, category=:category, wholesale=:wholesale, taxable=:taxable, qoh=:qoh, retail=:retail WHERE sku=:sku";
  protected $delete_query_string = "DELETE FROM products WHERE sku=:sku";
  protected $options_query_string = array(''); // TODO

  protected $accessible_fields = array('category', 'manufacturer', 'name', 'price', 'qoh', 'retail', 'sku', 'taxable', 'upc', 'wholesale', 'discounts');

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
    $this->execute();
  }

  //abstract public function execute();
  public function apply_discounts() {
    // TODO apply discounts
  }

  /* Validation Methods */

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
  }

  public function set_category($category) {
    //TODO do we need to validate categories
    if ($category) {
      $this->category = $category;
    }
  }

  public function set_manufacturer($manufacturer) {
    //TODO do we need to validate manufacturer?
    $this->manufacturer = $manufacturer;
  }

  public function set_name($name) {
    //TODO Do we need to validate the name?
    $this->name = $name;
  }

  public function set_qoh($qoh) {
    $qoh = validate("qoh", $qoh, FILTER_VALIDATE_INT);
    if ($qoh) {
      $this->qoh = $qoh;
    }
  }

  public function set_retail($retail) {
    $retail = validate("retail", $retail, FILTER_VALIDATE_FLOAT);
    if ($retail) {
      $this->retail = $retail;
      $this->price = $retail;
    }
  }

  public function set_taxable($taxable) {
    $taxable = validate("taxable", $taxable, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    $this->taxable = $taxable;
  }

  public function set_wholesale($wholesale) {
    $wholesale = validate("wholesale", $wholesale, FILTER_VALIDATE_FLOAT);
    if ($wholesale) {
      $this->wholesale = $wholesale;
    }
  }

  public function set_discounts($discount) {
    if (is_array($discount)) {
      foreach ($discount as $d) {
        $this->set_discount($d);
      }
      return false; // don't do what's after this if we're an array.
    }
    if (!is_a($discount, "Discount")) {
      $discount = new Discount($discount);
    }
    $this->discounts[] = $discount;
  }

}

?>
