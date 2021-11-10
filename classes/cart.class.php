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
	}

	public function remove_item($item,$count)
	{
		if(array_key_exists($item,$this->items)){
			unset($this->items[$item]);
		}
	}

	public function decrease_item($item,$count){
		if(array_key_exists($item,$this->items)){
			$this->items[$item] -= $count;
			if($this->items[$item] < 0 ) $this->items[$item] = 0;
		}
	}

	public static function setup_cart_if_not(){
		if(!isset($_SESSION['cart'])){
			$_SESSION['cart'] = new Cart();
		}
	}

}