<?php
class Put_Discount extends Discount {
  public function execute() {
    $this->query = new Query($this->put_query_string);
    $parameters = array(
      'id'    =>$this->id,
      'name'  =>$this->name,
      'group1'=>$this->group1,
      'group2'=>$this->group2,
      'discount'  =>$this->discount,
      'percentage'=>$this->percentage,
      'floor'     =>$this->floor,
      'ceiling'   =>$this->ceiling,
      'bogo'      =>$this->bogo,
      'stackable' =>$this->stackable,
      'max'       =>$this->max,
      'combinable'=>$this->combinable,
      'automatic' =>$this->automatic,
      'active'    =>$this->active
    );

    $this->query->execute($parameters);

  }
}

$request = new Request();
$targets = $request->targets;
$discount = new Put_Discount($request->body);
$discount->execute();

//TODO if the product didn't exist or didn't update for whatever reason.

$response = new Response(204, array());
$response->send();
?>
