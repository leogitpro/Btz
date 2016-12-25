<?php
/**
 * Forgot password form
 *
 * User: leo
 */

namespace User\Form;


use User\Service\UserManager;
use User\Validator\EmailExistedValidator;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

class ForgotPasswordForm extends Form
{

    /**
     * @var UserManager
     */
    private $userManager = null;


    /**
     * @var array
     */
    private $captchaConfig = [];


    /**
     * ForgotPasswordForm constructor.
     *
     * @param UserManager $userManager
     * @param array $captcha
     */
    public function __construct(UserManager $userManager, array $captcha = [])
    {
        parent::__construct('forgot_password_form');

        $this->setAttributes([
            'method' => 'post',
            'role' => 'form',
        ]);

        $this->userManager = $userManager;
        $this->captchaConfig = $captcha;

        $this->setInputFilter(new InputFilter());

        $this->addElements();
        $this->addInputFilters();
    }


    public function addElements()
    {
        // Field csrf
        $this->add([
            'type' => 'csrf',
            'name' => 'csrf',
            'options' => [
                'csrf_options' => [
                    'timeout' => 600, // 10 minutes
                ],
            ],
        ]);

        // Field email
        $this->add([
            'type' => 'text',
            'name' => 'email',
            'attributes' => [ // Array of attributes
                'id' => 'email',
            ],
            'options' => [ // Array of options
                'label' => 'Your E-mail', // Text Label
            ],
        ]);

        // Field captcha
        $this->add([
            'type' => 'captcha',
            'name' => 'captcha',
            'options' => [
                'label' => 'Verification code',
                'captcha' => $this->captchaConfig,
            ],
        ]);

        // Field submit
        $this->add([
            'type'  => 'submit',
            'name' => 'submit',
            'attributes' => [
                'value' => 'Forgot Password',
                'id' => 'submit',
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
                    'name' => EmailExistedValidator::class,
                    'break_chain_on_failure' => true,
                    'options' => [
                        'userManager' => $this->userManager,
                    ],
                ],
            ],
        ]);
    }

}