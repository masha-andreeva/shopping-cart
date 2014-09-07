<?php

namespace ShoppingCart\Model;

class ShoppingCart
{
    public $id;
    public $user_id;
    public $product_id;
    public $product_count;
    
    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->user_id = (!empty($data['user_id'])) ? $data['user_id'] : null;
        $this->product_id = (!empty($data['product_id'])) ? $data['product_id'] : null;
        $this->product_count = (!empty($data['product_count'])) ? $data['product_count'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
