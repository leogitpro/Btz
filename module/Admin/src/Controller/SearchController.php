<?php
/**
 * SearchController.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Admin\Controller;


use Admin\Entity\Department;
use Admin\Entity\Member;
use Admin\Service\DepartmentManager;
use Admin\Service\MemberManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\JsonModel;

class SearchController extends AbstractActionController
{

    /**
     * @var MemberManager
     */
    private $memberManager;

    /**
     * @var DepartmentManager
     */
    private $deptManager;


    public function onDispatch(MvcEvent $e)
    {
        $sm = $e->getApplication()->getServiceManager();
        $this->memberManager = $sm->get(MemberManager::class);
        $this->deptManager = $sm->get(DepartmentManager::class);

        return parent::onDispatch($e);
    }


    /**
     * Department Search
     *
     * @return JsonModel
     */
    public function deptsAction()
    {
        $key = $this->params()->fromPost('query');

        $rows = $this->deptManager->getDeptsBySearch($key);
        $list = [];
        foreach ($rows as $row) {
            if ($row instanceof Department) {
                if (Department::DEFAULT_DEPT_ID == $row->getDeptId()) {
                    continue;
                }
                $list[] = [
                    'id' => $row->getDeptId(),
                    'label' => $row->getDeptName(),
                ];
            }
        }

        return new JsonModel($list);
    }


    /**
     * Member Search
     *
     * @return JsonModel
     */
    public function membersAction()
    {
        $key = $this->params()->fromPost('query');

        $member = $this->memberManager->getCurrentMember();

        $rows = $this->memberManager->getMembersBySearch($key);
        $list = [];
        foreach ($rows as $row) {
            if ($row instanceof Member) {
                if ($member->getMemberId() == $row->getMemberId() || $row->getMemberId() == Member::DEFAULT_MEMBER_ID) {
                    continue;
                }
                $list[] = [
                    'id' => $row->getMemberId(),
                    'label' => $row->getMemberName(),
                ];
            }
        }

        return new JsonModel($list);
    }

}