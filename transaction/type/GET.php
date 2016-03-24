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

$get_transactionids_query =
    "SELECT
        t.transactionId,
    FROM
        transactions t,
        transactionTypes tt
    WHERE
        tt.id = t.transactionType AND
        t.date > :startDate AND
        t.date < :endDate AND
        tt.name = :transactionType";

// TODO validate dates of transactions.
$transactionIdsQuery = new Query($get_transactionids_query, array('startDate' => VALIDATE START DATE, 'endDate' => VALIDATE END DATE, 'transactionType' => VALIDATE TRANSACTION TYPE));
$transactionIds = $transactionIdsQuery->results;
//TODO all this might be able to be put into a helper and REQUIRE it in all endpoints
$targets = $request->targets;
$data = array();
$query = new Query()

foreach ($transactionIds['transactionId'] as $target) {
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
