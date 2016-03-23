<?php
class Get_Transaction extends Transaction {
  protected $get_query_string =
      "SELECT
          t.transactionId,
          tt.transactionType,
          u.name as clerk,
          c.name as customer,
          t.total,
          t.time,
          t.discount,
          p.paymentType as payment
      FROM
          transactions t,
          transactionTypes tt,
          users u,
          customers c,
          paymentTypes p
      WHERE
          tt.id = t.transactionType AND
          u.id = t.userId AND
          c.id = t.customerId AND
          p.id = t.paymentType AND
          t.transactionId = :transactionId";

  public function execute() {
    $this->query = new Query($this->get_query_string, array('transactionId' => $this->transactionId));
    if (isset($this->query->results[0])){
      $this->set($this->query->results[0]);
    } else {
      $this->transactionId = ''; // transaction didn't exist. Return a nulled result
    }
  }
}


$request = new Request(array('id'));
$targets = $request->targets;
$data = array();

foreach ($targets as $target) {
  $transaction = new Get_Transaction($target);
  $data[$transaction->transactionId] = $transaction->get('all');
}

if (isset($request->parameters['include'])) {
  $response = new Response($data, $request->parameters['include'], 'json');
} else {
  $response = new Response(200, $data);
}

$response->send();
?>
