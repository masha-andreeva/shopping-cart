<?php

namespace ProductCatalogue\Form;

use Zend\Form\Form;

class ProductForm extends Form
{

    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('product');

        $this->setAttribute('class', 'add-product-form');

        $this->addElements();
    }

    /**
     *  Add form elements
     */
    public function addElements()
    {
        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'name',
            'type' => 'Text',
            'options' => array(
                'label' => 'Name',
            ),
            'attributes' => array(
                'class' => 'product-form-input',
            )
        ));
        $this->add(array(
            'name' => 'price',
            'type' => 'number',
            'options' => array(
                'label' => 'Price',
            ),
            'attributes' => array(
                'class' => 'product-form-input',
            )
        ));
        $this->add(array(
            'name' => 'description',
            'type' => 'Textarea',
            'options' => array(
                'label' => 'Decription',
            ),
            'attributes' => array(
                'class' => 'product-form-input',
            )
        ));
        $this->add(array(
            'name' => 'photo',
            'type' => 'Zend\Form\Element\File',
            'options' => array(
                'label' => 'Photo',
            ),
            'attributes' => array(
                'class' => 'form-control',
            )
        ));
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Go',
                'id' => 'submitbutton',
                'class' => 'btn',
            ),
        ));
    }

}
