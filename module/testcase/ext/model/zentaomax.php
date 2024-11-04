<?php
/**
 * Create from import
 *
 * @param  int    $productID
 * @access public
 * @return void
 */
public function createFromImport($applicationID, $productID, $branch = 0)
{
    $this->loadModel('action');
    $this->loadModel('story');
    $this->loadModel('file');
    $now    = helper::now();
    $branch = (int)$branch;
    $data   = fixer::input('post')->get();

    if(!empty($_POST['id']))
    {
        $oldSteps = $this->dao->select('t2.*')->from(TABLE_CASE)->alias('t1')
            ->leftJoin(TABLE_CASESTEP)->alias('t2')->on('t1.id = t2.case')
            ->where('t1.id')->in(($_POST['id']))
            ->andWhere('t1.product')->eq($productID)
            ->andWhere('t1.version=t2.version')
            ->orderBy('t2.id')
            ->fetchGroup('case');
        $oldCases = $this->dao->select('*')->from(TABLE_CASE)->where('id')->in($_POST['id'])->fetchAll('id');
    }
    $storyVersionPairs = $this->story->getVersions($data->story);

    /* 获取所属应用系统下包含哪些产品，报错时需要判断项目和产品是否存在关联关系。*/
    $productIdList = $this->loadModel('rebirth')->getAllProductIdList($applicationID, false);
    $productProejctGroup = $this->dao->select('t1.product,t1.project')->from(TABLE_PROJECTPRODUCT)->alias('t1')
        ->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.project = t2.id')
        ->where('t1.product')->in($productIdList)
        ->andWhere('t2.type')->eq('project')
        ->andWhere('t2.deleted')->eq('0')
        ->fetchGroup('product', 'project');

    $cases = array();
    $line  = 1;
    foreach($data->product as $key => $product)
    {
        $caseData = new stdclass();

        $caseData->applicationID = (int)$data->applicationID[$key];
        $caseData->project      = (int)$data->project[$key];
        $caseData->execution    = 0;
        $caseData->product      = (int)$product;
        $caseData->branch       = 0;
        $caseData->module       = isset($data->module) ? (int)$data->module[$key] : 0;
        $caseData->story        = isset($data->story) ? (int)$data->story[$key] : 0;
        $caseData->title        = $data->title[$key];
        $caseData->pri          = (int)$data->pri[$key];
        $caseData->type         = $data->type[$key];
        $caseData->stage        = isset($data->stage[$key]) ? join(',', $data->stage[$key]) : '';
        $caseData->categories   = isset($data->categories[$key]) ? join(',', $data->categories[$key]) : '';
        $caseData->keywords     = $data->keywords[$key];
        $caseData->frequency    = 1;
        $caseData->precondition = $data->precondition[$key];
        $caseData->intro        = $data->intro[$key];

        if(isset($this->config->testcase->create->requiredFields))
        {
            $requiredFields = explode(',', $this->config->testcase->create->requiredFields);
            foreach($requiredFields as $requiredField)
            {
                $requiredField = trim($requiredField);
                if(!isset($caseData->$requiredField)) continue;
                if(empty($caseData->$requiredField)) dao::$errors[] = sprintf($this->lang->testcase->noRequire, $line, $this->lang->testcase->$requiredField);
            }
        }

        /* 判断关联的项目是否和产品关联了。*/
        if(!empty($caseData->product) and !empty($caseData->project) and !isset($productProejctGroup[$caseData->product][$caseData->project]))
        {
            dao::$errors[] = sprintf($this->lang->testcase->noRelatedProduct, $line);
        }

        $titleLength = $this->isValidTitleLength($caseData->title, 200);
        if(!$titleLength)
        {
            dao::$errors[] = sprintf($this->lang->testcase->exceedingLength, $line, $this->lang->testcase->title);
        }

        if(isset($this->config->testcase->appendFields))
        {
            foreach(explode(',', $this->config->testcase->appendFields) as $appendField)
            {
                if(empty($appendField)) continue;
                $caseData->$appendField = $_POST[$appendField][$key];
            }
        }

        $cases[$key] = $caseData;
        $line++;
    }
    if(dao::isError()) die(js::error(dao::getError()));

    $forceNotReview = $this->forceNotReview();
    foreach($cases as $key => $caseData)
    {
        $caseID = 0;
        if(!empty($_POST['id'][$key]) and empty($_POST['insert']))
        {
            $caseID = $data->id[$key];
            if(!isset($oldCases[$caseID])) $caseID = 0;
        }

        if($caseID)
        {
            $stepChanged = false;
            $steps       = array();
            $oldStep     = isset($oldSteps[$caseID]) ? $oldSteps[$caseID] : array();
            $oldCase     = $oldCases[$caseID];

            /* Remove the empty setps in post. */
            $steps = array();
            if(isset($_POST['desc'][$key]))
            {
                foreach($this->post->desc[$key] as $id => $desc)
                {
                    $desc = trim($desc);
                    if(empty($desc)) continue;
                    $step = new stdclass();
                    $step->type    = $data->stepType[$key][$id];
                    $step->desc    = htmlspecialchars($desc);
                    $step->expect  = htmlspecialchars(trim($this->post->expect[$key][$id]));

                    $steps[] = $step;
                }
            }

            /* If step count changed, case changed. */
            if((!$oldStep != !$steps) or (count($oldStep) != count($steps)))
            {
                $stepChanged = true;
            }
            else
            {
                /* Compare every step. */
                foreach($oldStep as $id => $oldStep)
                {
                    if(trim($oldStep->desc) != trim($steps[$id]->desc) or trim($oldStep->expect) != $steps[$id]->expect)
                    {
                        $stepChanged = true;
                        break;
                    }
                }
            }

            $version           = $stepChanged ? $oldCase->version + 1 : $oldCase->version;
            $caseData->version = $version;
            $changes           = common::createChanges($oldCase, $caseData);
            if($caseData->story != $oldCase->story) $caseData->storyVersion = zget($storyVersionPairs, $caseData->story, 1);
            if(!$changes and !$stepChanged) continue;

            if($changes or $stepChanged)
            {
                $caseData->lastEditedBy   = $this->app->user->account;
                $caseData->lastEditedDate = $now;
                if($stepChanged and !$forceNotReview) $caseData->status = 'wait';
                $this->dao->update(TABLE_CASE)->data($caseData)->where('id')->eq($caseID)->autoCheck()->exec();
                if($stepChanged)
                {
                    $parentStepID = 0;
                    foreach($steps as $id => $step)
                    {
                        $step = (array)$step;
                        if(empty($step['desc'])) continue;
                        $stepData = new stdclass();
                        $stepData->type    = ($step['type'] == 'item' and $parentStepID == 0) ? 'step' : $step['type'];
                        $stepData->parent  = ($stepData->type == 'item') ? $parentStepID : 0;
                        $stepData->case    = $caseID;
                        $stepData->version = $version;
                        $stepData->desc    = $step['desc'];
                        $stepData->expect  = $step['expect'];
                        $this->dao->insert(TABLE_CASESTEP)->data($stepData)->autoCheck()->exec();
                        if($stepData->type == 'group') $parentStepID = $this->dao->lastInsertID();
                        if($stepData->type == 'step')  $parentStepID = 0;
                    }
                }
                $oldCase->steps  = $this->joinStep($oldStep);
                $caseData->steps = $this->joinStep($steps);
                $changes  = common::createChanges($oldCase, $caseData);
                $actionID = $this->action->create('case', $caseID, 'Edited');
                $this->action->logHistory($actionID, $changes);

                $this->syncCase2Project($caseData, $caseID);
            }
        }
        else
        {
            $caseData->version    = 1;
            $caseData->openedBy   = $this->app->user->account;
            $caseData->openedDate = $now;
            $caseData->branch     = isset($data->branch[$key]) ? $data->branch[$key] : $branch;
            if($caseData->story) $caseData->storyVersion = zget($storyVersionPairs, $caseData->story, 1);
            $caseData->status = !$forceNotReview ? 'wait' : 'normal';
            $this->dao->insert(TABLE_CASE)->data($caseData)->autoCheck()->exec();

            if(!dao::isError())
            {
                $caseID       = $this->dao->lastInsertID();
                $parentStepID = 0;
                foreach($this->post->desc[$key] as $id => $desc)
                {
                    $desc = trim($desc);
                    if(empty($desc)) continue;
                    $stepData = new stdclass();
                    $stepData->type    = ($data->stepType[$key][$id] == 'item' and $parentStepID == 0) ? 'step' : $data->stepType[$key][$id];
                    $stepData->parent  = ($stepData->type == 'item') ? $parentStepID : 0;
                    $stepData->case    = $caseID;
                    $stepData->version = 1;
                    $stepData->desc    = htmlspecialchars($desc);
                    $stepData->expect  = htmlspecialchars($this->post->expect[$key][$id]);
                    $this->dao->insert(TABLE_CASESTEP)->data($stepData)->autoCheck()->exec();
                    if($stepData->type == 'group') $parentStepID = $this->dao->lastInsertID();
                    if($stepData->type == 'step')  $parentStepID = 0;
                }
                $this->action->create('case', $caseID, 'Opened'); 
                $this->syncCase2Project($caseData, $caseID);
            }
        }
    }

    if($this->post->isEndPage)
    {
        unlink($this->session->importFile);
        unset($_SESSION['importFile']);
    }
}

/**
 * Create action for import cases.
 *
 * @param  int    $objectType
 * @param  int    $objectID
 * @param  int    $actionType
 * @access public
 * @return void
 */
public function createAction($objectType, $objectID, $actionType)
{
    $action             = new stdclass();
    $action->objectType = strtolower($objectType);
    $action->objectID   = $objectID;
    $action->actor      = $this->app->user->account;
    $action->action     = $actionType;
    $action->date       = helper::now();

    /* Get product project and execution for this object. */
    $relation          = $this->loadModel('action')->getRelatedFields($action->objectType, $objectID);
    $action->product   = (int)$relation['product'];
    $action->project   = (int)$relation['project'];
    $action->execution = (int)$relation['execution'];

    $this->dao->insert(TABLE_ACTION)->data($action)->autoCheck()->exec();

    return $this->dao->lastInsertID();
}

public function isValidTitleLength($title, $maxLength = 255)
{
    $titleLength = mb_strlen($title, 'UTF-8');
    return $titleLength <= $maxLength;
}
