<?php

class Cart{

	private $items = array();

	public function add_item($item,$count)
	{
		

		if(array_key_exists($item,$this->items)){
			$this->items[$item] += $count;
		}
		else{
			$this->items[$item] = $count;
		}

		if(Conferences::get_number_tickets_left($item) < $this->items[$item]){
			$this->items[$item] -= $count;
			if($this->items[$item] == 0 ) unset($this->items[$item]);
			return "Nedostatok vstupeniek";
		}

		return true;

	}

	public function remove_item($item)
	{
		if(array_key_exists($item,$this->items)){
			unset($this->items[$item]);
		}
	}

	public function decrease_item($item,$count){
		if(array_key_exists($item,$this->items)){
			$this->items[$item] -= $count;
			if($this->items[$item] <= 0 ) unset($this->items[$item]);
		}
	}
	public function increase_item($item,$count){
		if(array_key_exists($item,$this->items)){
			$this->items[$item] += $count;
			if($this->items[$item] <= 0 ) unset($this->items[$item]);
		}

		if(Conferences::get_number_tickets_left($item) < $this->items[$item]){
			$this->items[$item] -= $count;
			if($this->items[$item] ==0 ) unset($this->items[$item]);
			return "Nedostatok vstupeniek";
		}
		return true;
	}

	public static function setup_cart_if_not(){
		if(!isset($_SESSION['cart'])){
			$_SESSION['cart'] = new Cart();
		}
	}

	public function get_items(){
		return $this->items;
	}

	public function get_items_structured(){
		$print_items=array();
		foreach($this->get_items() as $key => $count){
	      $row = Conferences::get_conference_by_id($key);
	      if($row == false) continue;
	      $print_items[] = array(
	        "id" => $key,
	        "price" => $row['price'],
	        "count" => $count,
	        "name" => $row['name'],
	        "image" => $row['image_url'],
	      );
	    }
	     return $print_items;
		
	}

	public function get_cart_total(){
		$total =0;
		foreach($this->get_items() as $key => $count){
	      $row = Conferences::get_conference_by_id($key);
	      if($row == false) continue;
	      $total += $row['price'] * $count;
		}
		return $total;
	}

	public function remove_all_items(){
		$this->items = array();
	}
}