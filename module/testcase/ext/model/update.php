<?php
/**
* Update a case.
*
* @param  int    $caseID
* @access public
* @return void
*/
public function update($caseID)
{
    $now     = helper::now();
    $oldCase = $this->getById($caseID);

    $result = $this->getStatus('update', $oldCase);
    if(!$result or !is_array($result)) return $result;

    $versionChanged = false;
    list($stepChanged, $status) = $result;

    if($stepChanged) $versionChanged = true; 
    if($oldCase->precondition != $this->post->precondition) $versionChanged = true; 

    $version = $versionChanged ? $oldCase->version + 1 : $oldCase->version;

    $currentVersion = $this->loadModel('story')->getVersion($this->post->story);

    $case = fixer::input('post')
        ->add('version', $version)
        ->setIF($this->post->story != false and $this->post->story != $oldCase->story, 'storyVersion', $currentVersion)
        ->setIF(!$this->post->linkCase, 'linkCase', '')
        ->setDefault('lastEditedBy', $this->app->user->account)
        ->add('lastEditedDate', $now)
        ->setDefault('story,branch', 0)
        ->join('stage', ',')
        ->join('linkCase', ',')
        ->join('categories', ',')
        ->setForce('status', $status)
        ->cleanInt('story,product,branch,module')
        ->remove('comment,steps,expects,files,labels,stepType')
        ->get();
    if(!isset($case->categories)) $case->categories = '';

    $requiredFields = $this->config->testcase->edit->requiredFields;
    if($oldCase->lib != 0)
    {
        /* Remove the require field named story when the case is a lib case.*/
        $requiredFieldsArr = explode(',', $requiredFields);
        $fieldIndex        = array_search('story', $requiredFieldsArr);
        array_splice($requiredFieldsArr, $fieldIndex, 1);
        $requiredFields    = implode(',', $requiredFieldsArr);
    }

    $this->dao->update(TABLE_CASE)->data($case)->autoCheck()->batchCheck($requiredFields, 'notempty')->where('id')->eq((int)$caseID)->exec();
    if(!$this->dao->isError())
    {
        $isLibCase = ($oldCase->lib and empty($oldCase->product));

        $titleChanged = ($case->title != $oldCase->title);
        if($isLibCase and $titleChanged) $this->dao->update(TABLE_CASE)->set('`title`')->eq($case->title)->where('`fromCaseID`')->eq($caseID)->exec();

        $introChanged = ($case->intro != $oldCase->intro);
        if($isLibCase and $introChanged) $this->dao->update(TABLE_CASE)->set('`intro`')->eq($case->intro)->where('`fromCaseID`')->eq($caseID)->exec();

        $this->updateCase2Project($oldCase, $case, $caseID);

        if($isLibCase)
        {
            if($versionChanged)
            {
                $fromcaseVersion  = $this->dao->select('fromCaseVersion')->from(TABLE_CASE)->where('fromCaseID')->eq($caseID)->fetch('fromCaseVersion');
                $fromcaseVersion += 1;
                $this->dao->update(TABLE_CASE)->set('`fromCaseVersion`')->eq($fromcaseVersion)->where('`fromCaseID`')->eq($caseID)->exec();
            }
        }
        else
        {
            $this->dao->update(TABLE_TESTRUN)
            ->set('precondition')->eq($oldCase->precondition)
            ->where('`case`')->eq($caseID)
            ->andWhere('precondition')->isNull()
            ->exec();
        }

        if($versionChanged)
        {
            $parentStepID = 0;

            /* Ignore steps when post has no steps. */
            if($this->post->steps)
            {
                foreach($this->post->steps as $stepID => $stepDesc)
                {
                    if(empty($stepDesc)) continue;
                    $stepType = $this->post->stepType;
                    $step = new stdclass();
                    $step->type    = ($stepType[$stepID] == 'item' and $parentStepID == 0) ? 'step' : $stepType[$stepID];
                    $step->parent  = ($step->type == 'item') ? $parentStepID : 0;
                    $step->case    = $caseID;
                    $step->version = $version;
                    $step->desc    = htmlspecialchars($stepDesc);
                    $step->expect  = $step->type == 'group' ? '' : htmlspecialchars($this->post->expects[$stepID]);
                    $this->dao->insert(TABLE_CASESTEP)->data($step)->autoCheck()->exec();
                    if($step->type == 'group') $parentStepID = $this->dao->lastInsertID();
                    if($step->type == 'step')  $parentStepID = 0;
                }
            }
            else
            {
                foreach($oldCase->steps as $step)
                {
                    unset($step->id);
                    $step->version = $version;
                    $this->dao->insert(TABLE_CASESTEP)->data($step)->autoCheck()->exec();
                }
            }
        }


        /* Join the steps to diff. */
        if($stepChanged and $this->post->steps)
        {
            $oldCase->steps = $this->joinStep($oldCase->steps);
            $case->steps    = $this->joinStep($this->getById($caseID, $version)->steps);
        }
        else
        {
            unset($oldCase->steps);
        }
        return common::createChanges($oldCase, $case);
    }
}
