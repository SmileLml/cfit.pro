<?php
class demo extends control
{
    public function index()
    {
        $users = $this->loadModel('user')->getList();
        $this->view->users = $users;
        $this->display();
    }
}
