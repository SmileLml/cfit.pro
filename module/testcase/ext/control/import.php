<?php
helper::import(dirname(dirname(dirname(__FILE__))) . "/control.php");
class mytestcase extends testcase
{
    public function import($applicationID, $productID, $branch = 0)
    {
        if($_FILES)
        {
            $file = $this->loadModel('file')->getUpload('file');
            $file = $file[0];
            if($file['extension'] != 'xlsx') die(js::alert($this->lang->file->onlySupportXLSX));

            $fileName = $this->file->savePath . $this->file->getSaveName($file['pathname']);
            move_uploaded_file($file['tmpname'], $fileName);

            $phpExcel  = $this->app->loadClass('phpexcel');
            $phpReader = new PHPExcel_Reader_Excel2007();
            if(!$phpReader->canRead($fileName))
            {
                $phpReader = new PHPExcel_Reader_Excel5();
                if(!$phpReader->canRead($fileName))die(js::alert($this->lang->excel->canNotRead));
            }
            $this->session->set('fileImport', $fileName);
            die(js::locate(inlink('showImport', "applicationID=$applicationID&productID=$productID&branch=$branch"), 'parent.parent'));
        }
        $this->display();
    }
}
