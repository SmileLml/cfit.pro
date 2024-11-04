<?php
include '../../control.php';
class myCompany extends company
{
    public function edit()
    {
        if(!empty($_POST['systemMailName']))
        {
            $this->loadModel('setting')->setItem('system.common.global.systemMailName', $this->post->systemMailName);
            unset($_POST['systemMailName']);
        }

        parent::edit();
    }
}
