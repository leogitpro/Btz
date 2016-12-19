<?php
/**
 * User sign up form
 *
 * User: leo
 */


namespace User\Form;



use Doctrine\ORM\EntityManager;
use User\Entity\User;

use User\Validator\EmailUniqueValidator;
use Zend\Filter\FilterChain;
use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;
use Zend\Form\Element\Password;
use Zend\Form\Element\Submit;
use Zend\Form\Element\Text;
use Zend\Form\Form;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Validator\EmailAddress;
use Zend\Validator\Identical;
use Zend\Validator\StringLength;
use Zend\Validator\ValidatorChain;


/**
 * Class SignUpForm
 *
 * @package User\Form
 */
class SignUpForm extends Form
{

    /**
     * @var EntityManager|null
     */
    private $entityManager = null;

    /**
     * @var null|User
     */
    private $user = null;


    /**
     * SignUpForm constructor.
     *
     * @param EntityManager $entityManager
     * @param User $user
     */
    public function __construct(EntityManager $entityManager, $user = null)
    {
        parent::__construct('signup_form');

        $this->setAttributes([
            'method' => 'post',
            'role' => 'form',
        ]);

        $this->entityManager = $entityManager;
        $this->user = $user;

        $this->setInputFilter(new InputFilter());

        $this->addElements();
    }


    /**
     * Add input email
     */
    public function addInputEmail()
    {
        $input = 'email'; // Input name

        $element = new Text($input);
        $element->setLabel('E-mail');
        $element->setAttributes(['id' => $input]);
        $this->add($element);

        $inputFilter = new Input($input); // The input filter for email
        $inputFilter->setBreakOnFailure(true); // Stop validate next input when failure.
        $inputFilter->setRequired(true); // Not null input

        $validatorChain = new ValidatorChain();

        $validatorEmail = new EmailAddress(); // Validator for e-mail address
        $validatorEmail->setOptions([
            'allow' => \Zend\Validator\Hostname::ALLOW_DNS,
            'useMxCheck' => false,
        ]);
        $validatorEmail->setMessage('请输入一个合法的电子邮箱地址.', EmailAddress::INVALID_FORMAT);
        $validatorChain->attach($validatorEmail, true); //出错返回, 不执行后续验证器, 权重 1(默认)

        $validatorEmailUnique = new EmailUniqueValidator(['entityManager' => $this->entityManager, 'user' => $this->user]);
        $validatorEmailUnique->setMessage('电子邮件已经被其他人使用了!', EmailUniqueValidator::USER_EXISTS);
        $validatorChain->attach($validatorEmailUnique);

        $inputFilter->setValidatorChain($validatorChain);

        $this->getInputFilter()->add($inputFilter);
    }


    /**
     * Add input name
     */
    public function addInputName()
    {
        $input = 'name';

        $element = new Text($input);
        $element->setLabel('Nickname');
        $element->setAttributes(['id' => $input]);
        $this->add($element);

        $inputFilter = new Input($input); // The input filter
        $inputFilter->setBreakOnFailure(true); // Stop validate next input when failure.
        $inputFilter->isRequired(true); // Not null input

        $filterChain = new FilterChain(); // Filter chain
        $filterChain->attach(new StringTrim()); // Filter: string trim
        $filterChain->attach(new StripTags()); // Filter: strip tag
        $inputFilter->setFilterChain($filterChain);

        $validatorChain = new ValidatorChain(); // Validator chain
        $validatorStringLen = new StringLength(); // Length validator
        $validatorStringLen->setMin(2); // Min 2 chars
        $validatorStringLen->setMax(15); // Max 15 chars
        $validatorStringLen->setMessages([
            StringLength::TOO_SHORT => '名字最短需要 %min% 个字.',
            StringLength::TOO_LONG => '名字最长不能超过 %max% 个字.',
        ]);
        $validatorChain->attach($validatorStringLen);
        $inputFilter->setValidatorChain($validatorChain);

        $this->getInputFilter()->add($inputFilter);
    }


    public function addInputPassword()
    {
        $input = 'passwd';
        $element = new Password($input);
        $element->setLabel('Password');
        $element->setAttribute('id', $input);
        $this->add($element);

        $inputFilter = new Input($input);
        $inputFilter->setBreakOnFailure(true);
        $inputFilter->isRequired(true);

        $validatorChain = new ValidatorChain();
        $validatorStringLen = new StringLength(['min' => '4', 'max' => 15]);
        $validatorChain->attach($validatorStringLen);

        $inputFilter->setValidatorChain($validatorChain);
        $this->getInputFilter()->add($inputFilter);


        $input1 = 'repasswd';
        $element1 = new Password($input1);
        $element1->setLabel('Confirm password');
        $element1->setAttribute('id', $input1);
        $this->add($element1);

        $inputFilter1 = new Input($input1);
        $inputFilter1->setBreakOnFailure(true);
        $inputFilter1->isRequired(true);

        $validatorChain1 = new ValidatorChain();

        $validatorIdentical = new Identical(); // confirm same as password
        //$validatorIdentical->setOptions(['token' => $input]);
        $validatorIdentical->setToken($input);
        $validatorChain1->attach($validatorIdentical);

        $inputFilter1->setValidatorChain($validatorChain1);
        $this->getInputFilter()->add($inputFilter1);

    }


    public function addInputSubmit()
    {
        $element = new Submit('submit');
        $element->setAttribute('value', 'Sign Up');
        $this->add($element);
    }



    public function addElements()
    {
        $this->addInputEmail();
        $this->addInputName();
        $this->addInputPassword();
        $this->addInputSubmit();
    }

}
