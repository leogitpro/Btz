<?php
/**
 * WechatClientForm.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Admin\Form;


use Admin\Entity\WechatClient;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

class WechatClientForm extends Form
{

    private $client;

    public function __construct($client = null)
    {
        parent::__construct('wx_client_form');

        $this->setAttributes(['method' => 'post', 'role' => 'form']);

        $this->setInputFilter(new InputFilter());

        $this->client = $client;

        $this->addElements();
        $this->addFilters();
    }


    public function addElements()
    {
        $this->add([
            'type'  => 'csrf',
            'name' => 'csrf',
            'attributes' => [],
            'options' => [
                'csrf_options' => [
                    'timeout' => 600
                ]
            ],
        ]);

        $this->add([
            'type' => 'text',
            'name' => 'name',
            'attributes' => [
                'id' => 'name',
                'value' => ($this->client instanceof WechatClient) ? $this->client->getName() : '',
            ],
        ]);

        $this->add([
            'type' => 'text',
            'name' => 'domain',
            'attributes' => [
                'id' => 'domain',
                'value' => ($this->client instanceof WechatClient) ? $this->client->getName() : '',
            ],
        ]);

        $this->add([
            'type' => 'text',
            'name' => 'ips',
            'attributes' => [
                'id' => 'ips',
                'value' => ($this->client instanceof WechatClient) ? $this->client->getIps() : '',
            ],
        ]);

        $this->add([
            'type' => 'date',
            'name' => 'active',
            'attributes' => [
                'id' => 'active',
                'value' => ($this->client instanceof WechatClient) ? date('Y-m-d', $this->client->getActiveTime()) : '',
            ],
        ]);

        $this->add([
            'type' => 'date',
            'name' => 'expire',
            'attributes' => [
                'id' => 'expire',
                'value' => ($this->client instanceof WechatClient) ? date('Y-m-d', $this->client->getExpireTime()) : '',
            ],
        ]);

        $this->add([
            'type' => 'submit',
            'name' => 'submit',
            'attributes' => [
                'id' => 'submit',
                'value' => 'Submit',
            ],
        ]);
    }

    public function addFilters()
    {
        //todo
    }

}