<?php

namespace ProductCatalogue\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use ProductCatalogue\Model\Product;
use ProductCatalogue\Form\ProductForm;
use Zend\Validator\File\Size;
use Zend\Validator\File\Extension;
use Zend\File\Transfer\Adapter\Http;
use Zend\Session\Container;

class ProductController extends AbstractActionController
{
    protected $productTable;
    protected $shoppingCartTable;

    public function indexAction()
    {
        // Get Product Count
        // Check if usera is auth
        if (false) {
            $userId = 1;
            $productCount = $this->getShoppingCartTable()->getUserProductCountInCart($userId);
        } else {
            // Get From session
            $productCount = 0;
            // Get From session
            $productSession = new Container('products');
            $productsData = $productSession->products;

            // Get Product Count
            foreach ($productsData as $productRow) {
               $productCount +=  $productRow['count'];
            }
        }
        // Assign product count to layout
        $this->layout()->setVariable('productCount', $productCount);
        
        return new ViewModel(array(
            'products' => $this->getProductTable()->fetchAll()
        ));
    }

    public function addAction()
    {
        $form = new ProductForm();
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $product = new Product();
            $form->setInputFilter($product->getInputFilter());

            $postArr = $request->getPost()->toArray();
            $fileArr = $this->params()->fromFiles('photo');
            $formData = array_merge(
                    $postArr, //POST
                    array('file' => $fileArr['name']) //FILE...
            );
            $form->setData($formData);

            if ($form->isValid()) {
                $adapter = new Http();
                $size = new Size(array('min' => 1));
                $extension = new Extension(array('extension' => array('jpg', 'JPEG', 'png')));
                $adapter->setValidators(array($size, $extension), $fileArr['name']);

                if ($adapter->isValid()) {
                    $adapter->setDestination(getcwd() . '/public/photo');
                    if ($adapter->receive($fileArr['name'])) {
                        $filename = $adapter->getFileName();
                        $product->exchangeArray($form->getData());
                        $product->photo = $fileArr['name'];
                        $this->getProductTable()->saveProduct($product);
                    }
                } else {
                    $dataError = $adapter->getMessages();
                    $error = array();
                    foreach ($dataError as $key => $row) {
                        $error[] = $row;
                    }

                    $form->setMessages(array('photo' => $error));
                }
                // Redirect to list of products
                return $this->redirect()->toRoute('product');
            }
        }
        return array('form' => $form);
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('product', array(
                        'action' => 'add'
            ));
        }
        try
        {
            $product = $this->getProductTable()->getProduct($id);
        } catch (\Exception $ex)
        {
            return $this->redirect()->toRoute('product', array(
                        'action' => 'index'
            ));
        }

        $form = new ProductForm();
        $form->bind($product);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($product->getInputFilter());
            
            if ($request->getFiles()->photo['size'] != 0) {
                $postArr = $request->getPost()->toArray();
                $fileArr = $this->params()->fromFiles('photo');
                
                $formData = array_merge(
                        $postArr, //POST
                        array('file' => $fileArr['name']) //FILE...
                );
                
            } else {
                $formData = $request->getPost();
            }
            $form->setData($formData);

            // Get Old Photo
            $oldPhoto = $product->photo;
            if ($form->isValid()) {  
                if ($request->getFiles()->photo['size'] != 0) {
                    $adapter = new Http();
                    $size = new Size(array('min' => 1));
                    $extension = new Extension(array('extension' => array('jpg', 'JPEG', 'png')));
                    $adapter->setValidators(array($size, $extension), $fileArr['name']);
                    if ($adapter->isValid()) {
                        // Remove old
                        if (!is_null($product->photo)) {
                            unlink(getcwd() . '/public/photo/' . $product->photo);
                        }

                        $adapter->setDestination(getcwd() . '/public/photo');

                        if ($adapter->receive($fileArr['name'])) {
                            $product->photo = $fileArr['name'];
                            $this->getProductTable()->saveProduct($product);
                        }
                    } else {
                        $dataError = $adapter->getMessages();
                        $error = array();
                        foreach ($dataError as $key => $row) {
                            $error[] = $row;
                        }

                        $form->setMessages(array('photo' => $error));
                    }
                } else {
                    // Re-init Photo
                    $product->photo = $oldPhoto;
                    $this->getProductTable()->saveProduct($product);
                }

                // Redirect to list of products
                return $this->redirect()->toRoute('product');
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
        );
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('product');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                // Delete Photo
                $product = $this->getProductTable()->getProduct($id);
                unlink(getcwd() . '/public/photo/' . $product->photo);
                
                $this->getProductTable()->deleteProduct($id);
            }

            // Redirect to list of products
            return $this->redirect()->toRoute('product');
        }

        return array(
            'id' => $id,
            'product' => $this->getProductTable()->getProduct($id)
        );
    }

    public function getProductTable()
    {
        if (!$this->productTable) {
            $sm = $this->getServiceLocator();
            $this->productTable = $sm->get('ProductCatalogue\Model\ProductTable');
        }
        return $this->productTable;
    }
    
    public function getShoppingCartTable()
    {
        if (!$this->shoppingCartTable) {
            $sm = $this->getServiceLocator();
            $this->shoppingCartTable = $sm->get('ShoppingCart\Model\ShoppingCartTable');
        }
        return $this->shoppingCartTable;
    }

}
