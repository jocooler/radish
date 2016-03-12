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
    $this->sku = $sku;
  }

  public function set_upc($upc) {
    //TODO: validate upc
    $this->upc = $upc;
  }

  public function set_category($category) {
    //TODO validate categories
    $this->category = $category;
  }

  public function set_discount_type($discount_type) {
    //TODO validate discount types
    $this->discount_type = $discount_type;
  }

  public function set_discount($discount, $discount_type = FALSE) {
    if ($discount_type != FALSE) {
      $this->set_discount_type($discount_type);
    }
    //TODO validate discount
    $this->discount = $discount;
  }

  public function set_manufacturer($manufacturer) {
    //TODO validate manufacturer
    $this->manufacturer = $manufacturer;
  }

  public function set_name($name) {
    //TODO validate name
    $this->name = $name;
  }

  public function set_qoh($qoh) {
    //TODO validate QOH
    $this->qoh = $qoh;
  }

  public function set_retail($retail) {
    $retail = validate("retail", $retail, FILTER_VALIDATE_FLOAT);
    if ($retail) {
      $this->retail = $retail;
    }
    return $retail;
  }

  public function set_taxable($taxable) {
    $taxable = validate("taxable", $taxable, FILTER_VALIDATE_BOOLEAN);
    if ($taxable) {
      $this->taxable = $taxable;
    }
    return $taxable;
  }

  public function set_wholesale($wholesale) {
    $wholesale = validate("wholesale", $wholesale, FILTER_VALIDATE_FLOAT);
    if ($wholesale) {
      $this->wholesale = $wholesale;
    }
    return $wholesale;
  }
}

?>
