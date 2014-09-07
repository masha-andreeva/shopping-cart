<?php

namespace ShoppingCart\Model;

use Zend\Db\TableGateway\TableGateway;

class ShoppingCartTable
{
    protected $tableGateway;
    
    const TABLE_NAME = "shopping_cart";

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    /**
     * Get Shopping Cart By ID
     * 
     * @param int $id
     * 
     * @return ShoppingCart\Model\ShoppingCart
     * @throws \Exception
     */
    public function getShoppingCart($id)
    {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
 
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }
    
    /**
     * Get all user shopping cart
     * 
     * @param int $userId
     * @return array
     * @throws \Exception
     */
    public function getUserShoppingCart($userId)
    {
        $userId = (int) $userId;

        $rowset = $this->tableGateway->select(array('user_id' => $userId));
        $rows = $rowset->toArray();
        if (!$rows) {
            return false;
        }
        $products = array();
        foreach ($rows as $row) {
            $products[$row['product_id']] = $row;
        }
        return $products;
    }

    /**
     * Get Product To User Row
     * 
     * @param int $userId
     * @param int $productId
     * 
     * @return ShoppingCart\Model\ShoppingCart
     */
    public function getProduct2UserRow($userId, $productId)
    {
        $userId = (int) $userId;
        $productId = (int) $productId;
        $rowset = $this->tableGateway->select(array('user_id' => $userId, 'product_id' => $productId));
        $row = $rowset->current();

        return $row;
    }
    
    public function saveShoppingCart(ShoppingCart $shoppingCart)
    {
        $data = array(
            'user_id' => $shoppingCart->user_id,
            'product_id' => $shoppingCart->product_id,
            'product_count' => $shoppingCart->product_count
        );

        $id = (int) $shoppingCart->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            $this->tableGateway->update($data, array('id' => $id));
        }
    }
    
    /**
     * Get Product Counts in Cart
     * 
     * @param int $userId
     * 
     * @return int
     */
    public function getUserProductCountInCart($userId)
    {
        $userId = (int) $userId;
        $rowset = $this->tableGateway->select(array('user_id' => $userId));
        $rows = $rowset->toArray();
    
        if (!$rows) {
            return 0;
        }
        $count = 0;
        foreach ($rows as $row) {
            $count += $row['product_count'];
        }
        return $count;
    }

    /**
     * Get Shopping Cart
     * 
     * @param int $userId
     * 
     * @return array
     */
    public function getUserProducts($userId)
    {
        $userId = (int) $userId;
        $rowset = $this->tableGateway->select(array('user_id' => $userId));
        $rows = $rowset->toArray();

        return $rows;
    }
    
    /**
     * Delete from cart
     * 
     * @param int $id
     */
    public function deleteProductFromCart($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
    }
}
