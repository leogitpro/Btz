<?php
/**
 * BaseController.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Application\Controller;


use Application\Service\ContactManager;
use Application\Service\MailManager;
use Zend\Mvc\Controller\AbstractActionController;


/**
 * Class BaseController
 *
 * @package Application\Controller
 *
 * @method \Application\Controller\Plugin\AsyncRequestPlugin getAsyncRequestPlugin()
 * @method \Application\Controller\Plugin\ConfigPlugin getConfigPlugin()
 * @method \Application\Controller\Plugin\DisplayPlugin getDisplayPlugin()
 * @method \Application\Controller\Plugin\LoggerPlugin getLoggerPlugin()
 * @method \Application\Controller\Plugin\ServerPlugin getServerPlugin()
 */
class AppBaseController extends AbstractActionController
{

    /**
     * @param string $sm
     * @return mixed
     */
    protected function getSm($sm)
    {
        return $this->getEvent()->getApplication()->getServiceManager()->get($sm);
    }

    /**
     * @param string $sm
     * @param array|null $options
     * @return mixed
     */
    protected function buildSm($sm, array $options = null)
    {
        return $this->getEvent()->getApplication()->getServiceManager()->build($sm, $options);

    }


    /**
     * @return MailManager
     */
    protected function getMailManager()
    {
        return $this->getSm(MailManager::class);
    }


    /**
     * @return ContactManager
     */
    protected function getContactManager()
    {
        return $this->getSm(ContactManager::class);
    }

}