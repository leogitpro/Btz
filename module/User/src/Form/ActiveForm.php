<?php
/**
 * Active account form
 *
 * User: leo
 */

namespace User\Form;


use Doctrine\ORM\EntityManager;
use User\Validator\ActiveCodeValidator;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

class ActiveForm extends Form
{

    private $entityManager;

    private $active_code = '';


    public function __construct(EntityManager $entityManager, $code = '')
    {
        parent::__construct('active_form');

        $this->setAttributes([
            'method' => 'post',
            'role' => 'form',
        ]);

        $this->entityManager = $entityManager;
        $this->active_code = $code;

        $this->setInputFilter(new InputFilter());

        $this->addElements();
        $this->addInputFilters();
    }


    /**
     * Form elements list
     *
     */
    private function addElements() {

        // CSRF Safe
        $this->add([
            'type' => 'csrf',
            'name' => 'csrf',
            'options' => [
                'csrf_options' => [
                    'timeout' => 600, // 10 minutes
                ],
            ],
        ]);


        // Text Code input
        $this->add([
            'type' => 'text', // Element type
            'name' => 'active_code', // Field name
            'attributes' => [ // Array of attributes
                'id' => 'active_code',
                'value' => $this->active_code,
            ],
            'options' => [ // Array of options
                'label' => 'Code', // Text Label
            ],
        ]);

        // Submit button input
        $this->add([
            'type' => 'submit',
            'name' => 'submit',
            'attributes' => [
                'value' => 'Active',
            ],
        ]);
    }


    /**
     * Add Filters and Validators
     */
    private function addInputFilters() {
        $this->getInputFilter()->add([
            'name' => 'active_code',
            'required' => true,
            'filters' => [
                [
                    'name' => 'StringTrim'
                ],
            ],
            'validators' => [
                [
                    'name' => ActiveCodeValidator::class,
                    'options' => [
                        'entityManager' => $this->entityManager,
                    ],
                ],
            ],
        ]);
    }

}