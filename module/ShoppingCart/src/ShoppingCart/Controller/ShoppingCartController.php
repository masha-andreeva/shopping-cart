<?php

namespace ShoppingCart\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use ShoppingCart\Model\ShoppingCart;
use Zend\Http\Response;
use Zend\Session\Container;

class ShoppingCartController extends AbstractActionController
{
    protected $shoppingCartTable;
    protected $productTable;

    public function addAction()
    {
        $request = $this->getRequest();
        // Collect Data
        $data = $request->getPost()->toArray(); 
        $productId = $data['productId'];
        $productCount = (int) $data['productCount'];
        // Check if user is auth
        $userId = 1;
        if (true) {
            // Store Products in session
            $productSession = new Container('products');
            $productsData = $productSession->products;
            
            if (isset($productsData)) {
                
                if (isset($productsData[$productId])) {
                    $productsData[$productId]['count'] += $productCount;
                } else {
                    $productsData[$productId] = array();
                    $productsData[$productId]['count'] = $productCount;
                }
            } else {
                $productsData = array();
                $productsData[$productId] = array();
                $productsData[$productId]['count'] = $productCount;
            }
             
            //Store
            $productSession->products = $productsData;
            
            // Get Product count
            $productCount = 0;
            foreach ($productsData as $productRow) {
               $productCount +=  $productRow['count'];
            }
        } else {
            $userShoppingCart = $this->getShoppingCartTable()->getUserShoppingCart($userId);
            if (isset($userShoppingCart[$productId])) {
                $productCount += $userShoppingCart[$productId]['product_count'];
            }
            // Update Shopping cart
            //Get Product
            $shoppingCart = $this->getShoppingCartTable()->getProduct2UserRow($userId, $productId);
            if (!$shoppingCart) {
                $shoppingCart = new ShoppingCart();
                $shoppingCart->user_id = $userId;
                $shoppingCart->product_id = $productId;
                $shoppingCart->product_count = $productCount;
            } else {
                $shoppingCart->product_count = $productCount;
            }
            $this->getShoppingCartTable()->saveShoppingCart($shoppingCart);
            
            // Get Product count in Cart
            $productCount = $this->getShoppingCartTable()->getUserProductCountInCart($userId);
        }
        $response = new Response();
        $response->setContent($productCount);
        return $response;
    }

    public function indexAction()
    {
        // Get Product Count
        // Check if usera is auth
        if (false) {
            $userId = 1;
            $productCount = $this->getShoppingCartTable()->getUserProductCountInCart($userId);
            // Get Shopping cart
            $shoppingCart = $this->getShoppingCartTable()->getUserProducts($userId);
            
            $products = array();
            foreach ($shoppingCart as $shoppingCartRow) {
                $data = array();
                $data['id'] = $shoppingCartRow['id'];
                $data['product_count'] = $shoppingCartRow['product_count'];
                // Cannot understand how i can fetch all data from Product table, so do it init Product obj
                $product = $this->getProductTable()->getProduct($shoppingCartRow['product_id']);
                $data['name'] = $product->name;
                $data['price'] = $product->price;
                $data['photo'] = $product->photo;
                $products[] = $data;
            }
        } else {
            // Init
            $productCount = 0;
            $products = array();
            // Get From session
            $productSession = new Container('products');
            $productsData = $productSession->products;
            foreach ($productsData as $productId => $productRow) {
                $data = array();
                $data['id'] = 'guest_' . $productId;
                $data['product_count'] = $productRow['count'];
                $product = $this->getProductTable()->getProduct($productId);
                $data['name'] = $product->name;
                $data['price'] = $product->price;
                $data['photo'] = $product->photo;
                $products[] = $data;
            }
            
            // Get Product Count
            foreach ($productsData as $productRow) {
               $productCount +=  $productRow['count'];
            }
        }

        // Assign product count to layout
        $this->layout()->setVariable('productCount', $productCount);
  
        return new ViewModel(array(
            'products' => $products
        ));
    }
    
    public function deleteAction()
    {
        $id = $this->params()->fromRoute('id', 0);

        if (!$id) {
            return $this->redirect()->toRoute('shoppingCart/view');
        }

        // Check if quest
        if (preg_match('/guest/', $id)) {
            $productData = explode("_", $id);
            $productId = $productData[1];
            // Get From session
            $productSession = new Container('products');
            $productsData = $productSession->products;
            // Remove from session
            unset($productsData[$productId]);
            //Store
            $productSession->products = $productsData;
        } else {
            $this->getShoppingCartTable()->deleteProductFromCart($id);
        }

        // Redirect to cart
        return $this->redirect()->toRoute('shoppingCart');
    }
    
    public function updateAction()
    {
        $request = $this->getRequest();
        // Collect Data
        $data = $request->getPost()->toArray(); 
        $itemId = $data['itemId'];
        $productCount = (int) $data['productCount'];
        // Check if quest
        if (preg_match('/guest/', $itemId)) {
            // Get From session
            $productData = explode("_", $itemId);
            $productId = $productData[1];
            $productSession = new Container('products');
            $productsData = $productSession->products;
            
            if (isset($productsData[$productId])) {
                $productsData[$productId]['count'] = $productCount;
            }
            //Store
            $productSession->products = $productsData;
        } else {
            $shoppingCart = $this->getShoppingCartTable()->getShoppingCart($itemId);
            // Update Shopping cart
            $shoppingCart->product_count = $productCount;

            $this->getShoppingCartTable()->saveShoppingCart($shoppingCart);
        } 

        $response = new Response();
        $response->setContent($productCount);
        return $response;
    }
    
    public function getShoppingCartTable()
    {
        if (!$this->shoppingCartTable) {
            $sm = $this->getServiceLocator();
            $this->shoppingCartTable = $sm->get('ShoppingCart\Model\ShoppingCartTable');
        }
        return $this->shoppingCartTable;
    }
    
    public function getProductTable()
    {
        if (!$this->productTable) {
            $sm = $this->getServiceLocator();
            $this->productTable = $sm->get('ProductCatalogue\Model\ProductTable');
        }
        return $this->productTable;
    }
}
