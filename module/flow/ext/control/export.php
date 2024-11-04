<?php
class flow extends control
{
    /**
     * Export datas of a flow.
     *
     * @param  string $module
     * @param  string $mode
     * @access public
     * @return void
     */
    public function export($module, $mode = 'all')
    {
        if(!commonModel::hasPriv($module, 'export')) $this->loadModel('common')->deny($module, 'export');

        $flow = $this->loadModel('workflow')->getByModule($module);

        if($_POST)
        {
            $numberFields = array();
            $exportFields = $this->loadModel('workflowfield')->getExportFields($flow->module);
            $flowFields   = $this->workflowfield->getList($flow->module);
            foreach($flowFields as $field)
            {
                if(!isset($exportFields[$field->field])) continue;

                if(isset($this->config->workflowfield->typeList['number'][$field->type])) $numberFields[] = $field->field;

                if($field->options) $field->options = $this->workflowfield->getFieldOptions($field);

                if($field->control == 'richtext' or $field->control == 'textarea') $this->config->excel->editor[$module][] = $field->field;
            }

            $flowDatas      = array();
            $queryCondition = $this->session->{$module . 'QueryCondition'};
            if($mode == 'all')
            {
                if(strpos($queryCondition, 'LIMIT') !== false) $queryCondition = substr($queryCondition, 0, strpos($queryCondition, 'LIMIT'));
                $stmt = $this->dbh->query($queryCondition);
                while($row = $stmt->fetch()) $flowDatas[$row->id] = $row;
            }
            if($mode == 'thisPage')
            {
                $stmt = $this->dbh->query($queryCondition);
                while($row = $stmt->fetch()) $flowDatas[$row->id] = $row;
            }

            foreach($flowDatas as $data)
            {
                foreach($data as $key => $value)
                {
                    foreach($flowFields as $field)
                    {
                        if($field->field == $key)
                        {
                            if(!is_array($field->options)) break;
                            if(($field->control == 'multi-select' or $field->control == 'checkbox') and $value and is_string($value))
                            {
                                $decodedValue = json_decode($value);
                                if(empty($decodedValue)) $decodedValue = explode(',', $value);

                                $value = $decodedValue;
                            }

                            if(is_array($value))
                            {
                                $strValue = '';
                                foreach($value as $v)
                                {
                                    $strValue .= zget($field->options, $v) . ',';
                                }
                                $data->$key = trim($strValue, ',');
                            }
                            else
                            {
                                $data->$key = zget($field->options, $value);
                            }
                        }
                    }
                }
            }

            $this->post->set('fields', (array)$exportFields);
            $this->post->set('rows',   (array)$flowDatas);
            $this->post->set('kind', $flow->module);
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        $this->view->fileName = $flow->name;
        $this->view->fields   = $this->loadModel('workflowfield')->getExportFields($flow->module);
        $this->view->module   = $module;
        $this->display();
    }
}
