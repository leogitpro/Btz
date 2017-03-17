<?php
/**
 * BaseForm.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Application\Form;


use Zend\Form\Form;
use Zend\InputFilter\InputFilter;


class BaseForm extends Form
{

    public function __construct()
    {
        parent::__construct('form_' . rand(1111, 9999));


        $this->setInputFilter(new InputFilter());

        $this->setAttributes(['method' => 'post', 'role' => 'form']);

        $this->addElements();
    }


    protected function addElements() {}


    protected function addCsrfElement()
    {
        $this->add([
            'type'  => 'csrf',
            'name' => 'csrf',
            'options' => [
                'csrf_options' => [
                    'timeout' => 600
                ]
            ],
        ]);
    }

    protected function addSubmitElement()
    {
        $this->add([
            'type' => 'submit',
            'name' => 'submit',
            'attributes' => [
                'id' => 'submit',
                'value' => 'Submit',
            ],
        ]);
    }

}