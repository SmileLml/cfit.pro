<?php
    /**
     * Print cell data
     *
     * @param  object $col
     * @param  object $case
     * @param  array  $users
     * @param  array  $branches
     * @access public
     * @return void
     */
    public function printCell($col, $case, $users, $branches, $modulePairs = array(), $browseType = '', $mode = 'datatable', $projects = array(), $products = array())
    {
        /* Check the product is closed. */
        $canBeChanged = common::canBeChanged('case', $case);

        $canBatchRun                = common::hasPriv('testtask', 'batchRun');
        $canBatchEdit               = common::hasPriv('testcase', 'batchEdit');
        $canBatchDelete             = common::hasPriv('testcase', 'batchDelete');
        $canBatchCaseTypeChange     = common::hasPriv('testcase', 'batchCaseTypeChange');
        $canBatchConfirmStoryChange = common::hasPriv('testcase', 'batchConfirmStoryChange');
        $canBatchChangeModule       = common::hasPriv('testcase', 'batchChangeModule');

        $canBatchAction             = ($canBatchRun or $canBatchEdit or $canBatchDelete or $canBatchCaseTypeChange or $canBatchConfirmStoryChange or $canBatchChangeModule);

        $canView    = common::hasPriv('testcase', 'view');
        $caseLink   = helper::createLink('testcase', 'view', "caseID=$case->id&version=$case->version");
        $account    = $this->app->user->account;
        $fromCaseID = $case->fromCaseID;
        $id = $col->id;
        if($col->show)
        {
            $class = 'c-' . $id;
            $title = '';
            if($id == 'title')
            {
                $class .= ' text-left';
                $title  = "title='{$case->title}'";
            }
            if($id == 'status')
            {
                $class .= $case->status;
                $title  = "title='" . $this->processStatus('testcase', $case) . "'";
            }
            if($id == 'actions') $class .= ' c-actions text-right';
            if($id == 'lastRunResult') $class .= " {$case->lastRunResult}";
            if(strpos(',product,stage,precondition,keywords,story,', ",{$id},") !== false) $class .= ' text-ellipsis';

            echo "<td class='{$class}' {$title}>";
            if(isset($this->config->bizVersion)) $this->loadModel('flow')->printFlowCell('testcase', $case, $id);
            switch($id)
            {
            case 'id':
                if($canBatchAction)
                {
                    $disabled = $canBeChanged ? '' : 'disabled';
                    echo html::checkbox('caseIDList', array($case->id => ''), '', $disabled) . html::a(helper::createLink('testcase', 'view', "caseID=$case->id"), sprintf('%03d', $case->id), '', "data-app='{$this->app->openApp}'");
                }
                else
                {
                    printf('%03d', $case->id);
                }
                break;
            case 'pri':
                echo "<span class='label-pri label-pri-" . $case->pri . "' title='" . zget($this->lang->testcase->priList, $case->pri, $case->pri) . "'>";
                echo zget($this->lang->testcase->priList, $case->pri, $case->pri);
                echo "</span>";
                break;
            case 'title':
                if($case->branch) echo "<span class='label label-info label-outline'>{$branches[$case->branch]}</span> ";
                if($modulePairs and $case->module)
                {
                    $moduleName = zget($modulePairs, $case->module, '');
                    echo "<span class='label label-gray label-badge'>{$moduleName}</span> ";
                }
                echo $canView ? ($fromCaseID ? html::a($caseLink, $case->title, null, "style='color: $case->color' data-app='{$this->app->openApp}'") . html::a(helper::createLink('testcase', 'view', "caseID=$fromCaseID"), "[<i class='icon icon-share' title='{$this->lang->testcase->fromCase}'></i>#$fromCaseID]", '', "data-app='{$this->app->openApp}'") : html::a($caseLink, $case->title, null, "style='color: $case->color' data-app='{$this->app->openApp}'")) : "<span style='color: $case->color'>$case->title</span>";
                break;
            case 'product':
                $product = $case->product;

                if($this->app->openApp == 'project')
                {
                    echo zget($products, $case->applicationID . '-' . $case->product, '');
                }
                else
                {
                    if(!$product) $product = 'na';
                    echo zget($products, $product, '');
                }
                break;
            case 'project':
                echo zget($projects, $case->project, '');
                break;
            case 'branch':
                echo $branches[$case->branch];
                break;
            case 'type':
                echo $this->lang->testcase->typeList[$case->type];
                break;
            case 'stage':
                $stages = '';
                foreach(explode(',', trim($case->stage, ',')) as $stage) $stages .= $this->lang->testcase->stageList[$stage] . ',';
                $stages = trim($stages, ',');
                echo "<span title='$stages'>$stages</span>";
                break;
            case 'status':
                if($case->needconfirm)
                {
                    print("<span class='status-story status-changed' title='{$this->lang->story->changed}'>{$this->lang->story->changed}</span>");
                }
                elseif(isset($case->fromCaseVersion) and $case->fromCaseVersion > $case->version and !$case->needconfirm)
                {
                    print("<span class='status-story status-changed' title='{$this->lang->testcase->changed}'>{$this->lang->testcase->changed}</span>");
                }
                else
                {
                    print("<span class='status-testcase status-{$case->status}'>" . $this->processStatus('testcase', $case) . "</span>");
                }
                break;
            case 'story':
                static $stories = array();
                if(empty($stories)) $stories = $this->dao->select('id,title')->from(TABLE_STORY)->where('deleted')->eq('0')->andWhere('product')->eq($case->product)->fetchPairs('id', 'title');
                if($case->story and isset($stories[$case->story])) echo html::a(helper::createLink('story', 'view', "storyID=$case->story"), $stories[$case->story]);
                break;
            case 'precondition':
                echo $case->precondition;
                break;
            case 'keywords':
                echo $case->keywords;
                break;
            case 'version':
                echo $case->version;
                break;
            case 'openedBy':
                echo zget($users, $case->openedBy);
                break;
            case 'openedDate':
                echo substr($case->openedDate, 5, 11);
                break;
            case 'reviewedBy':
                echo zget($users, $case->reviewedBy);
                break;
            case 'reviewedDate':
                echo substr($case->reviewedDate, 5, 11);
                break;
            case 'lastEditedBy':
                echo zget($users, $case->lastEditedBy);
                break;
            case 'lastEditedDate':
                echo substr($case->lastEditedDate, 5, 11);
                break;
            case 'lastRunner':
                echo zget($users, $case->lastRunner);
                break;
            case 'lastRunDate':
                if(!helper::isZeroDate($case->lastRunDate)) echo date(DT_MONTHTIME1, strtotime($case->lastRunDate));
                break;
            case 'lastRunResult':
                $class = 'result-' . $case->lastRunResult;
                $lastRunResultText = $case->lastRunResult ? zget($this->lang->testcase->resultList, $case->lastRunResult, $case->lastRunResult) : $this->lang->testcase->unexecuted;
                echo "<span class='$class'>" . $lastRunResultText . "</span>";
                break;
            case 'bugs':
                echo (common::hasPriv('testcase', 'bugs') and $case->bugs) ? html::a(helper::createLink('testcase', 'bugs', "runID=0&caseID={$case->id}"), $case->bugs, '', "class='iframe'") : $case->bugs;
                break;
            case 'results':
                echo (common::hasPriv('testtask', 'results') and $case->results) ? html::a(helper::createLink('testtask', 'results', "runID=0&caseID={$case->id}"), $case->results, '', "class='iframe'") : $case->results;
                break;
            case 'stepNumber':
                echo $case->stepNumber;
                break;
            case 'actions':
                if($canBeChanged)
                {
                    if($case->needconfirm or $browseType == 'needconfirm')
                    {
                        common::printIcon('testcase', 'confirmstorychange',  "caseID=$case->id", $case, 'list', 'confirm', 'hiddenwin', '', '', '', $this->lang->confirm);
                        break;
                    }

                    if(common::hasPriv('testcase', 'confirmLibcaseChange') && isset($case->fromCaseVersion) and $case->fromCaseVersion > $case->version and !$case->needconfirm)
                    {
                        common::printIcon('testcase', 'confirmLibcaseChange', "caseID=$case->id&libcaseID=$case->fromCaseID&from=list", $case, 'list', 'search', 'hiddenwin');
                        break;
                    }

                    common::printIcon('testtask', 'results', "runID=0&caseID=$case->id", $case, 'list', '', '', 'iframe', true, "data-width='95%'");
                    common::printIcon('testtask', 'runCase', "runID=0&caseID=$case->id&version=$case->version", $case, 'list', 'play', '', 'runCase iframe', false, "data-width='95%'");
                    common::printIcon('testcase', 'edit',    "caseID=$case->id", $case, 'list');
                    if($this->config->testcase->needReview or !empty($this->config->testcase->forceReview)) common::printIcon('testcase', 'review',  "caseID=$case->id", $case, 'list', 'glasses', '', 'iframe');
                    common::printIcon('testcase', 'createBug', "applicationID=$case->applicationID&product=$case->product&branch=$case->branch&extra=caseID=$case->id,version=$case->version,runID=", $case, 'list', 'bug', '', 'iframe', '', "data-width='90%'");
                    common::printIcon('testcase', 'create',  "applicationID=$case->applicationID&productID=$case->product&branch=$case->branch&moduleID=$case->module&from=testcase&param=$case->id", $case, 'list', 'copy');
                }

                break;
            case 'categories':
                echo $this->getCategoriesValueByKeys($case->categories);
                break;
            }
            echo '</td>';
        }
    }


