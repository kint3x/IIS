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
			$this->items[$item] -= $count;
			if($this->items[$item] < 0 ) $this->items[$item] = 0;
		}
	}

	

}