<?php


namespace Admin\Form;


use Admin\Entity\Adminer;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

class UpdateProfileForm extends Form
{
    /**
     * @var Adminer
     */
    private $adminer;


    public function __construct(Adminer $adminer)
    {
        parent::__construct('update_profile_form');

        $this->adminer = $adminer;

        $this->setAttributes(['method' => 'post', 'role' => 'form']);
        $this->setInputFilter(new InputFilter());
        $this->addElements();
        $this->addInputFilters();
    }


    public function addElements()
    {
        $this->add([ // CSRF Safe
            'type' => 'csrf',
            'name' => 'csrf',
            'options' => [
                'csrf_options' => [
                    'timeout' => 600, // 10 minutes
                ],
            ],
        ]);

        $this->add([
            'type' => 'text',
            'name' => 'name',
            'attributes' => [
                'id' => 'name',
                'value' => $this->adminer->getAdminName(),
            ],
            'options' => [
                'label' => 'Full Name',
            ],
        ]);


        $this->add([ // Submit
            'type' => 'submit',
            'name' => 'submit',
            'attributes' => [
                'id' => 'submit',
                'value' => 'Update Profile',
            ],
        ]);
    }


    public function addInputFilters()
    {
        $this->getInputFilter()->add([
            'name' => 'name',
            'required' => true,
            'break_on_failure' => true,
            'filters'  => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
            ],
            'validators' => [
                [
                    'name' => 'StringLength',
                    'break_chain_on_failure' => true,
                    'options' => [
                        'min' => 2,
                        'max' => 15,
                    ],
                ],
            ],
        ]);
    }

}