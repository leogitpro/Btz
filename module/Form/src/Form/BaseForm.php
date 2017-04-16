<?php
/**
 * BaseForm.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Form\Form;


use Form\Filter\Factory as FilterFactory;
use Form\Validator\Factory as ValidatorFactory;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;


class BaseForm extends Form
{

    /**
     * @var array
     */
    private $_elements;

    /**
     * @var array
     */
    private $_filters;




    public function addElement($element = [])
    {
        if (empty($element)) return;

        $this->_elements[] = $element;
    }

    public function addFilter($filter = [])
    {
        if (empty($filter)) return;

        $this->_filters[] = $filter;
    }


    public function addElements() {}


    /**
     * 表单安全
     */
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


    /**
     * 表单提交
     */
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


    public function __construct()
    {
        parent::__construct('form_' . rand(1111, 9999));

        $this->_elements = [];
        $this->_filters = [];

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


    /**
     * @param string $name
     * @param array $value_options
     */
    protected function addMultiCheckboxElement($name = 'checkbox', $value_options = [])
    {
        $this->addElement([
            'type' => 'multiCheckbox',
            'name' => $name,
            'options' => [
                'disable_inarray_validator' => true,
                'value_options' => $value_options,
            ],
        ]);


        $filter = [
            'name' => $name,
            'required' => false,
            'break_on_failure' => true,
            'filters' => [
                FilterFactory::StripTags(),
            ],
        ];
        $this->addFilter($filter);
    }


    /**
     * @param string $name
     */
    protected function addHiddenElement($name = 'hide', $validators = [])
    {
        $this->addElement([
            'type' => 'hidden',
            'name' => $name,
            'attributes' => [
                'id' => $name,
            ]
        ]);

        $filter = [
            'name' => $name,
            'filters' => [
                FilterFactory::StripTags(),
            ],
            'validators' => $validators,
        ];

        $this->addFilter($filter);
    }


    /**
     * @param string $name
     * @param array $options
     * @param bool $required
     * @param array $validators
     * @param array $filters
     */
    protected function addSelectElement($name = 'select', $options = [], $required = true, $validators = [], $filters = [])
    {
        $this->addElement([
            'type' => 'select',
            'name' => $name,
            'attributes' => [
                'id' => $name,
            ],
            'options' => [
                'value_options' => $options,
            ],
        ]);

        $filter = [
            'name' => $name,
            'break_on_failure' => true,
            'filters' => array_merge(
                $filters,
                [
                    FilterFactory::StripTags(),
                ]
            ),
        ];

        if ($required) {
            //$filter['required'] = true;
            $filter['validators'] = array_merge(
                [
                    ValidatorFactory::NotEmpty(),
                ],
                $validators
            );
        }

        $this->addFilter($filter);
    }


    /**
     * @param string $name
     * @param bool $required
     * @param array $validators
     * @param array $filters
     */
    protected function addDateElement($name = 'date', $required = true, $validators = [], $filters = [])
    {
        $this->addElement([
            'type' => 'date',
            'name' => $name,
            'attributes' => [
                'id' => $name,
            ],
        ]);

        $filter = [
            'name' => $name,
            'break_on_failure' => true,
            'filters' => array_merge(
                $filters,
                [
                    FilterFactory::StripTags(),
                ]
            ),
        ];

        if ($required) {
            //$filter['required'] = true;
            $filter['validators'] = array_merge(
                [
                    ValidatorFactory::Date()
                ],
                $validators
            );
        }

        $this->addFilter($filter);
    }


    /**
     * @param $name
     * @param bool $required
     * @param array $validators
     * @param array $filters
     */
    protected function addTextareaElement($name = 'content', $required = true, $validators = [], $filters = [])
    {
        $this->addElement([
            'type' => 'textarea',
            'name' => $name,
            'attributes' => [
                'id' => $name,
            ],
        ]);

        $filter = [
            'name' => $name,
            'break_on_failure' => true,
            'filters' => array_merge(
                $filters,
                [
                    FilterFactory::StringTrim(),
                    FilterFactory::StripTags(),
                ]
            ),
        ];

        if ($required) {
            //$filter['required'] = true;
            $filter['validators'] = array_merge(
                [
                    ValidatorFactory::NotEmpty(),
                ],
                $validators
            );
        }

        $this->addFilter($filter);
    }


    /**
     * 表单: 验证码
     *
     * @param string $name
     * @param array $config
     */
    protected function addCaptchaElement($name = 'captcha', $config = [])
    {
        $this->addElement([
            'type' => 'captcha',
            'name' => $name,
            'options' => [
                'label' => 'Verification',
                'captcha' => $config,
            ],
        ]);

        $filter = [
            'name' => $name,
            'break_on_failure' => true,
            'filters' => [
                FilterFactory::StringTrim(),
                FilterFactory::StripNewlines(),
                FilterFactory::StringToLower(),
            ],
            'validators' => [
                [
                    'name' => \Zend\Captcha\Image::class,
                ]
            ],
        ];
        $this->addFilter($filter);
    }



    /**
     * 表单: 密码输入框
     *
     * @param string $name
     * @param array $validators
     * @param array $filters
     */
    protected function addPasswordElement($name = 'password', $validators = [], $filters = [])
    {
        $this->addElement([
            'type' => 'password',
            'name' => $name,
            'attributes' => [
                'id' => $name,
            ],
        ]);

        $filter = [
            'name' => $name,
            'break_on_failure' => true,
            'filters' => array_merge(
                $filters,
                [
                    FilterFactory::StringTrim(),
                    FilterFactory::StripNewlines(),
                    FilterFactory::StringToLower(),
                ]
            ),
            'validators' => array_merge(
                [
                    ValidatorFactory::NotEmpty(),
                ],
                $validators
            ),
        ];
        $this->addFilter($filter);
    }


    /**
     * 表单: 文本输入框
     *
     * @param string $name
     * @param bool $required
     * @param array $validators
     * @param array $filters
     */
    protected function addTextElement($name = 'name', $required = true, $validators = [], $filters = [])
    {
        $this->addElement([
            'type' => 'text',
            'name' => $name,
            'attributes' => [
                'id' => $name,
            ],
        ]);

        $filter = [
            'name' => $name,
            'break_on_failure' => true,
            'filters' => array_merge(
                $filters,
                [
                    FilterFactory::StringTrim(),
                    FilterFactory::StripTags(),
                    FilterFactory::StripNewlines(),
                ]
            ),
        ];

        if ($required) {
            //$filter['required'] = true;
            $filter['validators'] = array_merge(
                [
                    ValidatorFactory::NotEmpty(),
                ],
                $validators
            );
        }

        $this->addFilter($filter);
    }


    /**
     * 表单: Email 输入框
     *
     * @param string $name
     * @param bool $required
     * @param array $validators
     * @param array $filters
     */
    protected function addEmailElement($name = 'email', $required = true, $validators = [], $filters = [])
    {
        $this->addElement([
            'type' => 'text',
            'name' => $name,
            'attributes' => [
                'id' => $name,
            ],
        ]);

        $filter = [
            'name' => $name,
            'break_on_failure' => true,
            'filters' => array_merge(
                $filters,
                [
                    FilterFactory::StringTrim()
                ]
            ),
        ];
        if ($required) {
            //$filter['required'] = true;
            $filter['validators'] = array_merge(
                [
                    ValidatorFactory::NotEmpty(),
                    ValidatorFactory::EmailAddress(),
                ],
                $validators
            );
        }

        $this->addFilter($filter);
    }

}