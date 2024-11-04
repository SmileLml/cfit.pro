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
    public function printFilesByName($files, $fieldset, $fileName = "附件", $object = null, $canOperate = true, $isAjaxDel = false)
    {
        $this->view->files      = $files;
        $this->view->fieldset   = $fieldset;
        $this->view->object     = $object;
        $this->view->canOperate = $canOperate;
        $this->view->isAjaxDel  = $isAjaxDel; //�Ƿ��첽ɾ��
        $this->view->name  = $fileName;
        $this->display();
    }
}
