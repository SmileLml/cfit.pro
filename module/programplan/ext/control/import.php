<?php
include '../../control.php';
class myProgramPlan extends programplan
{
    /**
     * Browse program plans.
     *
     * @param  int     $projectID
     * @param  int     $productID
     * @param  string  $type
     * @param  string  $orderBy
     * @param  int     $baselineID
     * @access public
     * @return void
     */
    public function import($projectID)
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
                if(!$phpReader->canRead($fileName)) die(js::alert($this->lang->excel->canNotRead));
            }
            $this->session->set('fileImport', $fileName);

            die(js::locate(inlink('showImport', "projectID=$projectID"), 'parent.parent'));
        }

        $this->display();
    }
}
