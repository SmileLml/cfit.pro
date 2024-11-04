<?php
helper::import(dirname(dirname(dirname(__FILE__))) . "/control.php");
class myfile extends file
{
    /**
     * Print files.
     *
     * @param  array  $files
     * @param  string $fieldset
     * @param  object $object
     * @param $isAjaxDel
     * @access public
     * @return void
     */
    public function printfilesbycomponent($files, $fieldset = false, $canOperate = false, $object = null, $isAjaxDel = false)
    {
        $this->view->files      = $files;
        $this->view->fieldset   = $fieldset;
        $this->view->object     = $object;
        $this->view->canOperate = $canOperate;
        $this->view->isAjaxDel  = $isAjaxDel; //�Ƿ��첽ɾ��
        $this->display();
    }
}
