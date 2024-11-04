<?php
include '../../control.php';
class myCustom extends custom
{
    /**
     * Project: chengfangjinke
     * Method: restore
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:11
     * Desc: This is the code comment. This method is called restore.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $module
     * @param $field
     * @param string $confirm
     */
    public function restore($module, $field, $confirm = 'no')
    {   
        if($confirm == 'no') die(js::confirm($this->lang->custom->confirmRestore, inlink('restore', "module=$module&field=$field&confirm=yes")));

        if($module == 'user' and $field == 'contactField')
        {   
            $this->loadModel('setting')->deleteItems("module=$module&key=$field");
        }
        elseif($module == 'residentsupport' and $field == 'secondReviews') {
            $this->loadModel('setting')->deleteItems("module=$module&key=$field");
        }
        elseif($module == 'opinion' and $field == 'groupList')
        {   
            $this->custom->deleteItems("module=$module&section=$field");
            $this->custom->deleteItems("module=$module&section=ownerList");
        }   
        else
        {   
            $this->custom->deleteItems("module=$module&section=$field");
        }   
        die(js::reload('parent'));
    }
}
