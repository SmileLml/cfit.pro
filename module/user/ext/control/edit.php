<?php
include '../../control.php';
class myUser extends user
{
    /**
     * Edit a user.
     *
     * @param  string|int $userID   the int user id or account
     * @access public
     * @return void
     */
    public function edit($userID)
    {
        $this->lang->user->menu      = $this->lang->company->menu;
        $this->lang->user->menuOrder = $this->lang->company->menuOrder;
        if(!empty($_POST))
        {
            $this->user->update($userID);
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $link = $this->session->userList ? $this->session->userList : $this->createLink('company', 'browse');
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $link));
        }

        $user       = $this->user->getById($userID, 'id');
        $userGroups = $this->loadModel('group')->getByAccount($user->account);

        $title      = $this->lang->company->common . $this->lang->colon . $this->lang->user->edit;
        $position[] = $this->lang->user->edit;
        $this->view->title      = $title;
        $this->view->position   = $position;
        $this->view->user       = $user;
        $this->view->depts      = $this->dept->getOptionMenu();
        $this->view->userGroups = implode(',', array_keys($userGroups));
        $this->view->companies  = $this->loadModel('company')->getOutsideCompanies();
        $this->view->groups     = $this->dao->select('id, name')->from(TABLE_GROUP)->where('project')->eq('0')->fetchPairs('id', 'name');

        $this->view->rand = $this->user->updateSessionRandom();
        $this->display();
    }
}
