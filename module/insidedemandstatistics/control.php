<?php
class insidedemandstatistics extends control
{
    public function opinion()
    {
        $this->app->loadLang('opinion');
        /** @var opinioninsideModel $opinionInsideModel */
        $opinionInsideModel = $this->loadModel('opinioninside');
        $opinions = $opinionInsideModel->getAllStatus();
        $list = [];
        foreach($this->lang->opinion->unionList as $unionId => $unionName)
        {
            if(empty($unionId)) continue;
            $list[$unionId] = new stdClass();
            $list[$unionId]->name     = $unionName;
            $list[$unionId]->wait      = 0;
            $list[$unionId]->delivery = 0;
            $list[$unionId]->online    = 0;

            foreach ($opinions as $opinion){

                $unionArr = explode(',', $opinion->union);

                //$opinion->union == $unionId ||
                if(in_array($unionId, $unionArr)){
                    if(in_array($opinion->status, ['delivery','online'])){
                        $list[$unionId]->{$opinion->status} ++;
                    } else if(in_array($opinion->status, ['created','waitupdate','pass','reject','subdivided'])){
                        $list[$unionId]->wait ++;
                    }else{
                        continue;
                    }

                }
            }
        }
        $this->view->list = $list;
        $this->view->selected = 1;
        $this->view->title  = $this->lang->insidedemandstatistics->common;
        $this->display();
    }
    public function opinion2()
    {
        $this->app->loadLang('opinion');
        /** @var opinioninsideModel $opinionInsideModel */
        $opinionInsideModel = $this->loadModel('opinioninside');
        $opinions = $opinionInsideModel->getAllStatus();
        $list = [];
        foreach ($opinions as $opinion){
            if(empty($opinion->category)) continue;


                if(!isset($list[$opinion->category])) $list[$opinion->category] = new stdClass();
                if(!isset($list[$opinion->category]->wait)) $list[$opinion->category]->wait = 0;
                if(!isset($list[$opinion->category]->online)) $list[$opinion->category]->online = 0;
                if(!isset($list[$opinion->category]->delivery)) $list[$opinion->category]->delivery = 0;

                if(in_array($opinion->status, ['delivery','online'])){
                    $list[$opinion->category]->{$opinion->status} ++;
                } else if(in_array($opinion->status, ['created','waitupdate','pass','reject','subdivided'])){
                    $list[$opinion->category]->wait ++;
                }else{
                    continue;
                }


        }
        $this->view->list = $list;
        $this->view->categoryList = $this->lang->opinion->categoryList;
        $this->view->selected = 2;
        $this->view->title  = $this->lang->insidedemandstatistics->common;
        $this->display();
    }
    public function requirement()
    {
        /** @var opinioninsideModel $opinionInsideModel */
        $opinionInsideModel = $this->loadModel('opinioninside');
        $opinions = $opinionInsideModel->getPairs();
        /** @var requirementinsideModel $requiremenInsideModel */
        $requiremenInsideModel = $this->loadModel('requirementinside');
        $requirements = $requiremenInsideModel->getAllStatus();
        $list = [];

        foreach ($requirements as $requirement){
            if(!isset($list[$requirement->opinion])) $list[$requirement->opinion] = new stdClass();
            if(!isset($list[$requirement->opinion]->wait)) $list[$requirement->opinion]->wait = 0;
            if(!isset($list[$requirement->opinion]->onlined)) $list[$requirement->opinion]->onlined = 0;
            if(!isset($list[$requirement->opinion]->delivered)) $list[$requirement->opinion]->delivered = 0;

            if(in_array($requirement->status, ['delivered','onlined'])){
                    $list[$requirement->opinion]->{$requirement->status} ++;
                } else {
                    $list[$requirement->opinion]->wait ++;
                }
        }

        $this->view->list = $list;
        $this->view->opinions = $opinions;
        $this->view->selected = 3;
        $this->view->title  = $this->lang->insidedemandstatistics->common;
        $this->display();
    }
    public function demand()
    {
        /** @var demandinsideModel $demandInsideModel */
        $demandInsideModel = $this->loadModel('demandinside');
        $demands = $demandInsideModel->getAllStatus();
        $list = [];
        foreach ($demands as $demand) {
            if(empty($demand) || empty($demand->acceptDept)) continue;

            if(in_array($demand->status, ['delivery','onlinesuccess','wait','feedbacked','build'])) {
                if (!isset($list[$demand->acceptDept])) $list[$demand->acceptDept] = new stdClass();
                if (!isset($list[$demand->acceptDept]->wait)) $list[$demand->acceptDept]->wait = 0;
                if (!isset($list[$demand->acceptDept]->onlinesuccess)) $list[$demand->acceptDept]->onlinesuccess = 0;
                if (!isset($list[$demand->acceptDept]->delivery)) $list[$demand->acceptDept]->delivery = 0;

                if(in_array($demand->status, ['delivery','onlinesuccess'])){
                    $list[$demand->acceptDept]->{$demand->status} ++;
                } else if(in_array($demand->status, ['wait','feedbacked','build'])){
                    $list[$demand->acceptDept]->wait ++;
                }
            }

        }
        $this->view->list = $list;
        $this->view->selected = 4;
        $this->view->depts   = $this->loadModel('dept')->getOptionMenu();
        $this->view->title  = $this->lang->insidedemandstatistics->common;
        $this->display();
    }
}