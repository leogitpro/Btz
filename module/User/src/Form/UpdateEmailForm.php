<?php
/**
 * Update user email form
 *
 * User: leo
 */

namespace User\Form;


use User\Entity\User;
use User\Service\UserManager;
use User\Validator\EmailUniqueValidator;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

class UpdateEmailForm extends Form
{

    /**
     * @var UserManager
     */
    private $userManager;


    /**
     * @var User
     */
    private $user;


    /**
     * UpdateEmailForm constructor.
     *
     * @param UserManager $userManager
     * @param User $user
     */
    public function __construct(UserManager $userManager, User $user)
    {
        parent::__construct('update_email_form');

        $this->userManager = $userManager;
        $this->user = $user;

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

        $this->add([ // New E-mail address
            'type' => 'text',
            'name' => 'email',
            'attributes' => [
                'id' => 'email',
            ],
            'options' => [
                'label' => 'New E-mail address',
            ],
        ]);


        $this->add([ // Submit
            'type' => 'submit',
            'name' => 'submit',
            'attributes' => [
                'id' => 'submit',
                'value' => 'Update E-mail',
            ],
        ]);
    }


    public function addInputFilters()
    {
        $this->getInputFilter()->add([
            'name' => 'email',
            'required' => true,
            'break_on_failure' => true,
            'filters'  => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name' => 'EmailAddress',
                    'break_chain_on_failure' => true,
                    'options' => [
                        'allow' => \Zend\Validator\Hostname::ALLOW_DNS,
                        'useMxCheck' => false,
                    ],
                ],
                [
                    'name' => EmailUniqueValidator::class,
                    'break_chain_on_failure' => true,
                    'options' => [
                        'userManager' => $this->userManager,
                        'user' => $this->user,
                    ],
                ],
            ],
        ]);
    }

}