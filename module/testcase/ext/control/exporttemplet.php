<?php
helper::import(dirname(dirname(dirname(__FILE__))) . "/control.php");
class mytestcase extends testcase
{
    public function exportTemplet($applicationID, $productID = 'all', $projectID = 0)
    {
        if($_POST)
        {
            $this->testcase->setListValue($applicationID, $productID, 0, $projectID);

            $fields = array();
            $fields['applicationID'] = $this->lang->testcase->applicationID;
            $fields['product']       = $this->lang->testcase->product;
            $fields['module']        = $this->lang->testcase->module;
            $fields['project']       = $this->lang->testcase->project;
            $fields['execution']     = $this->lang->testcase->execution;
            $fields['story']         = $this->lang->testcase->story;
            $fields['title']         = $this->lang->testcase->title;
            $fields['precondition']  = $this->lang->testcase->precondition;
            $fields['stepDesc']      = $this->lang->testcase->stepDesc;
            $fields['stepExpect']    = $this->lang->testcase->stepExpect;
            $fields['keywords']      = $this->lang->testcase->keywords;
            $fields['pri']           = $this->lang->testcase->pri;
            $fields['types']         = $this->lang->testcase->type;
            $fields['stage']         = $this->lang->testcase->stage;
            $fields['categories']    = $this->lang->testcase->categories;
            $fields['intro']         = $this->lang->testcase->intro;

            if(isset($this->config->bizVersion))
            {
                $appendFields = $this->dao->select('t2.*')->from(TABLE_WORKFLOWLAYOUT)->alias('t1')
                    ->leftJoin(TABLE_WORKFLOWFIELD)->alias('t2')->on('t1.field=t2.field && t1.module=t2.module')
                    ->where('t1.module')->eq('testcase')
                    ->andWhere('t1.action')->eq('exporttemplate')
                    ->andWhere('t2.buildin')->eq(0)
                    ->orderBy('order')
                    ->fetchAll();

                foreach($appendFields as $appendField) $fields[$appendField->field] = $appendField->name;
            }

            $width['applicationID'] = 30;
            $width['project']       = 30;
            $width['module']        = 20;
            $width['story']         = 20;
            $width['precondition']  = 30;

            $this->post->set('fields', $fields);
            $this->post->set('kind', 'testcase');
            $this->post->set('rows', array());
            $this->post->set('width', $width);
            $this->post->set('extraNum',   $this->post->num);
            $this->post->set('fileName', 'caseTemplate');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        $this->display();
    }
}
