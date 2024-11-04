<?php
include '../../control.php';
class myCustom extends custom
{
    /**
     * Project: chengfangjinke
     * Method: doclib
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:11
     * Desc: This is the code comment. This method is called doclib.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     */
    public function doclib()
    {   
        $this->loadModel('doclib');
        if(strtolower($this->server->request_method) == "post")
        {   
            $data = fixer::input('post')->get();
            $this->loadModel('setting')->setItem('system.doclib.client', $data->client);
            $this->setting->setItem('system.doclib.account', $data->account);
            $this->setting->setItem('system.doclib.password', $data->password);

            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $this->createLink('custom', 'doclib')));
        }   

        $this->view->title      = $this->lang->custom->common . $this->lang->colon . $this->lang->doclib->common;
        $this->view->position[] = $this->lang->custom->common;
        $this->display();
    }
}
