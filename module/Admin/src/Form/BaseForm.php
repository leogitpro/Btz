<?php
/**
 * BaseForm.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Admin\Form;


use Zend\Form\Form;
use Zend\InputFilter\InputFilter;


class BaseForm extends Form
{

    private $_elements;
    private $_filters;

    public function __construct()
    {
        parent::__construct('form_' . rand(1111, 9999));

        $this->_elements = [];
        $this->_filters = [];

        $this->setInputFilter(new InputFilter());
        $this->setAttributes(['method' => 'post', 'role' => 'form']);

        $this->addElements();
        $this->addCsrfElement();
        $this->addSubmitElement();
        foreach ($this->_elements as $element) {
            $this->add($element);
        }

        $inputFilter = new InputFilter();
        foreach ($this->_filters as $filter) {
            $inputFilter->add($filter);
        }
        $this->setInputFilter($inputFilter);
    }


    public function addElement($element)
    {
        $this->_elements[] = $element;
    }

    public function addFilter($filter)
    {
        $this->_filters[] = $filter;
    }


    public function addElements() {}


    private function addCsrfElement()
    {
        $this->addElement([
            'type'  => 'csrf',
            'name' => 'csrf',
            'options' => [
                'csrf_options' => [
                    'timeout' => 600
                ]
            ],
        ]);
    }


    private function addSubmitElement()
    {
        $this->addElement([
            'type' => 'submit',
            'name' => 'submit',
            'attributes' => [
                'id' => 'submit',
                'value' => 'Submit',
            ],
        ]);
    }


}