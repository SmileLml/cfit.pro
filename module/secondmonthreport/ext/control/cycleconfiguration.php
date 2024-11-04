<?php
include '../../control.php';
class mySecondmonthreport extends secondmonthreport
{
    public function cycleconfiguration()
    {
        $this->loadModel('custom');
        $field = 'examinecycleList';
        $lang = 'zh-cn';
        $module = 'secondmonthreport';
        if($_POST){
            $this->custom->deleteItems("lang=$lang&module=$module&section=$field");
            foreach($_POST['keys'] as $index => $key)
            {
                if(!empty($key)) $key = trim($key);

                if(!trim($_POST['values'][$index])){
                    $this->send(array('result' => 'fail', 'message' => '第'.($index+1).'行值不能为空'));
                }
                if(!empty($key)  and !validater::checkREG($key, '/^[a-zA-Z_0-9]+$/')) $this->send(array('result' => 'fail', 'message' => $this->lang->custom->notice->invalidStringKey));


            }
            $data = fixer::input('post')->get();

            foreach($data->keys as $index => $key)
            {
                //if(!$system and (!$value or !$key)) continue; //Fix bug #951.

                $value  = $data->values[$index];
                $system = $data->systems[$index];

                $this->custom->setItem("{$lang}.{$module}.{$field}.{$key}.{$system}", $value);

            }
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = inlink('cycleconfiguration', "");

            $this->send($response);
        }

        $dbFields = $this->custom->getItems("lang={$lang}&module={$module}&section={$field}");


        $this->view->dbFields    = $dbFields;
        $this->view->canAdd    = false;
        $this->view->field    = $field;
        $this->view->module    = $module;
        $this->view->fieldList    = [];


        //左侧选中标识，传当前方法名字
        $this->view->selected    = '';

        $this->view->topmenukey = 'configuration';
        $this->display();
    }
}
