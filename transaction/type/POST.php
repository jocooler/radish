<?php
class Post_Transaction extends Transaction {
  protected $transaction_query_string =
      "INSERT INTO transactions(
          userId,
          customerId,
          total,
          discounts,
          paymentType,
          transactionType
          ) VALUES (
          :userId,
          :customerId,
          :total,
          :discounts,
          :paymentType,
          :transactionType
        )";

protected $transactionId_query_string = "SELECT MAX(transactionId) from transactions";
protected $productsToTransactions_query_string =
    "INSERT INTO productsToTransactions (
      sku,
      transactionId,
      quantity,
      originalPrice,
      actualPrice,
      discounts
    ) VALUES (
      :sku,
      :transactionId,
      :quantity,
      :originalPrice,
      :actualPrice,
      :discounts
    )";
protected $quantity_update_query_string = "UPDATE products SET qoh = qoh + :quantity WHERE sku = :sku";

  public function execute() {
    // posting a new transaction has to:
    // 1. add the transaction to the transactions table
    // 2. add the products to the productsToTransactions table
    // 3. update product quantities

    // Parse the body.
    $this->set($this->body);

    $transaction_query = new Query($transaction_query_string, array(
      "userId" => $this->user->id,
      "customerId" => $this->customer->id,
      "total" => $this->total,
      "discounts" => serialize($this->discounts),
      "paymentType" => serialize($this->paymentType),
      "transactionType" => $this->type)
    );

    $transactionId_query = new Query($transactionId_query_string);
    $this->transactionId = $transactionId_query->execute;

    $productsToTransactions_query = new Query($productsToTransactions_query_string);
    $quantity_update_query = new Query($quantity_update_query);

    foreach ($this->products as $product) {
      $product['product']->applyDiscounts();

      $productsToTransactions_query->execute(array(
        "sku" => $product['product']->sku,
        "transactionId" => $this->transactionId,
        "quantity" => $product['quantity'],
        "originalPrice" => $product['product']->retail,
        "actualPrice" => $product['product']->price,
        "discounts" => serialize($product['product']->discounts)
      ));

      $quantity = $this->transactionTypeEffect * $product['quantity'];

      $quantity_update_query->execute(array("quantity"=>$quantity, "sku"=>$product['product']->sku));
    }
  }
}

$request = new Request(array(), array('signature', 'timestamp'), false); //TODO update this to use the new Request constructor.
$transaction = new Post_Transaction();
$response = new Response(205, array('message'=>'product posted ok.', 'transactionId'=>$transaction->transactionId));
$response->send();
?>
