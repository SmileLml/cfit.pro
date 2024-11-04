<?php
class secondmonthdata extends control
{
    public function problem($browseType = 'all', $param = 0, $orderBy = 't1.id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1){
        $type = 'problem';
        $queryID   = ($browseType == 'bysearch') ? (int)$param : 0;
        $actionURL = $this->createLink('secondmonthdata', $type, "browseType=bysearch&queryID=myQueryID");

        $this->secondmonthdata->buildSearchForm($queryID, $actionURL);


        $this->app->loadClass('pager', $static = true);
        $pager = pager::init($recTotal, $recPerPage, $pageID);

        $this->view->pager         = $pager;
        $this->view->title         = $this->lang->secondmonthdata->common;
        $this->view->browseType    = $browseType;
        $this->view->orderBy       = $orderBy;
        $this->view->param         = $param;
        $this->view->dataList = $this->secondmonthdata->getProblemList($browseType, $param, $orderBy, $pager);
        $this->loadModel('application');
        $this->view->apps = $this->application->getPairs();
        $this->view->topmenukey = $type;
        $this->loadModel('problem');

        $this->view->depts      = $this->loadModel('dept')->getOptionMenu();
        $this->view->users      = $this->loadModel('user')->getPairs('noletter|noclosed');
        $this->view->type      = $type;
        $this->display();
    }

    public function demand($browseType = 'all', $param = 0, $orderBy = 't1.id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1){
        $type = 'demand';
        $queryID   = ($browseType == 'bysearch') ? (int)$param : 0;
        $actionURL = $this->createLink('secondmonthdata', $type, "browseType=bysearch&queryID=myQueryID");

        $this->secondmonthdata->buildSearchForm($queryID, $actionURL);


        $this->app->loadClass('pager', $static = true);
        $pager = pager::init($recTotal, $recPerPage, $pageID);

        $this->view->pager         = $pager;
        $this->view->title         = $this->lang->secondmonthdata->common;
        $this->view->browseType    = $browseType;
        $this->view->orderBy       = $orderBy;
        $this->view->param         = $param;
        $this->view->dataList = $this->secondmonthdata->getDemandList($browseType, $param, $orderBy, $pager);
        $this->loadModel('application');
        $this->view->apps = $this->application->getPairs();
        $this->view->topmenukey = $type;
        $this->loadModel('demand');
        $this->loadModel('requirement');

        $this->view->depts      = $this->loadModel('dept')->getOptionMenu();
        $this->view->users      = $this->loadModel('user')->getPairs('noletter|noclosed');
        $this->view->type      = $type;
        $this->display();
    }

    public function secondorder($browseType = 'all', $param = 0, $orderBy = 't1.id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1){
        $type = 'secondorder';
        $queryID   = ($browseType == 'bysearch') ? (int)$param : 0;
        $actionURL = $this->createLink('secondmonthdata', $type, "browseType=bysearch&queryID=myQueryID");

        $this->secondmonthdata->buildSearchForm($queryID, $actionURL);


        $this->app->loadClass('pager', $static = true);
        $pager = pager::init($recTotal, $recPerPage, $pageID);

        $this->view->pager         = $pager;
        $this->view->title         = $this->lang->secondmonthdata->common;
        $this->view->browseType    = $browseType;
        $this->view->orderBy       = $orderBy;
        $this->view->param         = $param;
        $this->view->dataList = $this->secondmonthdata->getSecondorderList($browseType, $param, $orderBy, $pager);
        $this->loadModel('application');
        $this->view->apps = $this->application->getPairs();
        $this->view->topmenukey = $type;
        $this->loadModel('secondorder');


        $this->view->depts      = $this->loadModel('dept')->getOptionMenu();
        $this->view->users      = $this->loadModel('user')->getPairs('noletter|noclosed');
        $this->view->type      = $type;
        $this->display();
    }
    public function importdata($type=""){
        if(!$type){
            echo "请刷新浏览器重新进入";
            return;
        }
        if($_FILES)
        {
            $file = $this->loadModel('file')->getUpload('file');
            $file = $file[0];
            if($file['extension'] != 'xlsx') die(js::alert($this->lang->file->onlySupportXLSX));

            $fileName = $this->file->savePath . $this->file->getSaveName($file['pathname']);
            move_uploaded_file($file['tmpname'], $fileName);
            $rows = $this->file->getRowsFromExcel($fileName);
            if($rows){
                foreach ($rows as $key=>$row){
                    if($key == 0){
                        continue;
                    }
                    $row[0] = trim($row[0]);
                    $row[1] = trim($row[1]);
                    if(is_numeric($row[0]) && is_numeric($row[1])){
                        $indata = [
                            'sourceyear'=>$row[0],
                            'objectid'=>$row[1],
                            'sourcetype'=>$type,
                        ];
                        $isexist = $this->dao->select('id')->from(TABLE_SECONDMONTHHISTORYDATA)->where("sourcetype")->eq($type)
                            ->andWhere("sourceyear")->eq($row[0])
                            ->andWhere("objectid")->eq($row[1])
                            ->andWhere("deleted")->eq(0)
                            ->fetch();
                        if($isexist){
                            continue;
                        }
                        $this->dao->insert(TABLE_SECONDMONTHHISTORYDATA)->data($indata)->exec();
                    }


                }
            }
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }
        $this->display();

    }

    public function create($type){

        if($_POST){
            $result = $this->secondmonthdata->create();
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }
        $this->view->type = $type;
        $this->display();
    }

    public function delete($ID, $confirm = 'no',$type)
    {
        if($confirm == 'no')
        {
            echo js::confirm($this->lang->secondmonthdata->confirmDelete, $this->createLink('secondmonthdata', 'delete', "id={$ID}&confirm=yes&type={$type}"), '');
            exit;
        }
        else
        {
            $this->secondmonthdata->delete(TABLE_SECONDMONTHHISTORYDATA, $ID);
            die(js::locate(inlink($type), 'parent'));
        }
    }

    public function exportTemplate()
    {
        if ($_POST) {
//            $this->projectplan->setListValue();

            foreach ($this->config->secondmonthdata->export->templateFields as $field) $fields[$field] = $this->lang->secondmonthdata->$field;


            $this->post->set('fields', $fields);
            $this->post->set('kind', 'secondmonthdata');
            $this->post->set('rows', array());
            $this->post->set('extraNum', $this->post->num);
            $this->post->set('fileName', $_POST['fileName']);
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }
        $this->display();
    }
}