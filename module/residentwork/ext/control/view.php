<?php
include '../../control.php';
class myResidentwork extends residentwork
{
    public function view($workId=0){
        $this->app->loadLang("residentsupport");
        $depts = $this->loadModel('dept')->getOptionMenu();
        $depts[0] = '';
        $info = $this->residentwork->getByworkId($workId,'*',true);
        $this->view->info = $info;
        $this->view->depts = $depts;
        $this->view->actions = $this->loadModel('action')->getList('residentsupportdayno', $workId);
        $users = $this->loadModel('user')->getPairs('noletter');
        $outsideList1 = $this->loadModel('user')->getUsersNameByType('outsideExpertType');
        $outsideList2 = $this->loadModel('user')->getUsersNameByType('outside');
        $users = array_merge($users, $outsideList1, $outsideList2);
        $this->view->users = $users;
        $this->view->title = $this->lang->residentsupport->common;
        $this->display();
    }

}