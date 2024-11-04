<?php
helper::import(dirname(dirname(dirname(__FILE__))) . "/control.php");
class mybug extends bug
{
    public function exportTemplet($applicationID, $productID = 'all', $branch = 0, $projectID = 0)
    {
        if($_POST)
        {
            if(isset($this->config->bizVersion))
            {
                $appendFields = $this->dao->select('t2.*')->from(TABLE_WORKFLOWLAYOUT)->alias('t1')
                    ->leftJoin(TABLE_WORKFLOWFIELD)->alias('t2')->on('t1.field=t2.field && t1.module=t2.module')
                    ->where('t1.module')->eq('bug')
                    ->andWhere('t1.action')->eq('exporttemplate')
                    ->andWhere('t2.buildin')->eq(0)
                    ->orderBy('order')
                    ->fetchAll();

                foreach($appendFields as $appendField)
                {
                    $this->lang->bug->{$appendField->field} = $appendField->name;
                    $this->config->bug->export->templateFields[] = $appendField->field;
                }
            }
            $this->bug->setListValue($applicationID, $productID, $branch, $projectID);

            foreach($this->config->bug->export->templateFields as $field)
            {
                $fields[$field] = $this->lang->bug->$field;
            }

            $width['applicationID'] = 30;
            $width['project']       = 30;
            $this->post->set('width', $width);
            $this->post->set('fields', $fields);
            $this->post->set('kind', 'bug');
            $this->post->set('rows', array());
            $this->post->set('extraNum', $this->post->num);
            $this->post->set('fileName', 'bugTemplate');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        $this->display();
    }
}
