<?php
/**
 * Mail support
 *
 * User: leo
 */

namespace Application\Service;


use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp;
use Zend\Mail\Transport\SmtpOptions;

class MailManager
{


    /**
     * @var array
     */
    private $config;


    /**
     * MailManager constructor.
     *
     * @param array $config
     */
    public function __construct($config = array())
    {
        $this->config = $config;
    }


    /**
     * Send a e-mail
     *
     * @param string $recipient
     * @param string $subject
     * @param string $content
     * @return bool
     */
    public function sendMail($recipient, $subject, $content)
    {

        $result = false;

        if (!isset($this->config['smtp'])) {
            return $result;
        }

        try {

            $sender = @$this->config['smtp']['connection_config']['username'];

            $smtp = new Smtp();
            $option = new SmtpOptions($this->config['smtp']);
            $smtp->setOptions($option);

            $message = new Message();
            $message->addTo($recipient);
            $message->setFrom($sender, ucfirst(substr($sender, 0, (stripos($sender, '@')))));
            $message->setSubject($subject);
            $message->setBody($content);

            $smtp->send($message);

            $result = true;

        } catch (\Exception $e) {
            //todo
        }

        return $result;
    }

}