protected $put_query_string = array(
  "UPDATE products SET sku=:sku, upc=:upc, name=:name, manufacturer=:manufacturer, category=:category, wholesale=:wholesale, taxable=:taxable WHERE sku=:sku",

  "UPDATE burlingtonProducts SET sku=:sku, qoh=:qoh, retail=:retail, discount=:discount, discount_type=:discount_type WHERE sku=:sku"
);
