<?php
/**
 * Create a case.
 *
 * @param int $bugID
 * @access public
 * @return void
 */
public function create($bugID)
{
    $now    = helper::now();
    $status = $this->getStatus('create');
    $case   = fixer::input('post')
        ->add('status', $status)
        ->add('version', 1)
        ->add('fromBug', $bugID)
        ->setDefault('openedBy', $this->app->user->account)
        ->setDefault('openedDate', $now)
        ->setIF($this->post->story != false, 'storyVersion', $this->loadModel('story')->getVersion((int)$this->post->story))
        ->remove('steps,expects,files,labels,stepType,forceNotReview')
        ->setDefault('story', 0)
        ->cleanInt('story,product,branch,module')
        ->join('stage', ',')
        ->join('categories', ',')
        ->get();

    $param = '';
    if(!empty($case->lib))     $param = "lib={$case->lib}";
    if(!empty($case->product)) $param = "product={$case->product}";
    $result = $this->loadModel('common')->removeDuplicate('case', $case, $param);
    if($result['stop']) return array('status' => 'exists', 'id' => $result['duplicate']);

    /* Value of story may be showmore. */
    $case->story      = (int)$case->story;
    $this->dao->insert(TABLE_CASE)->data($case)->autoCheck()->batchCheck($this->config->testcase->create->requiredFields, 'notempty')->exec();
    if(!$this->dao->isError())
    {
        $caseID = $this->dao->lastInsertID();
        $this->loadModel('file')->saveUpload('testcase', $caseID);
        $parentStepID = 0;
        $this->loadModel('score')->create('testcase', 'create', $caseID);
        foreach($this->post->steps as $stepID => $stepDesc)
        {
            if(empty($stepDesc)) continue;
            $stepType      = $this->post->stepType;
            $step          = new stdClass();
            $step->type    = ($stepType[$stepID] == 'item' and $parentStepID == 0) ? 'step' : $stepType[$stepID];
            $step->parent  = ($step->type == 'item') ? $parentStepID : 0;
            $step->case    = $caseID;
            $step->version = 1;
            $step->desc    = htmlspecialchars($stepDesc);
            $step->expect  = $step->type == 'group' ? '' : htmlspecialchars($this->post->expects[$stepID]);
            $this->dao->insert(TABLE_CASESTEP)->data($step)->autoCheck()->exec();
            if($step->type == 'group') $parentStepID = $this->dao->lastInsertID();
            if($step->type == 'step')  $parentStepID = 0;
        }

        /* If the story is linked project, make the case link the project. */
        $this->syncCase2Project($case, $caseID);

        return array('status' => 'created', 'id' => $caseID);
    }
}
