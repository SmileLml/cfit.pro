<?php
/**
 * Batch update testcases.
 *
 * @access public
 * @return array
 */
public function batchUpdate()
{
    $cases      = array();
    $allChanges = array();
    $now        = helper::now();
    $data       = fixer::input('post')->get();
    $caseIDList = $this->post->caseIDList;

    /* Process data if the value is 'ditto'. */
    foreach($caseIDList as $caseID)
    {
        if($data->pris[$caseID]  == 'ditto') $data->pris[$caseID]  = isset($prev['pri'])  ? $prev['pri']  : 3;
        if($data->types[$caseID] == 'ditto') $data->types[$caseID] = isset($prev['type']) ? $prev['type'] : '';

        $prev['pri']    = $data->pris[$caseID];
        $prev['type']   = $data->types[$caseID];
    }

    /* Initialize cases from the post data.*/
    $extendFields = $this->getFlowExtendFields();
    foreach($caseIDList as $caseID)
    {
        $case = new stdclass();
        $case->lastEditedBy   = $this->app->user->account;
        $case->lastEditedDate = $now;
        $case->pri            = $data->pris[$caseID];
        $case->status         = $data->statuses[$caseID];
        $case->color          = $data->color[$caseID];
        $case->title          = $data->title[$caseID];
        $case->precondition   = $data->precondition[$caseID];
        $case->keywords       = $data->keywords[$caseID];
        $case->type           = $data->types[$caseID];
        $case->stage          = empty($data->stages[$caseID]) ? '' : implode(',', $data->stages[$caseID]);
        $case->categories     = empty($data->categories[$caseID]) ? '' : implode(',', array_filter($data->categories[$caseID]));

        /* 用例库批量编辑的时候，可以更换模块。*/
        if(isset($data->modules)) $case->module = $data->modules[$caseID];

        foreach($extendFields as $extendField)
        {
            $case->{$extendField->field} = htmlspecialchars($this->post->{$extendField->field}[$caseID]);
            $message = $this->checkFlowRule($extendField, $case->{$extendField->field});
            if($message) die(js::alert($message));
        }

        $cases[$caseID] = $case;
        unset($case);
    }

    /* Update cases. */
    foreach($cases as $caseID => $case)
    {
        $oldCase = $this->getByID($caseID);
        $case->project = $oldCase->project;
        $case->product = $oldCase->product;

        $this->dao->update(TABLE_CASE)->data($case)
            ->autoCheck()
            ->batchCheck($this->config->testcase->edit->requiredFields, 'notempty')
            ->where('id')->eq($caseID)
            ->exec();

        if(!dao::isError())
        {
            $isLibCase    = ($oldCase->lib and empty($oldCase->product));
            $titleChanged = ($case->title != $oldCase->title);
            if($isLibCase and $titleChanged) $this->dao->update(TABLE_CASE)->set('`title`')->eq($case->title)->where('`fromCaseID`')->eq($caseID)->exec();

            $this->updateCase2Project($oldCase, $case, $caseID);

            $this->executeHooks($caseID);

            unset($oldCase->steps);
            $allChanges[$caseID] = common::createChanges($oldCase, $case);
        }
        else
        {
            die(js::error('case#' . $caseID . dao::getError(true)));
        }
    }

    return $allChanges;
}
