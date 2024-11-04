<?php
class excelBug extends bugModel
{
    public function setListValue($applicationID, $productID, $branch = 0, $projectID = 0)
    {
        if(empty($productID)) $productID = 'na';

        $products = $this->loadModel('rebirth')->getProductPairs($applicationID, true);
        $projects = $this->rebirth->getProductLinkProjectPairs($applicationID, $productID);
        $modules  = array();
        $stories  = array();
        $builds   = array();

        $application = $this->rebirth->getApplicationByID($applicationID);
        $this->post->set('applicationID', $application->name . "(#$application->id)");

        if($projectID)
        {
            $project = $this->loadModel('project')->getByID($projectID);
            unset($this->config->bug->export->listFields[1]);
            $this->post->set('project', $project->name . "(#$projectID)");

            $executionList = $this->loadModel('project')->getExecutionByAvailable($projectID);
            $executions    = array();
            foreach($executionList as $id => $execution) $executions[$id] = "$execution(#$id)";

            $this->config->bug->export->listFields[] = 'execution';
            $this->post->set('executionList', $executions);

            if($productID == 'na' or is_numeric($productID))
            {
                $this->post->set('product', zget($products, $productID) . "(#$productID)");
                $modules = $this->loadModel('tree')->getOptionMenu($productID, 'bug', 0, $branch);
                $stories = $this->loadModel('story')->getProductStories($productID, $branch);
                $builds  = $this->loadModel('build')->getProductBuildPairs($productID, $branch, 'noempty');

                foreach($modules as $id => $module) $modules[$id] = "$module(#$id)";
                foreach($stories as $id => $story)  $stories[$id] = "$story->title(#$story->id)";
                foreach($builds  as $id => $build)  $builds[$id]  = "$build(#$id)";
            }
            else
            {
                $this->config->bug->export->listFields[] = 'product';
                foreach($products  as $id => $product) $products[$id] = "$product(#$id)";
                $this->post->set('productList', $products);
            }
        }
        else
        {
            if($productID == 'na' or is_numeric($productID))
            {
                $this->post->set('product', zget($products, $productID) . "(#$productID)");
                $modules = $this->loadModel('tree')->getOptionMenu($productID, 'bug', 0, $branch);
                $stories = $this->loadModel('story')->getProductStories($productID, $branch);
                $builds  = $this->loadModel('build')->getProductBuildPairs($productID, $branch, 'noempty');

                foreach($modules as $id => $module) $modules[$id] = "$module(#$id)";
                foreach($stories as $id => $story)  $stories[$id] = "$story->title(#$story->id)";
                foreach($builds  as $id => $build)  $builds[$id]  = "$build(#$id)";
            }
            else
            {
                $this->config->bug->export->listFields[] = 'product';
                foreach($products  as $id => $product) $products[$id] = "$product(#$id)";
                $this->post->set('productList', $products);
            }

            if(empty($projects)) $projects[0] = '';
            foreach($projects as $id => $project) $projects[$id] = "$project(#$id)";
            $this->post->set('projectList', $projects);
        }

        $severityList = $this->lang->bug->severityList;
        $priList      = $this->lang->bug->priList;
        $typeList     = $this->lang->bug->typeList;
        $osList       = $this->lang->bug->osList;
        $browserList  = $this->lang->bug->browserList;

        unset($typeList['']);
        unset($typeList['designchange']);
        unset($typeList['newfeature']);
        unset($typeList['trackthings']);

        $this->post->set('moduleList',   $modules);
        $this->post->set('storyList',    $stories);
        $this->post->set('buildList',    $builds);
        $this->post->set('severityList', join(',', $severityList));
        $this->post->set('priList',      join(',', $priList));
        $this->post->set('osList',       join(',', $osList));
        $this->post->set('browserList',  join(',', $browserList));
        $this->post->set('listStyle',    $this->config->bug->export->listFields);
        $this->post->set('extraNum',     0);

        // 处理导出的Bug分类和Bug子类的联动。
        foreach($typeList as $key => $value)
        {
            $typeList[$key] = $value . "(#{$key})";
        }

        $childTypeList = $this->getChildTypeParentList();
        $childTypeData = array();
        foreach($childTypeList as $key => $values)
        {
            foreach($values as $index => $value)
            {
                $values[$index] = $value . "(#{$index})";
            }
            $childTypeData[$key] = $values;
        }

        $this->post->set('typeList', $typeList);
        $this->post->set('childTypeList', $childTypeData);

        $cascade = array();
        $cascade['childType'] = 'type';
        if(!empty($childTypeList)) $this->post->set('cascade', $cascade);
    }

    public function createFromImport($applicationID, $productID, $branch = 0)
    {
        $this->loadModel('action');
        $this->loadModel('story');
        $this->loadModel('file');
        $now    = helper::now();
        $branch = (int)$branch;
        $data   = fixer::input('post')->get();

        $this->app->loadClass('purifier', true);
        $purifierConfig = HTMLPurifier_Config::createDefault();
        $purifierConfig->set('Filter.YouTube', 1);
        $purifier = new HTMLPurifier($purifierConfig);

        if(!empty($_POST['id'])) $oldBugs = $this->dao->select('*')->from(TABLE_BUG)->where('id')->in($_POST['id'])->andWhere('product')->eq($productID)->fetchAll('id');

        /* 获取所属应用系统下包含哪些产品，报错时需要判断项目和产品是否存在关联关系。*/
        $productIdList = $this->loadModel('rebirth')->getAllProductIdList($applicationID, false);
        $productProejctGroup = $this->dao->select('t1.product,t1.project')->from(TABLE_PROJECTPRODUCT)->alias('t1')
            ->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.project = t2.id')
            ->where('t1.product')->in($productIdList)
            ->andWhere('t2.type')->eq('project')
            ->andWhere('t2.deleted')->eq('0')
            ->fetchGroup('product', 'project');

        $bugs = array();
        $line = 1;
        foreach($data->product as $key => $product)
        {
            $bugData = new stdclass();

            $bugData->product       = (int)$product;
            $bugData->branch        = 0;
            $bugData->applicationID = (int)$data->applicationID[$key];
            $bugData->module        = (int)$data->module[$key];
            $bugData->project       = $this->app->openApp == 'project' ? $this->session->project : (int)$data->project[$key];

            $bugData->execution     = 0;
            $bugData->openedBuild   = isset($data->openedBuild) && isset($data->openedBuild[$key]) ? join(',', $data->openedBuild[$key]) : '';
            $bugData->title         = $data->title[$key];
            $bugData->steps         = nl2br($purifier->purify($this->post->steps[$key]));
            $bugData->story         = isset($data->story) ? (int)$data->story[$key] : 0;
            $bugData->pri           = (int)$data->pri[$key];
            $bugData->deadline      = $data->deadline[$key];
            $bugData->type          = $data->type[$key];
            $bugData->childType     = $data->childTypes[$key];
            $bugData->severity      = (int)$data->severity[$key];
            $bugData->os            = $data->os[$key];
            $bugData->browser       = $data->browser[$key];
            $bugData->keywords      = $data->keywords[$key];

            if(!empty($bugData->openedBuild))
            {
                $builds = $this->dao->select('version')->from(TABLE_BUILD)
                    ->where('id')->in($bugData->openedBuild)
                    ->fetchAll('version');
                $number = array_keys($builds);
                $number = implode(',',$number);
                $bugData->linkPlan = $number;
            } else
            {
                $bugData->linkPlan = implode(',', $data->linkPlan[$key]);
            }

            if(isset($this->config->bug->create->requiredFields))
            {
                $requiredFields = explode(',', $this->config->bug->create->requiredFields);
                foreach($requiredFields as $requiredField)
                {
                    $requiredField = trim($requiredField);
                    if(empty($bugData->$requiredField)) dao::$errors[] = sprintf($this->lang->bug->noRequire, $line, $this->lang->bug->$requiredField);
                }
            }

            /* 判断关联的项目是否和产品关联了。*/
            if(!empty($bugData->product) and !empty($bugData->project) and !isset($productProejctGroup[$bugData->product][$bugData->project]))
            {
                dao::$errors[] = sprintf($this->lang->bug->noRelatedProduct, $line);
            }

            if(isset($this->config->bug->appendFields))
            {
                foreach(explode(',', $this->config->bug->appendFields) as $appendField)
                {
                    if(empty($appendField)) continue;
                    $bugData->$appendField = $_POST[$appendField][$key];
                }
            }

            $bugs[$key] = $bugData;
            $line++;
        }
        if(dao::isError()) die(js::error(dao::getError()));

        $storyVersionPairs = isset($data->story) ? $this->story->getVersions($data->story) : array();
        foreach($bugs as $key => $bugData)
        {
            $bugID = 0;
            if(!empty($_POST['id'][$key]) and empty($_POST['insert']))
            {
                $bugID = $data->id[$key];
                if(!isset($oldBugs[$bugID])) $bugID = 0;
            }

            if($bugID)
            {
                if($bugData->story != $oldBugs[$bugID]->story) $bugData->storyVersion = zget($storyVersionPairs, $bugData->story, 1);
                $bugData->steps = str_replace('src="' . common::getSysURL() . '/', 'src="', $bugData->steps);

                $oldBug = (array)$oldBugs[$bugID];
                $newBug = (array)$bugData;
                $oldBug['steps'] = trim($this->file->excludeHtml($oldBug['steps'], 'noImg'));
                $newBug['steps'] = trim($this->file->excludeHtml($newBug['steps'], 'noImg'));
                $changes = common::createChanges((object)$oldBug, (object)$newBug);
                if(empty($changes)) continue;

                $bugData->lastEditedBy   = $this->app->user->account;
                $bugData->lastEditedDate = $now;
                $this->dao->update(TABLE_BUG)->data($bugData)->where('id')->eq($bugID)->autoCheck()->exec();

                if(!dao::isError())
                {
                    $actionID = $this->action->create('bug', $bugID, 'Edited');
                    $this->action->logHistory($actionID, $changes);
                }
            }
            else
            {
                if($bugData->story) $bugData->storyVersion = zget($storyVersionPairs, $bugData->story, 1);
                $bugData->openedBy   = $this->app->user->account;
                $bugData->openedDate = $now;

                $this->dao->insert(TABLE_BUG)->data($bugData)->autoCheck()->exec();
                if(!dao::isError())
                {
                    $bugID = $this->dao->lastInsertID();
                    $this->action->create('bug', $bugID, 'Opened');
                }
            }
        }

        if($this->post->isEndPage)
        {
            unlink($this->session->fileImport);
            unset($_SESSION['fileImport']);
        }
    }
}
