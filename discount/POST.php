<?php
class Post_Discount extends Discount {
  public function execute() {
    $this->query = new Query($this->post_query_string);
    $parameters = array(
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
    $this->id = $this->query->lastId();

  }
}

$request = new Request();
$discount = new Post_Discount($request->body);
$discount->execute();

//TODO if the product didn't exist or didn't update for whatever reason.

$response = new Response(205, array('id'=>$discount->id)); // TODO return ID for the new discount
$response->send();
?>
