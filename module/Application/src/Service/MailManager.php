<?php
/**
 * Mail support
 *
 * User: leo
 */

namespace Application\Service;


use Zend\Log\Logger;
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
     * @var Logger
     */
    private $logger;


    /**
     * MailManager constructor.
     *
     * @param array $config
     * @param Logger $logger
     */
    public function __construct($config = array(), Logger $logger)
    {
        $this->config = $config;
        $this->logger = $logger;
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
            $this->logger->err('No E-mail SMTP information configuration, Cann\'t send mail.');
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

            $this->logger->debug('Sent mail with' . PHP_EOL . 'to:' . $recipient . PHP_EOL . 'subject:' . $subject . PHP_EOL . 'content:' . $content);

            $result = true;

        } catch (\Exception $e) {
            $this->logger->err('Send mail failure: ' . $e->getMessage());
        }

        return $result;
    }

}