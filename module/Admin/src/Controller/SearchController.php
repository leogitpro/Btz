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
use Zend\View\Model\JsonModel;


class SearchController extends AdminBaseController
{

    /**
     * Department Search
     *
     * @return JsonModel
     */
    public function deptsAction()
    {
        $key = $this->params()->fromPost('query');

        $rows = $this->getDeptManager()->getDeptsBySearch($key);
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
        $memberManager = $this->getMemberManager();

        $key = $this->params()->fromPost('query');

        $member = $memberManager->getCurrentMember();

        $rows = $memberManager->getMembersBySearch($key);
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