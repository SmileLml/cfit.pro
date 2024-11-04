<?php
include '../../control.php';
class mySecondmonthreport extends secondmonthreport
{

    /**
     * @param $phototype 快照类型  basic/formsnap
     * @param $starttime 开始时间
     * @param $endtime 结束时间
     * @param $deptID 部门id
     * @return void
     */
    public function realtimebasicexport($phototype,$starttime,$endtime,$deptID,$staticType,$isuseHisData){
        $this->loadModel('file');
        $this->loadModel('problem');
        if ($_POST) {
            /*$secondmonthreportLang   = $this->lang->secondmonthreport;
            $secondmonthreportConfig = $this->config->secondmonthreport;*/
            $fieldsArr = $this->secondmonthreport->getexportField($staticType,$phototype);
            /*$loadmodel = $this->lang->secondmonthreport->reportTomodules[$staticType];
            $this->loadModel($loadmodel);
            $destlang = $this->lang->$loadmodel;*/
            $destlang = $this->secondmonthreport->getColumnTopLang($staticType);
//            $this->loadModel('problem');
            // Create field lists.
            if($phototype == 'basic'){
                $usefields = $fields = explode(',', $fieldsArr['basic']);
                foreach ($fields as $key => $fieldName) {
//                $fieldName = trim($fieldName);

                    $fields[$fieldName] = $destlang->$fieldName;

                    unset($fields[$key]);
                }
                $resdata = $this->secondmonthreport->getsnapphotoData($starttime,$endtime,$deptID,$phototype,$usefields,$staticType,$isuseHisData);
            }else{
                $usefields = $fields = explode(',', $fieldsArr['form']);
                foreach ($fields as $key => $fieldName) {

                    $fields[$fieldName] = $destlang->$fieldName;

                    unset($fields[$key]);
                }
                $resdata = $this->secondmonthreport->getsnapphotoData($starttime,$endtime,$deptID,$phototype,$usefields,$staticType,$isuseHisData);

            }

            // Get $demandBrowseInfo.



            $this->post->set('fields', $fields);
            $this->post->set('rows', $resdata);
            $this->post->set('kind', $this->lang->secondmonthreport->demandBrowse);
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }


        $this->view->fileName        = $this->secondmonthreport->getExportFileName('realtime',$staticType,'',$starttime,$endtime);
        $this->view->allExportFields = [];
        $this->view->customExport    = false;

        $this->view->deptID = $deptID;


        $this->display();
    }
}
