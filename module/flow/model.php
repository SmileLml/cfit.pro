<?php
class flowModel extends model
{
    /**
     * Get data of a flow by Id.
     *
     * @param  object $flow
     * @param  int    $dataID
     * @param  bool   $decode
     * @access public
     * @return object
     */
    public function getDataByID($flow, $dataID, $decode = true)
    {
        $data = $this->dao->select('*')->from("`$flow->table`")->where('id')->eq($dataID)->fetch();

        if($data)
        {
            $fields = $this->dao->select('field')->from(TABLE_WORKFLOWFIELD)->where('module')->eq($flow->module)->andWhere('control')->eq('file')->fetchPairs();
            $files  = $this->loadModel('file')->getByObject($flow->module, $dataID);
            foreach($fields as $field)
            {
                $filesName = "{$field}files";
                $data->$filesName = array();
                foreach($files as $file)
                {
                    if($file->extra == $field) $data->{$filesName}[] = $file;
                }
            }

            $data = $this->processDBData($flow->module, $data, $decode);
        }

        unset(dao::$cache[$flow->table]);

        return $data;
    }

    /**
     * Get data list by id list.
     *
     * @param  object $flow
     * @param  array  $idList
     * @access public
     * @return array
     */
    public function getDataByIDList($flow, $idList)
    {
        $dataList = $this->dao->select('*')->from($flow->table)->where('id')->in($idList)->fetchAll('id');

        foreach($dataList as $data) $data = $this->processDBData($flow->module, $data);

        return $dataList;
    }

    /**
     * Get data list of a flow.
     *
     * @param  object $flow
     * @param  string $mode
     * @param  int    $label
     * @param  string $categoryQuery
     * @param  int    $parentID
     * @param  string $orderBy
     * @param  object $pager
     * @param  string $extraQuery   the param is used in CRRC extension.
     * @access public
     * @return array
     */
    public function getDataList($flow, $mode = 'browse', $label = 0, $categoryQuery = '', $parentID = 0, $orderBy = '', $pager = null, $extraQuery = '')
    {
        $querySessionName = $flow->module . 'Query';
        if($this->session->$querySessionName == false) $this->session->set($querySessionName, ' 1 = 1');
        $searchQuery = $this->loadModel('search')->replaceDynamic($this->session->$querySessionName);

        $labelQuery = '';
        if($label)
        {
            list($labelQuery, $labelOrderBy) = $this->getLabelQueryAndOrderBy($label);

            if(!$orderBy) $orderBy = $labelOrderBy;
        }

        if(!$orderBy) $orderBy = 'id_desc';

        $dataList = $this->dao->select('*')->from($flow->table)
            ->where('deleted')->eq('0')
            ->beginIF(!$flow->buildin && $parentID)->andWhere('parent')->eq($parentID)->fi()
            ->beginIF($mode == 'bysearch')->andWhere($searchQuery)->fi()
            ->beginIF($labelQuery)->andWhere($labelQuery)->fi()
            ->beginIF($categoryQuery)->andWhere($categoryQuery)->fi()
            ->beginIF($extraQuery)->andWhere($extraQuery)->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');

        $this->session->set($flow->module . 'QueryCondition', $this->dao->get());

        foreach($dataList as $data) $data = $this->processDBData($flow->module, $data);

        return $dataList;
    }

    /**
     * Get data pairs of a flow.
     *
     * @param  object $flow
     * @param  mixed  $idList   array | string
     * @access public
     * @return array
     */
    public function getDataPairs($flow, $idList = array())
    {
        $valueFields = $this->loadModel('workflowfield', 'flow')->getValueFields($flow->module);
        if(!$valueFields)
        {
            $dataList = $this->dao->select('id')->from($flow->table)
                ->where('deleted')->eq('0')
                ->beginIF($idList)->andWhere('id')->in($idList)->fi()
                ->fetchPairs();
            foreach($dataList as $dataID) $dataList[$dataID] = $flow->name . $dataID;

            return $dataList;
        }

        $dataList = $this->dao->select('*')->from($flow->table)
            ->where('deleted')->eq('0')
            ->beginIF($idList)->andWhere('id')->in($idList)->fi()
            ->fetchAll();

        $fields = $this->dao->select('module, field, type, control, options')->from(TABLE_WORKFLOWFIELD)
            ->where('module')->eq($flow->module)
            ->andWhere('field')->in($valueFields)
            ->fetchAll('field');
        $fields = $this->loadModel('workflowaction', 'flow')->processFields($fields, true, $dataList);

        $dataPairs = array();
        foreach($dataList as $data)
        {
            $value = '';
            foreach($valueFields as $field)
            {
                $field = $fields[$field];

                /* Get display value. */
                if($field->field == 'id')
                {
                    $value .= $flow->name . $data->id . ' ';
                }
                elseif($field->options)
                {
                    if($field->control == 'multi-select' or $field->control == 'checkbox')
                    {
                        $dataValues = explode(',', $data->{$field->field});
                        if(!$dataValues) continue;

                        foreach($dataValues as $dataValue) $value .= zget($field->options, $dataValue) . ' ';
                    }
                    else
                    {
                        $value .= zget($field->options, $data->{$field->field}) . ' ';
                    }
                }
                else
                {
                    $value .= $data->{$field->field} . ' ';
                }

                if(strpos(',date,datetime,', ",$field->control,") !== false) $value = formatTime($value);
            }

            /* Push into array to display. */
            $dataPairs[$data->id] = trim($value);
        }

        return array('') + $dataPairs;
    }

    /**
     * Get query string and orderBy of a label.
     *
     * @param  int    $labelID
     * @access public
     * @return array
     */
    public function getLabelQueryAndOrderBy($labelID)
    {
        $this->loadModel('workflowhook', 'flow');

        $query  = '(1';
        $label  = $this->loadModel('workflowlabel', 'flow')->getByID($labelID);
        $fields = $this->loadModel('workflowfield', 'flow')->getControlPairs($label->module);
        foreach($label->params as $param)
        {
            $field    = $param['field'];
            $value    = $param['value'];
            $operator = $param['operator'];

            if(in_array($value, $this->config->flow->variables)) $value = $this->workflowhook->getParamRealValue($value);

            if($operator == 'include')
            {
                if($fields[$field] == 'multi-select' or $fields[$field] == 'checkbox')
                {
                    $values = explode(',', $value);
                    foreach($values as $value)
                    {
                        $value = $this->dbh->quote("%$value%");

                        $query .= " AND (1 AND `$field` LIKE $value)";
                    }
                }
                else
                {
                    $value = $this->dbh->quote("%$value%");

                    $query .= " AND (1 AND `$field` LIKE $value)";
                }
            }
            else if($operator == 'notinclude')
            {
                if($fields[$field] == 'multi-select' or $fields[$field] == 'checkbox')
                {
                    $values = explode(',', $value);
                    foreach($values as $value)
                    {
                        $value = $this->dbh->quote("%$value%");

                        $query .= " AND (1 AND `$field` NOT LIKE $value)";
                    }
                }
                else
                {
                    $value = $this->dbh->quote("%$value%");

                    $query .= " AND (1 AND `$field` NOT LIKE $value)";
                }
            }
            else if($operator == 'between')
            {
                $value2 = $param['value2'];
                $value2 = $this->dbh->quote($value2);
                $value  = $this->dbh->quote($value);

                $query .= " AND (1 AND `$field` BETWEEN $value AND $value2)";
            }
            else
            {
                $value    = $this->dbh->quote($value);
                $operator = zget($this->lang->workflowlabel->operatorList, $operator);

                $query .= " AND (1 AND `$field` $operator $value)";
            }
        }
        $query .= ')';

        $orderBy = '';
        if($label->orderBy)
        {
            foreach($label->orderBy as $labelOrderBy)
            {
                $orderBy .= $labelOrderBy['field'] . '_' . $labelOrderBy['type'] . ',';
            }
            if($orderBy) $orderBy = rtrim($orderBy, ',');
        }

        return array($query, $orderBy);
    }

    /**
     * Get module pairs to link.
     *
     * @param  string module
     * @access public
     * @return array
     */
    public function getLinkPairs($module)
    {
        $linkPairs = $this->loadModel('workflow', 'flow')->getPairs('', 'flow');
        unset($linkPairs[$module]);

        return $linkPairs;
    }

    /**
     * Get linked datas of a flow.
     *
     * @param  string $module
     * @param  int    $dataID
     * @param  string $linkType
     * @access public
     * @return array
     */
    public function getLinkedDatas($module, $dataID, $linkType = '')
    {
        $linkedDatas = array();

        if($linkType)
        {
            $linkedIDList = $this->dao->select('linkedID')->from(TABLE_WORKFLOWLINKDATA)
                ->where('objectType')->eq($module)
                ->andWhere('objectID')->eq($dataID)
                ->andWhere('linkedType')->eq($linkType)
                ->fetchPairs();

            if($linkedIDList)
            {
                $linkedFlow = $this->loadModel('workflow', 'flow')->getByModule($linkType);

                if($linkedFlow)
                {
                    $linkedDatas = $this->getDataByIDList($linkedFlow, $linkedIDList);
                }
                else if(in_array($linkType, $this->config->flow->linkPairs))
                {
                    list($linkedApp, $linkedModule) = $this->extractAppAndModule($linkType);

                    $linkedDatas = $this->loadModel($linkedModule, $linkedApp)->getByIDList($linkedIDList);
                }
            }
        }
        else
        {
            $flowList   = $this->dao->select('*')->from(TABLE_WORKFLOW)->fetchAll('module');
            $groupDatas = $this->dao->select('*')->from(TABLE_WORKFLOWLINKDATA)
                ->where('objectType')->eq($module)
                ->andWhere('objectID')->eq($dataID)
                ->fetchGroup('linkedType');

            foreach($groupDatas as $linkType => $datas)
            {
                $linkedFlow = zget($flowList, $linkType, '');

                $linkedIDList = array();
                foreach($datas as $data) $linkedIDList[] = $data->linkedID;

                if($linkedFlow)
                {
                    $linkedDatas[$linkType] = $this->getDataByIDList($linkedFlow, $linkedIDList);
                }
                else if(in_array($linkType, $this->config->flow->linkPairs))
                {
                    list($linkedApp, $linkedModule) = $this->extractAppAndModule($linkType);

                    $linkedDatas[$linkType] = $this->loadModel($linkedModule, $linkedApp)->getByIDList($linkedIDList);
                }
            }
        }

        return $linkedDatas;
    }

    /**
     * Get unlinked datas of a flow.
     *
     * @param  string $module
     * @param  int    $dataID
     * @param  string $linkedFlow
     * @param  string $mode
     * @access public
     * @return array
     */
    public function getUnlinkedDatas($module, $dataID, $linkedFlow, $mode)
    {
        $linkedDatas   = $this->getLinkedDatas($module, $dataID, $linkedFlow->module);
        $unlinkedDatas = $this->getDataList($linkedFlow, $mode);

        foreach($unlinkedDatas as $id => $data)
        {
            if(isset($linkedDatas[$id])) unset($unlinkedDatas[$id]);
        }

        return $unlinkedDatas;
    }

    /**
     * Get formula script.
     *
     * @param  string $module
     * @param  object $action
     * @param  array  $fields
     * @param  array  $childFields
     * @access public
     * @return string
     */
    public function getFormulaScript($module, $action, $fields, $childFields = array())
    {
        $script = '';
        foreach($childFields as $childModule => $moduleFields)
        {
            $result = $this->getFieldScript($childModule, $action, $moduleFields, $mode = 'sub');
            if(is_array($result)) return $result['message'];

            $script .= $result;
        }

        $result = $this->getFieldScript($module, $action, $fields);
        if(is_array($result)) return $result['message'];

        $script .= $result;

        if($script) $script = "<script>\n$(function()\n{\n{$script}});\n</script>";

        return $script;
    }

    /**
     * Get script of formula fields of a module.
     *
     * @param  string $module
     * @param  object $action
     * @param  array  $fields
     * @access public
     * @return string
     */
    public function getFieldScript($module, $action, $fields, $mode = '')
    {
        static $targetFields = array();

        $flowModule = $mode == 'sub' ? substr($module, 4) : $module;
        foreach($fields as $field) $targetFields[$flowModule][$field->field] = $field;

        $script = '';
        $blank  = '    ';
        $errors = array();
        foreach($fields as $field)
        {
            if($field->control != 'formula' or empty($field->expression)) continue;

            $expression = '';
            $selectors  = array();
            $conditions = array();
            $vars       = array();
            $functions  = array();

            $items = json_decode($field->expression);
            foreach($items as $item)
            {
                switch($item->type)
                {
                case 'target' :
                    if(!isset($targetFields[$item->module][$item->field]))
                    {
                        $expression .= 0;
                        $errors[]    = "console.log('Error: target field not found.');";
                        $errors[]    = "console.log(" . json_encode($item). ");";
                        break;
                    }

                    $targetField = $targetFields[$item->module][$item->field];
                    $parseFunc   = $targetField->type == 'decimal' ? 'parseFloat' : 'parseInt';

                    if(!empty($item->function))
                    {
                        $var  = "{$item->module}_{$targetField->field}_{$item->function}";
                        $list = "{$item->module}_{$targetField->field}_list";

                        $functions[] = "var {$var} = 0;";
                        $functions[] = "var {$list} = $.map($('.table-child [name*=\\\[sub_{$item->module}\\\]][name*=\\\[{$targetField->field}\\\]]'), function(item){if($.isNumeric(item.value)) return {$parseFunc}(item.value);});";
                        switch($item->function)
                        {
                        case 'sum' :
                            $functions[] = "if($list.length > 0) {$var} = {$parseFunc}({$list}.reduce((a, b) => {$parseFunc}(a) + {$parseFunc}(b)));\n";
                            break;
                        case 'average' :
                            $functions[] = "if($list.length > 0) {$var} = {$parseFunc}({$list}.reduce((a, b) => {$parseFunc}(a) + {$parseFunc}(b))) / {$list}.length;\n";
                            break;
                        case 'max' :
                            $functions[] = "if($list.length > 0) {$var} = {$parseFunc}(Math.max(...{$list}));\n";
                            break;
                        case 'min' :
                            $functions[] = "if($list.length > 0) {$var} = {$parseFunc}(Math.min(...{$list}));\n";
                            break;
                        case 'count' :
                            $functions[] = "if($list.length > 0) {$var} = {$list}.length;\n";
                            break;
                        }

                        $selector    = "[name*=\\\[sub_{$item->module}\\\]][name*=\\\[{$targetField->field}\\\]]";
                        $expression .= $var;
                    }
                    else
                    {
                        $var = "{$flowModule}_{$targetField->field}";

                        if($mode == 'sub')
                        {
                            $selector = "[name*=\\\[{$module}\\\]][name*=\\\[{$targetField->field}\\\]]";
                            $vars[]   = "var {$var} = {$parseFunc}($(this).closest('tr').find('{$selector}').val());";
                        }
                        else
                        {
                            if($action->type == 'batch' && $action->batchMode == 'different')
                            {
                                $selector = "[name^=dataList][name*=\\\[{$targetField->field}\\\]]";
                                $vars[]   = "var {$var} = {$parseFunc}($(this).closest('tr').find('{$selector}').val());";
                            }
                            else
                            {
                                $selector = "#{$targetField->field}";
                                $vars[]   = "var {$var} = {$parseFunc}($('{$selector}').val());";
                            }
                        }

                        $expression .= $var;
                    }

                    $selectors[]  = $selector;
                    $conditions[] = "if(!$.isNumeric($('{$selector}').val())) return false;";
                    break;
                case 'number' :
                    $expression .= $item->value;
                    break;
                default :
                    if($item->operator != '(' && $item->operator != ')') $item->operator = " {$item->operator} ";

                    $expression .= $item->operator;
                }
            }

            if($expression)
            {
                if($field->type == 'decimal')
                {
                    $decimalDigits = (int)substr($field->length, strpos($field->length, ',') + 1);
                    $precision     = pow(10, $decimalDigits);
                    $expression    = "Math.round(({$expression}) * {$precision}) / {$precision}";
                }
                else
                {
                    $expression = "Math.round({$expression})";
                }

                if($selectors)
                {
                    $script .= $blank . "$(document).on('change', '" . implode(',', $selectors) . "', function()\n";
                    $script .= $blank . "{\n";

                    //foreach($conditions as $condition) $script .= $blank . $blank . $condition . "\n";
                    //if($conditions) $script .= "\n";

                    foreach($vars as $var) $script .= $blank . $blank . $var . "\n";
                    if($vars) $script .= "\n";

                    foreach($functions as $function) $script .= $blank . $blank . $function . "\n";

                    $script .= $blank;
                }

                if($mode == 'sub')
                {
                    if($selectors)
                    {
                        $script .= $blank . "$(this).closest('tr').find('[name*=\\\[{$module}\\\]][name*=\\\[{$field->field}\\\]]').val({$expression}).change();\n";
                    }
                    else
                    {
                        $script .= $blank . "$('[name*=\\\[{$module}\\\]][name*=\\\[{$field->field}\\\]]').val({$expression}).change();\n";
                    }
                }
                else
                {
                    if($action->type == 'batch' && $action->batchMode == 'different')
                    {
                        if($selectors)
                        {
                            $script .= $blank . "$(this).closest('tr').find('[name^=dataList][name*=\\\[{$field->field}\\\]]').val({$expression}).change();\n";
                        }
                        else
                        {
                            $script .= $blank . "$('[name^=dataList][name*=\\\[{$field->field}\\\]]').val({$expression}).change();\n";
                        }
                    }
                    else
                    {
                        $script .= $blank . "$('#{$field->field}').val({$expression}).change();\n";
                    }
                }

                if($selectors) $script .= $blank . "});\n\n";
            }
        }

        foreach($errors as $error) $script .= $blank . $error . "\n";

        return $script;
    }

    /**
     * Get script to reset options of subStatus.
     *
     * @param  object $field
     * @access public
     * @return string
     */
    public function getSubStatusScript($field)
    {
        $this->app->loadLang($field->module);

        $statusLabel = $this->lang->{$field->module}->status;

        $field = $this->loadModel('workflowfield', 'flow')->getByField($field->module, $field->field, $mergeOptions = false);

        $script  = '<script>';
        $script .= "var subStatusData = JSON.parse('" . json_encode($field->options) . "');\n";
        $script .= <<<EOT
$(function()
{
    if($('#status').parents('tr').hasClass('hide'))
    {
        var statusLabel = '{$statusLabel}';
        if(statusLabel) $('#subStatus').parent().prev().html(statusLabel);
    }

    $('#status').change(function()
    {
        var status = $(this).val();
        if(!status) return;

        var oldSubStatus = $('#subStatus').val();
        var subStatus    = subStatusData[status];
        var options      = subStatus === undefined ? {} : subStatus.options;

        $('#subStatus').empty();
        $('#subStatus').append('<option></option>');

        $.each(options, function(key, value)
        {
            $('#subStatus').append("<option value='" + key + "'>" + value + '</option>');
        });

        $('#subStatus').val(oldSubStatus).trigger('chosen:updated');
    });

    $('#status').change();
});
EOT;
        $script .= '</script>';

        return $script;
    }

    /**
     * Get script by linkages of an action.
     *
     * @param  object $action
     * @param  array  $fields
     * @access public
     * @return string
     */
    public function getLinkageScript($action, $fields)
    {
        $script   = '';
        $linkages = $action->linkages;

        foreach($linkages as $linkage)
        {
            $sources = zget($linkage, 'sources', array());
            $targets = zget($linkage, 'targets', array());

            if(!$linkage or !$sources or !$targets) continue;

            $targetInit   = '';
            $targetChange = '';

            foreach($targets as $target)
            {
                $initFunc   = $target->status == 'show' ? 'hide()' : 'show()';
                $initAttr   = $target->status == 'show' ? "attr('disabled', 'disabled')" : "removeAttr('disabled')";
                $changeFunc = $target->status == 'show' ? 'show()' : 'hide()';
                $changeAttr = $target->status == 'show' ? "removeAttr('disabled')" : "attr('disabled', 'disabled')";

                $name = $fields[$target->field]->control == 'file' ? "files{$target->field}[]" : $target->field;
                if(strpos(',multi-select,checkbox,', ",{$fields[$target->field]->control}," !== false)) $name .= '[]';

                $targetInit   .= "        $(\"[name='{$name}']\").{$initAttr};\n";
                $targetInit   .= "        $(\"[name='{$name}']\").parents('tr').{$initFunc};";
                $targetChange .= "        $(\"[name='{$name}']\").{$changeAttr};\n";
                $targetChange .= "        $(\"[name='{$name}']\").parents('tr').{$changeFunc};";

                if($target != end($targets))
                {
                    $targetInit   .= "\n";
                    $targetChange .= "\n";
                }
            }

            foreach($sources as $source)
            {
                if(!isset($fields[$source->field])) continue;

                $field = $fields[$source->field];
                $value = str_replace('"', '\"', $source->value);

                if($field->control == 'radio' && !$field->readonly)
                {
                    $val = "var val = $('[name^={$source->field}]:checked').val();";
                }
                else if($field->control == 'checkbox' && !$field->readonly)
                {
                    $val = <<<EOT
    var values = [];
    $('[name^={$source->field}]:checked').each(function()
    {
        values.push($(this).val());
    });
    var val = values.join();
EOT;
                }
                else
                {
                    $val = "var val = $(this).val();";
                }

                $script .= <<<EOT
<script>
$("[name='{$source->field}']").change(function()
{
    {$val}
    if(val {$source->operator} "$value")
    {
{$targetChange}
    }
    else
    {
{$targetInit}
    }

    if(('.chosen').length > 0) $('.chosen').trigger('chosen:updated');
});

$("[name='{$source->field}']").change();
</script>
EOT;
            }
        }

        return $script;
    }

    /**
     * Get menu of a flow.
     *
     * @param  object $flow
     * @param  array  $labels
     * @param  array  $categories
     * @access public
     * @return void
     */
    public function getModuleMenu($flow, $labels = array(), $categories = array())
    {
        $this->loadModel('workflowlabel', 'flow');

        $isMobile   = $this->app->viewType === 'mhtml';
        $moduleMenu = !$isMobile ? "<nav id='menu'><div class='container'><ul class='nav nav-default'>" : '';

        if(empty($labels)) $labels = $this->loadModel('workflowlabel', 'flow')->getList($flow->module);
        if(!empty($labels)&& is_array($labels))
        {
            foreach($labels as $label)
            {
                if(!commonModel::hasPriv($flow->module, $label->id)) continue;

                $link = baseHTML::a(helper::createLink($flow->module, 'browse', "mode=browse&label=$label->id"), $label->label);
                $moduleMenu .= !$isMobile ? "<li>" . $link . "</li>" : $link;
            }
        }

        if(!$isMobile)
        {
            /* Add report label if has privelage. */
            if(commonModel::hasPriv($flow->module, 'report'))
            {
                $this->app->loadLang('workflowaction', 'flow');
                $moduleMenu .= '<li>' . baseHTML::a(helper::createLink($flow->module, 'report', "module=$flow->module"), $this->lang->workflowaction->default->actions['report']) . '</li>';
            }

            /* Add category labels. */
            if(commonModel::hasPriv('tree', 'browse'))
            {
                if(!empty($categories))
                {
                    foreach($categories as $category) $moduleMenu .= '<li>' . baseHTML::a(helper::createLink('tree', 'browse', "type=$category->type&startModule=&root=&from=$flow->module"), $category->name) . '</li>';
                }
                else
                {
                    $fields = $this->loadModel('workflowfield', 'flow')->getCategoryFields($flow->module);
                    foreach($fields as $type => $field)
                    {
                        $moduleMenu .= '<li>' . baseHTML::a(helper::createLink('tree', 'browse', "type=$type&startModule=&root=&from=$flow->module"), $field->name) . '</li>';
                    }
                }
            }
        }

        $moduleMenu .= !$isMobile ? "</ul></div></nav>" : '';

        return $moduleMenu;
    }

    /**
     * Get category menu settings of a flow.
     *
     * @param  string $module
     * @param  string $mode
     * @param  int    $label
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return string
     */
    public function getCategories($module, $mode, $label, $orderBy, $recTotal, $recPerPage, $pageID)
    {
        $categories = array();

        $fields = $this->loadModel('workflowfield', 'flow')->getCategoryFields($module);
        if(!$fields) return $categories;

        $this->loadModel('dept');
        $this->loadModel('dept');
        foreach($fields as $type => $field)
        {
            $treeMenu = $this->tree->getTreeMenu(0, $type, 0, array('flowModel', 'createCategoryLink'));
            $treeMenu = str_replace(array('MODULE', 'MODE', 'LABEL', 'FIELD', 'ORDERBY', 'RECTOTAL', 'RECPERPAGE', 'PAGEID'), array($module, $mode, $label, $field->field, $orderBy, $recTotal, $recPerPage, $pageID), $treeMenu);

            $category = new stdclass();
            $category->type     = $type;
            $category->name     = $field->name;
            $category->treeMenu = $treeMenu;

            $categories[$type] = $category;
        }

        return $categories;
    }

    /**
     * Create category manage link.
     *
     * @param  object $category
     * @static
     * @access public
     * @return string
     */
    public static function createCategoryLink($category)
    {
        return baseHTML::a(helper::createLink('MODULE', 'browse', "mode=MODE&label=LABEL&category=FIELD=$category->id&orderBy=ORDERBY&recTotal=RECTOTAL&recPerPage=RECPERPAGE&pageID=PAGEID"), $category->name, "id='{$category->type}{$category->id}'");
    }

    /**
     * Get summary of dataList.
     *
     * @param  array  $dataList
     * @param  array  $fields
     * @access public
     * @return string
     */
    public function getSummary($dataList, $fields)
    {
        if(!$dataList) return '';

        $fieldValues = array();
        foreach($fields as $field)
        {
            if(!$field->show or $field->field == 'actions') continue;
            if(!$field->summary or $field->field == 'id' or !in_array($field->type, $this->config->workflowfield->numberTypes)) continue;

            foreach($dataList as $data) $fieldValues[$field->field][] = $data->{$field->field};
        }

        if(!$fieldValues) return '';

        $fieldSummary = explode(',', $field->summary);

        $summaryList = array();
        foreach($fieldValues as $field => $values)
        {
            asort($values);

            $fieldSummary = explode(',', $fields[$field]->summary);
            foreach($fieldSummary as $summaryType)
            {
                switch($summaryType)
                {
                case 'sum':
                    $summaryList[$field][$summaryType] = array_sum($values);
                    break;
                case 'average':
                    $decimalDigits = 2;
                    $fieldType     = $fields[$field]->type;
                    if($fieldType == 'decimal')
                    {
                        $fieldLength   = $fields[$field]->length;
                        $decimalDigits = substr($fieldLength, strpos($fieldLength, ',') + 1);
                    }

                    $summaryList[$field][$summaryType] = round(array_sum($values) / count($values), $decimalDigits);
                    break;
                case 'max':
                    $summaryList[$field][$summaryType] = end($values);
                    break;
                case 'min':
                    $summaryList[$field][$summaryType] = reset($values);
                    break;
                }
            }
        }

        if(!$summaryList) return '';

        $summary = '';
        foreach($summaryList as $field => $fieldSummary)
        {
            $summary .= $fields[$field]->name . $this->lang->colon;
            foreach($fieldSummary as $type => $value) $summary .= $this->lang->workflowlayout->summaryList[$type] . $value . $this->lang->comma;
            $summary = rtrim($summary, $this->lang->comma) . $this->lang->semicolon;
        }

        return rtrim($summary, $this->lang->semicolon);
    }

    /**
     * Get the post data list.
     *
     * @param  object $flow
     * @param  object $action
     * @access public
     * @return array
     */
    public function getPostData($flow, $action)
    {
        $postData = fixer::input('post')->get();
        if($action->action != 'batchcreate')
        {
            /* Get post data id list. */
            $dataIDList = array();
            if($action->batchMode == 'same') $dataIDList = $postData->dataIDList;
            if($action->batchMode == 'different')
            {
                $dataList = $postData->dataList;

                foreach($dataList as $dataID => $data) $dataIDList[] = $dataID;
            }
            if(!$dataIDList) return array();

            /* Check post datas. */
            $validDataList = $this->getDataByIDList($flow, $dataIDList);
            foreach($validDataList as $dataID => $data)
            {
                $enabled = $this->checkConditions($action->conditions, $data);
                if(!$enabled) unset($validDataList[$dataID]);
            }
            if(!$validDataList) return array();
        }

        $dataList = array();
        if($action->batchMode == 'same')
        {
            $dataIDList = $postData->dataIDList;
            if(!is_array($dataIDList)) $dataIDList = explode(',', $dataIDList);

            foreach($dataIDList as $dataID)
            {
                if($action->action != 'batchcreate')
                {
                    if(!isset($validDataList[$dataID])) continue; // Remove invalid datas.
                }

                $dataList[$dataID]['id'] = $dataID;

                if($postData->data)
                {
                    foreach($postData->data as $field => $value) $dataList[$dataID][$field] = $value;
                }
            }
        }

        if($action->batchMode == 'different')
        {
            $dataList = $postData->dataList;

            foreach($dataList as $dataID => $data)
            {
                if($action->action != 'batchcreate')
                {
                    /* Remove invalid datas. */
                    if(!isset($validDataList[$dataID]))
                    {
                        unset($dataList[$dataID]);
                        continue;
                    }
                }

                /* Process ditto to real value. */
                foreach($data as $field => $value)
                {
                    if(empty($$field)) $$field = '';

                    $$field = $value == 'ditto' ? $$field : $value;

                    if(is_array($$field)) $$field = array_filter($$field);

                    $dataList[$dataID][$field] = $$field;
                }
            }

            /* Remove the empty record when batch create. */
            if($action->action == 'batchcreate')
            {
                foreach($dataList as $dataID => $data)
                {
                    $emptyData = true;
                    foreach($data as $field => $value)
                    {
                        if($value)
                        {
                            $emptyData = false;
                            break;
                        }
                    }
                    if($emptyData) unset($dataList[$dataID]);
                }
            }
        }

        return $dataList;
    }

    /**
     * Get data export to excel.
     *
     * @param  object $flow
     * @param  array  $flowDatas
     * @param  array  $flowFields
     * @access public
     * @return array
     */
    public function getExportData($flow, $flowDatas, $flowFields)
    {
        $fields         = array();
        $rows           = array();
        $numberFields   = array();
        $headerRowCount = 1;
        $headerRowspan  = array();
        $headerColspan  = array();
        $dataRowspan    = array();

        /* Prepare flow fields and number fields. */
        $exportFields = $this->loadModel('workflowfield', 'flow')->getExportFields($flow->module);
        foreach($flowFields as $field)
        {
            if(!isset($exportFields[$field->field])) continue;

            if(in_array($field->type, $this->config->workflowfield->numberTypes))
            {
                if($field->control != 'select' and $field->control != 'radio') $numberFields[] = $field->field;
            }
        }

        $flowDatas = $this->processExportDatas($flowDatas, $flowFields, $exportFields); // Prepare flow datas export to excel.

        /* Check child modules and prepare datas of them. */
        $childDataCount = array();
        $childDatas     = array();
        $dataIDList     = array_keys($flowDatas);
        $childModules   = $this->loadModel('workflow', 'flow')->getList('table', '', $flow->module);
        foreach($childModules as $childModule)
        {
            /* Get child datas group by the parent field. */
            $groupDatas = $this->dao->select('*')->from($childModule->table)
                ->where('deleted')->eq('0')
                ->andWhere('parent')->in($dataIDList)
                ->fetchGroup('parent');

            if($groupDatas)
            {
                $headerRowCount    = 2;
                $childExportFields = $this->workflowfield->getExportFields($childModule->module);
                $childFields       = $this->workflowfield->getList($childModule->module);

                /* Prepare child fields and number fields. */
                foreach($childFields as $field)
                {
                    if(!isset($childExportFields[$field->field])) continue;

                    if(in_array($field->type, $this->config->workflowfield->numberTypes))
                    {
                        if($field->control != 'select' and $field->control != 'radio') $numberFields[] = $childModule->module . '_' . $field->field;
                    }
                }

                /* Prepare fields export to excel. */
                foreach($exportFields as $fieldKey => $fieldLabel)
                {
                    $fields[0][$fieldKey] = $fieldLabel;
                    $fields[1][$fieldKey] = $fieldLabel;

                    $headerRowspan[0][$fieldKey] = 2;   // Prepare the header rowspan of flow fields.
                }

                /* Prepare fields export to excel. */
                $index = 1;
                foreach($childExportFields as $fieldKey => $fieldLabel)
                {
                    $fieldKey = $childModule->module . '_' . $fieldKey;

                    $fields[0][$fieldKey] = $childModule->name;
                    $fields[1][$fieldKey] = $fieldLabel;

                    if($index == 1) $headerColspan[0][$fieldKey] = count($childExportFields);   // Prepare the header colspan of child fields.

                    $index++;
                }

                /* Prepare child datas export to excel. */
                foreach($groupDatas as $parent => $datas)
                {
                    /* Prepare the child data count. */
                    $count = count($datas);
                    if(isset($childDataCount[$parent]))
                    {
                        if($count > $childDataCount[$parent]) $childDataCount[$parent] = $count;
                    }
                    else
                    {
                        $childDataCount[$parent] = $count;
                    }

                    $datas = $this->processExportDatas($datas, $childFields, $childExportFields);

                    $childDatas[$parent][$childModule->module] = $datas;
                }
            }
        }

        if(!$fields) $fields = $exportFields;

        /* Build datas export to excel according to the flow datas, the child datas and the child data count. */
        $row = 1;
        foreach($flowDatas as $dataID => $flowData)
        {
            $childCount = zget($childDataCount, $dataID, 1);

            for($index = 0; $index < $childCount; $index++)
            {
                foreach($flowData as $field => $value)
                {
                    $rows[$row][$field] = $value;   // Build datas according to the flow datas.

                    if($index == 0)
                    {
                        $dataRowspan[$row][$field] = $childCount;   // Prepare the data rowspan.
                    }
                }

                /* If the flow data has child datas, build them. */
                if(isset($childDatas[$dataID]))
                {
                    foreach($childDatas[$dataID] as $childModule => $datas)
                    {
                        if(isset($datas[$index]))
                        {
                            foreach($datas[$index] as $childField => $childValue) $rows[$row][$childModule . '_' . $childField] = $childValue;
                        }
                        else
                        {
                            foreach($datas[0] as $childField => $childValue) $rows[$row][$childModule . '_' . $childField] = '';
                        }
                    }
                }

                $row++;
            }
        }

        $data = new stdclass();
        $data->fields         = $fields;
        $data->rows           = $rows;
        $data->kind           = $flow->module;
        $data->title          = $flow->name;
        $data->numberFields   = $numberFields;
        $data->headerRowCount = $headerRowCount;
        $data->headerRowspan  = $headerRowspan;
        $data->headerColspan  = $headerColspan;
        $data->dataRowspan    = $dataRowspan;
        $data->nocolor        = true;

        return $data;
    }

    /**
     * Get import data.
     *
     * @param  object $flow
     * @access public
     * @return array
     */
    public function getImportData($flow)
    {
        $this->loadModel('workflowfield', 'flow');

        $fields = array();
        $titles = array();

        $flowFields = $this->dao->select('*')->from(TABLE_WORKFLOWFIELD)
            ->where('module')->eq($flow->module)
            ->andWhere('canExport')->eq('1')
            ->orderBy('`order`')
            ->fetchAll('field');

        foreach($flowFields as $field) $titles[$field->field] = $field->name;

        $fields[$flow->module] = $flowFields;

        $titles['sub_tables'] = array();    // 初始化数组防止没有明细表时循环出错。

        /* 获取明细表导出的字段。*/
        $subTables = $this->loadModel('workflow', 'flow')->getList('table', '', $flow->module);
        foreach($subTables as $subTable)
        {
            $subFields = $this->dao->select('*')->from(TABLE_WORKFLOWFIELD)
                ->where('module')->eq($subTable->module)
                ->andWhere('canExport')->eq('1')
                ->orderBy('`order`')
                ->fetchAll('field');

            if(!$subFields) continue;

            $titles['sub_tables'][$subTable->module]['name'] = $subTable->name;

            foreach($subFields as $subField) $titles['sub_tables'][$subTable->module]['fields'][$subField->field] = $subField->name;

            $fields[$subTable->module] = $subFields;
        }

        list($fields, $datas) = $this->parseExcel($flow, $fields, $titles);

        return array($fields, $titles, $datas);
    }

    /**
     * Parse excel file into array.
     *
     * @param  object $flow
     * @param  array  $fields
     * @param  array  $titles
     * @param  int    $sheetIndex
     * @access public
     * @return array
     */
    public function parseExcel($flow, $fields = array(), $titles = array(), $sheetIndex = 0)
    {
        $file = $this->session->importFile;

        $phpExcel  = $this->app->loadClass('phpexcel');
        $phpReader = new PHPExcel_Reader_Excel2007();
        if(!$phpReader->canRead($file)) $phpReader = new PHPExcel_Reader_Excel5();

        $excel      = $phpReader->load($file);
        $sheet      = $excel->getSheet($sheetIndex);            // 获取工作簿对象。
        $allRows    = $sheet->getHighestRow();                  // 获取内容区域最大行数。
        $allColumns = $sheet->getHighestColumn();               // 获取内容区域最大列数。
        $mergeCells = $sheet->getMergeCells();                  // 获取合并单元格的集合。
        $mergeCells = $this->processMergeCells($mergeCells);    // 处理合并单元格的数据。

        /* In php, 'A'++  === 'B', 'Z'++ === 'AA', 'a'++ === 'b', 'z'++ === 'aa'. */
        $allColumns++;          // 终止列设为内容区域列的下一列。
        $titleRows = 1;         // 表头行数。
        $column    = 'A';       // 起始列。
        $columnKey = array();   // 每一列和模块、字段的对应关系集合。

        /* 遍历表头区域，获取每一列对应的模块和字段。*/
        while($column != $allColumns)   // 遍历列。
        {
            $module = $flow->module;    // 默认每一列对应的流程是当前流程。

            for($row = 1; $row <= $titleRows; $row++)   // 遍历行。
            {
                if($row > 2) break;         // 表头最大支持两行。

                $cell = $column . $row;     // 当前读取的单元格。

                if(isset($mergeCells[$cell]))
                {
                    $mergeCell = $mergeCells[$cell];
                    $cell      = $mergeCell->parent;

                    if($mergeCell->rowspan > $titleRows) $titleRows = $mergeCell->rowspan;  // 如果当前读取的单元格被跨行合并到其他单元格，则更新表头行数。
                }
                $title = $sheet->getCell($cell)->getValue();    // 读取单元格内容。

                if($row == 1)
                {
                    $field = array_search($title, $titles);     // 根据读取的内容和表头字段数组获取字段代号。

                    /* 如果没有获取到字段代号则从明细表的表头字段数组中查找。*/
                    if(!$field)
                    {
                        $module = '';

                        foreach($titles['sub_tables'] as $subModule => $subTable)
                        {
                            if($title == $subTable['name'])
                            {
                                $module = $subModule;   // 如果读取的内容是明细表的名称，列对应的流程改为明细表的流程代号。
                            }
                            else
                            {
                                $field = array_search($title, $subTable['fields']);     // 根据读取的内容和明细表表头字段数组获取字段代号。

                                /* 如果获取到字段代号则把列对应的流程改为明细表的流程代号，并中断对明细表表头字段数组的循环。*/
                                if($field)
                                {
                                    $module = $subModule;

                                    break;
                                }
                            }
                        }
                    }
                }

                if($row == 2)
                {
                    if(!isset($columnKey[$column]['module'])) break;    // 列对应的流程应该在第一行已经确定，否则终止后续操作。

                    $module = $columnKey[$column]['module'];

                    if($module == $flow->module)
                    {
                        $field = array_search($title, $titles);     // 根据读取的内容和表头字段数组获取字段代号。
                    }
                    else
                    {
                        $field = array_search($title, $titles['sub_tables'][$module]['fields']);     // 根据读取的内容和明细表表头字段数组获取字段代号。
                    }

                }

                if(!$module && !$field) break;  // 如果获取不到列对应的流程代号和字段代号，终止后续操作。

                if(empty($columnKey[$column]['module'])) $columnKey[$column]['module'] = $module;
                if(empty($columnKey[$column]['field']))  $columnKey[$column]['field']  = $field;
            }

            $column++;
        }

        $dataList      = array();   // 数据集合。
        $dateFields    = array();   // 日期字段集合。
        $defaultValues = array();   // 默认值集合。

        /* 获取日期类型字段和默认值集合。*/
        foreach($fields as $flowFields)
        {
            foreach($flowFields as $field)
            {
                if($field->control == 'date' or $field->control == 'datetime') $dateFields[$field->module][$field->field] = $field->field;

                $defaultValues[$field->module][$field->field] = $field->default;
            }
        }

        /* 遍历数据区域。数据区域的起始行是表头区域的下一行，所以用表头行数加一。*/
        for($row = $titleRows + 1; $row <= $allRows; $row++)    // 遍历行。
        {
            $column = 'A';  // 起始列。
            $key    = $row; // 主数据的键名。
            $subKey = $row; // 明细表数据的键名。

            while($column != $allColumns)   // 遍历列。
            {
                /* 如果在前面没有获取到该列和模块、字段的对应关系，则跳过该列。*/
                if(empty($columnKey[$column]['module']) or empty($columnKey[$column]['field']))
                {
                    $column++;
                    continue;
                }

                $cell   = $column . $row;                   // 当前读取的单元格。
                $module = $columnKey[$column]['module'];    // 列对应的流程。
                $field  = $columnKey[$column]['field'];     // 列对应的字段。

                /* 如果当前读取的单元格被合并到其他单元格做相应处理。*/
                if(isset($mergeCells[$cell]))
                {
                    $mergeCell = $mergeCells[$cell];

                    if($mergeCell->parent != $cell)     // 当前读取的单元格是合并单元格的第一个单元格时不需要处理。
                    {
                        if($module == $flow->module)    // 当前读取的单元格对应的流程是主流程时，需要判断主流程在当前行对应的所有单元格是否都合并单元格，如果都合并则认为当前行和被合并到的单元格所在的行是同一条数据。
                        {
                            $allColumnsMerged = true;
                            foreach($columnKey as $currentColumn => $columnMap) // 遍历列和流程的对应关系。
                            {
                                if($columnMap['module'] != $module) continue;   // 列对应的流程不是主流程，跳过。

                                $currentCell = $currentColumn . $row;

                                /* 有一个单元格没有被合并，则跳出循环。*/
                                if(!isset($mergeCells[$currentCell]))
                                {
                                    $allColumnsMerged = false;
                                    break;
                                }

                                $currentMergeCell = $mergeCells[$currentCell];

                                /* 单元格是合并单元格的第一个单元格时，跳出循环。因为合并单元格的第一个单元格一定是一条独立的数据。*/
                                if($currentMergeCell->parent == $currentCell)
                                {
                                    $allColumnsMerged = false;
                                    break;
                                }
                            }

                            /* 如果当前读取的单元格对应的流程在当前行对应的所有单元格都合并了，把key改成合并后单元格的行号，并跳过后续操作。*/
                            if($allColumnsMerged)
                            {
                                preg_match('|[0-9]+|', $mergeCell->parent, $match); // 获取合并后单元格的行号。

                                $key = $match[0];   // 把key改成合并后单元格的行号。

                                $column++;
                                continue;
                            }
                        }

                        $cell = $mergeCell->parent; // 把当前读取的单元格改成合并后的单元格。
                    }
                }

                $value  = $sheet->getCell($cell)->getValue();   // 获取单元格内容。
                $value  = trim($value);

                if(empty($value)) $value = '';  // 避免显示0，0.0等类似数据。

                /* 处理日期类型的字段值。*/
                if(is_numeric($value) && isset($dateFields[$module][$field]))
                {
                    $value = date(DT_DATE1, PHPExcel_Shared_Date::ExcelToPHP($value));
                }

                if($module == $flow->module)
                {
                    $dataList[$key][$field] = $value;  // 记录主流程数据。
                }
                else
                {
                    $dataList[$key]['sub_tables'][$module][$subKey][$field] = $value;  // 记录明细表数据。
                }

                $column++;
            }
        }

        $dataList = $this->processDefaultValue($flow, $titles, $dataList, $defaultValues);
        $dataList = $this->processEmptyData($dataList);
        $fields   = $this->processImportFields($fields, $dataList);

        foreach($dataList as $key => $data)
        {
            foreach($data as $field => $value)
            {
                if($field == 'sub_tables')
                {
                    foreach($value as $subModule => $subDatas)
                    {
                        foreach($subDatas as $subKey => $subData)
                        {
                            foreach($subData as $subField => $subValue)
                            {
                                if(!isset($fields[$subModule][$subField])) continue;

                                $subField = $fields[$subModule][$subField];

                                if(!is_array($subField->options) or !$subField->options) continue;

                                /* 先把数据的导入值和字段选项数组的键匹配，如果匹配到了则取键作为数据的值，否则和字段选项数组的值匹配，如果匹配到了则取值对应的键作为数据的值，如还未匹配到则直接应用数据的导入值。 */
                                $subValue = isset($subField->options[$subValue]) ? $subValue : (in_array($subValue, $subField->options) ? array_search($subValue, $subField->options) : $subValue);

                                $dataList[$key][$field][$subModule][$subKey][$subField->field] = $subValue;
                            }
                        }
                    }
                }
                else
                {
                    if(!isset($fields[$flow->module][$field])) continue;

                    $field = $fields[$flow->module][$field];

                    if(!is_array($field->options) or !$field->options) continue;

                    /* 先把数据的导入值和字段选项数组的键匹配，如果匹配到了则取键作为数据的值，否则和字段选项数组的值匹配，如果匹配到了则取值对应的键作为数据的值，如还未匹配到则直接应用数据的导入值。 */
                    $value = isset($field->options[$value]) ? $value : (in_array($value, $field->options) ? array_search($value, $field->options) : $value);

                    $dataList[$key][$field->field] = $value;
                }
            }
        }

        return array($fields, $dataList);
    }

    /**
     * Check the import data and set default value of the empty property.
     *
     * @param  object $flow
     * @param  array  $titles
     * @param  array  $dataList
     * @param  array  $defaultValues
     * @access public
     * @return array
     */
    public function processDefaultValue($flow, $titles, $dataList, $defaultValues)
    {
        /* 检查是否有导入设置中的字段没有被赋值，如有则赋值为该字段的默认值。*/
        foreach($dataList as $key => $data)
        {
            foreach($titles as $field => $fieldName)
            {
                if($field == 'sub_tables')
                {
                    foreach($fieldName as $subModule => $subTable)
                    {
                        if(empty($subTable['fields'])) continue;
                        foreach($subTable['fields'] as $subField => $subFieldName)
                        {
                            if(!isset($data['sub_tables'])) continue;

                            foreach($data['sub_tables'][$subModule] as $subKey => $subData)
                            {
                                if(!isset($subData[$subField])) $dataList[$key]['sub_tables'][$subModule][$subKey][$subField] = $defaultValues[$subModule][$subField];
                            }
                        }
                    }
                }
                else
                {
                    if(!isset($data[$field])) $dataList[$key][$field] = $defaultValues[$flow->module][$field];
                }
            }
        }

        return $dataList;
    }

    /**
     * Check the import data and remove the empty one.
     *
     * @param  array  $dataList
     * @access public
     * @return array
     */
    public function processEmptyData($dataList)
    {
        /* 检查是否有整条数据都为空的情况，如有则删除该条数据。*/
        foreach($dataList as $key => $data)
        {
            $emptyData = true;

            foreach($data as $field => $value)
            {
                if($field == 'sub_tables')
                {
                    foreach($value as $subModule => $subDatas)
                    {
                        foreach($subDatas as $subKey => $subData)
                        {
                            foreach($subData as $subField => $subValue)
                            {
                                if($subValue)
                                {
                                    $emptyData = false;
                                    break;  // 逐层跳出循环，提高检查性能。
                                }
                            }

                            if(!$emptyData) break;  // 逐层跳出循环，提高检查性能。
                        }

                        if(!$emptyData) break;  // 逐层跳出循环，提高检查性能。
                    }
                }
                else
                {
                    if($value) $emptyData = false;
                }

                if(!$emptyData) break;  // 逐层跳出循环，提高检查性能。
            }

            if($emptyData) unset($dataList[$key]);
        }

        return $dataList;
    }

    /**
     * Process the fields of imported data.
     *
     * @param  array  $fields
     * @param  array  $dataList
     * @access public
     * @return array
     */
    public function processImportFields($fields, $dataList)
    {
        /* Construct the data array to get real options of fields. */
        $subDataList = array();
        foreach($dataList as $key => $data)
        {
            foreach($data as $field => $value)
            {
                if($field == 'sub_tables')
                {
                    foreach($value as $subModule => $subDatas)
                    {
                        if(isset($subDataList[$subModule]))
                        {
                            $subDataList[$subModule] = array_merge($subDataList[$subModule], $subDatas);
                        }
                        else
                        {
                            $subDataList[$subModule] = $subDatas;
                        }
                    }
                }
            }
        }

        /* Get the real options of fields. */
        $this->loadModel('workflowaction', 'flow');
        foreach($fields as $module => $moduleFields) $moduleFields = $this->workflowaction->processFields($moduleFields, true, zget($subDataList, $module, array()));

        return $fields;
    }

    /**
     * Process the post data.
     *
     * @param  objetc $action
     * @param  array  $fields
     * @param  string $editorFields
     * @param  object $data
     * @access public
     * @return object
     */
    public function processPostData($action, $fields, $editorFields, $data)
    {
        if($action->open != 'none')
        {
            /* Reset the show property of a field according by the linkages. */
            /* 根据界面联动设置重置字段的是否显示属性。防止界面联动把radio或者checkbox隐藏后，后面的代码又对其赋值而导致的错误。*/
            $linkages = $action->linkages;
            foreach($linkages as $linkage)
            {
                $sources = zget($linkage, 'sources', array());
                $targets = zget($linkage, 'targets', array());

                if(!$linkage or !$sources or !$targets) continue;

                foreach($sources as $source)
                {
                    if(!isset($fields[$source->field])) continue;
                    if(!isset($data->{$source->field})) continue;

                    $dataValue = $data->{$source->field};
                    $condition = false;

                    if($source->operator == '==') $condition = $dataValue == $source->value;
                    if($source->operator == '!=') $condition = $dataValue != $source->value;
                    if($source->operator == '>')  $condition = $dataValue >  $source->value;
                    if($source->operator == '>=') $condition = $dataValue >= $source->value;
                    if($source->operator == '<')  $condition = $dataValue <  $source->value;
                    if($source->operator == '<=') $condition = $dataValue <= $source->value;

                    foreach($targets as $target)
                    {
                        if(!isset($fields[$target->field])) continue;

                        $targetField = $fields[$target->field];

                        if($condition)
                        {
                            if($target->status == 'show') $targetField->show = 1;
                            if($target->status == 'hide') $targetField->show = 0;
                        }
                        else
                        {
                            if($target->status == 'show') $targetField->show = 0;
                            if($target->status == 'hide') $targetField->show = 1;
                        }

                        $fields[$target->field] = $targetField;
                    }
                }
            }

            foreach($fields as $field)
            {
                if($field->control == 'formula' and $field->expression and !isset($data->{$field->field}))
                {
                    $field->expression = json_decode($field->expression);

                    $expression = '';
                    foreach($field->expression as $item)
                    {
                        if($item->type == 'target' and !isset($data->{$item->field}))
                        {
                            $expression = '';
                            break;
                        }
                        if($item->type == 'target')   $expression .= $data->{$item->field};
                        if($item->type == 'operator') $expression .= $item->operator;
                        if($item->type == 'number')   $expression .= $item->value;
                    }

                    $fieldName = $field->field;
                    $data->$fieldName = eval("return $expression;");
                }

                if(!$field->show or $field->readonly) continue;

                if(isset($data->{$field->field}))
                {
                    if($field->options && is_string($field->options) && strpos(',user,dept,', ",$field->options,") !== false)
                    {
                        if(is_array($data->{$field->field}))
                        {
                            foreach($data->{$field->field} as $key => $value)
                            {
                                $data->{$field->field}[$key] = $this->workflowhook->getParamRealValue($value);
                            }
                        }
                        else
                        {
                            $data->{$field->field} = $this->workflowhook->getParamRealValue($data->{$field->field});
                        }
                    }

                    /* If data value is array, implode by comma. */
                    if(is_array($data->{$field->field}))
                    {
                        $dataValue = array_values(array_unique(array_filter($data->{$field->field})));
                        asort($dataValue);
                        $data->{$field->field} = implode(',', $dataValue);
                    }

                    if(empty($data->{$field->field})) $data->{$field->field} = in_array($field->type, $this->config->workflowfield->numberTypes) ? '0' : '';
                }
                else
                {
                    if(strpos(',radio,checkbox,multi-select,', ",$field->control,") !== false) $data->{$field->field} = '';
                }
            }
        }

        $skip = 'uid';
        foreach($data as $field => $value)
        {
            /* If the field don't show in view, skip it. */
            if(empty($fields[$field]->show) && !$value)
            {
                unset($data->$field);
                $skip .= ',' . $field;
            }
            else
            {
                $data->$field = $value;
            }

            if(in_array($value, $this->config->flow->variables)) $data->$field = $this->workflowhook->getParamRealValue($value);
        }

        if($editorFields) $data = $this->file->processImgURL($data, $editorFields, $this->post->uid);

        return array($data, $skip);
    }

    /**
     * Process data from db.
     *
     * @param  string $module
     * @param  array  $data
     * @param  bool   $decode
     * @access public
     * @return object
     */
    public function processDBData($module, $data, $decode = true)
    {
        static $fields = array();
        if(empty($fields[$module])) $fields[$module] = $this->loadModel('workflowfield', 'flow')->getControlPairs($module);

        $editorFields = array();
        $this->loadModel('file');

        foreach($fields[$module] as $field => $control)
        {
            if($decode && $control == 'multi-select' or $control == 'checkbox') $data->$field = explode(',', $data->$field);
            if($control == 'date' or $control == 'datetime') $data->$field = formatTime($data->$field);
            if($control == 'richtext') $editorFields[] = $field;
        }
        if($editorFields) $data = $this->file->replaceImgURL($data, $editorFields);

        return $data;
    }

    /**
     * Remove the field which will not be exported to the excel and process the value of field to real.
     *
     * @param  array  $datas
     * @param  array  $fields
     * @param  array  $exportFields
     * @access public
     * @return array
     */
    public function processExportDatas($datas, $fields, $exportFields)
    {
        $fields = $this->loadModel('workflowaction', 'flow')->processFields($fields, true, $datas);
        foreach($datas as $data)
        {
            foreach($data as $key => $value)
            {
                if(!isset($exportFields[$key]))
                {
                    unset($data->$key);
                    continue;
                }

                if(!isset($fields[$key])) continue;

                $field = $fields[$key];
                if(!is_array($field->options)) continue;

                if($field->control == 'multi-select' or $field->control == 'checkbox')
                {
                    $values = explode(',', $value);
                    foreach($values as $k => $v)
                    {
                        $values[$k] = zget($field->options, $v);
                    }
                    $data->$key = implode(',', array_unique(array_filter($values)));
                }
                else
                {
                    $data->$key = zget($field->options, $value);
                }
            }
        }

        return $datas;
    }

    /**
     * Process merge cells.
     * 处理合并单元格的数组，把其中每一个单元格的相关信息解析出来。
     *
     * @param  array  $mergeCells
     * @access public
     * @return array
     */
    public function processMergeCells($mergeCells)
    {
        $cells = array();

        foreach($mergeCells as $key => $mergeCell)
        {
            preg_match_all('|([A-Z]+)([0-9]+)|', $mergeCell, $matches);

            $beginColumn = $matches[1][0];  // 起始列。
            $beginRow    = $matches[2][0];  // 起始行。

            $endColumn = $matches[1][1];    // 终止列。
            $endRow    = $matches[2][1];    // 终止行。

            $parent  = $matches[0][0];  // 合并单元格中的第一个单元格。
            $colspan = ord($endColumn) - ord($beginColumn) + 1; // 跨列数。
            $rowspan = $endRow - $beginRow + 1;                 // 跨行数。

            $index = 1;
            $endColumn++;

            /* 遍历合并单元格的区域，解析相关数据。*/
            while($beginColumn != $endColumn)   // 遍历列。
            {
                for($row = $beginRow; $row <= $endRow; $row++)  // 遍历行。
                {
                    $cell = new stdclass();
                    $cell->cell    = $beginColumn . $row;
                    $cell->parent  = $parent;                   // 被合并到哪个单元格。
                    $cell->colspan = $index == 1 ? $colspan : 0;
                    $cell->rowspan = $index == 1 ? $rowspan : 0;

                    $cells[$cell->cell] = $cell;

                    $index++;
                }

                $beginColumn++;
            }

        }

        return $cells;
    }

    /**
     * Post data of a flow.
     *
     * @param  object $flow
     * @param  object $action
     * @param  int    $dataID
     * @param  string $prevModule
     * @access public
     * @return array
     */
    public function post($flow, $action, $dataID = 0, $prevModule = '')
    {
        $this->loadModel('action');
        $this->loadModel('file');
        $this->loadModel('workflow', 'flow');
        $this->loadModel('workflowaction', 'flow');
        $this->loadModel('workflowfield', 'flow');
        $this->loadModel('workflowhook', 'flow');
        $this->loadModel('workflowrule', 'flow');

        $fields = $this->workflowaction->getFields($flow->module, $action->action, false);

        /* Get editor fields. */
        $editorFields = array();
        foreach($fields as $field)
        {
            if($field->control == 'richtext') $editorFields[] = $field->field;
        }
        $editorFields = implode(',', $editorFields);

        /* Get data from post by fixer.*/
        $data = fixer::input('post')
            ->stripTags($editorFields, $this->config->allowedTags)
            ->remove('file,children')
            ->removeIF($action->position == 'menu', 'dataID')
            ->get();

        /* Remove files and labels field when uploading files. */
        foreach($data as $fieldName => $fieldValue)
        {
            if((strpos($fieldName, 'files') !== false) or (strpos($fieldName, 'labels') !== false)) unset($data->$fieldName);
        }

        foreach($fields as $field)
        {
            /* 检查附件是否为空 */
            if($field->control == 'file' && isset($field->layoutRules))
            {
                $rules = explode(',', trim($field->layoutRules, ','));
                $rules = array_unique($rules);
                foreach($rules as $rule)
                {
                    if(empty($rule)) continue;
                    $rule = $this->workflowrule->getByID($rule);

                    /* 附件比较特殊，只检查必填项，其他规则不检查，即使用户设置了。*/
                    if($rule->rule == 'notempty')
                    {
                        $files = $this->file->getUpload("files{$field->field}", "labels{$field->field}");
                        if(empty($files)) dao::$errors[$field->field][] = sprintf($this->lang->flow->filesNotEmpty, $field->name);
                    }
                }
            }
        }

        $result = $this->operate($flow, $action, $fields, $editorFields, $data, $dataID);
        if(dao::isError()) return array('result' => 'fail', 'message' => dao::getError());

        if(isset($result['result']) && $result['result'] == 'fail') return $result;

        if($action->action == 'create')
        {
            $dataID = $result['dataID'];
            $data   = $this->getDataByID($flow, $dataID);
            if(!$data)
            {
                $message = !empty($result['message']) ? $result['message'] : $this->lang->fail;
                return array('result' => 'fail', 'message' => $message);
            }
        }

        /* Link the created datas. */
        if($action->action == 'create' && $prevModule)
        {
            $relation = $this->loadModel('workflowrelation', 'flow')->getByPrevAndNext($prevModule, $flow->module);
            if($relation && strpos(",$relation->actions,", ',many2one') !== false)
            {
                $unlinkedIDList = $data->{$relation->field};
                /* If the relation field is posted in a hidden input, it had been encoded. */
                if(!is_array($unlinkedIDList)) $unlinkedIDList = explode(',', $unlinkedIDList);

                if($unlinkedIDList) $this->link($flow, $dataID, $prevModule, $unlinkedIDList);
            }
        }

        $this->file->updateObjectID($this->post->uid, $dataID, $flow->module);

        $allFiles = array();
        foreach($fields as $field)
        {
            if($field->control == 'file')
            {
                $files = $this->file->saveUpload($flow->module, $dataID, $field->field, "files{$field->field}", "labels{$field->field}");
                if($files) $allFiles += $files;
            }
        }
        $fileAction = $allFiles ? $this->lang->addFiles . join(',', $allFiles) : '';

        /* Create action. */
        $actionID = 0;
        if($action->action == 'create') $actionID = $this->action->create($flow->module, $dataID, $action->action, $fileAction);
        if($action->action != 'create' && (!empty($result['changes']) or $fileAction))
        {
            $actionID = $this->action->create($flow->module, $dataID, $action->action, $fileAction);
            $this->action->logHistory($actionID, $result['changes']);
        }

        $message = !empty($result['message']) ? $result['message'] : $this->lang->saveSuccess;
        if($action->open == 'none') return array('result' => 'success', 'recordID' => $dataID, 'actionID' => $actionID);

        $locate = helper::createLink($flow->module, 'browse');
        return array('result' => 'success', 'message' => $message, 'recordID' => $dataID, 'actionID' => $actionID, 'locate' => $locate);
    }

    /**
     * Post data of a flow.
     *
     * @param  object $flow
     * @param  object $action
     * @param  string $method   The method which called this function.
     * @access public
     * @return array
     */
    public function batchPost($flow, $action, $method = '')
    {
        $this->loadModel('action');
        $this->loadModel('file');
        $this->loadModel('workflow', 'flow');
        $this->loadModel('workflowaction', 'flow');
        $this->loadModel('workflowfield', 'flow');
        $this->loadModel('workflowhook', 'flow');
        $this->loadModel('workflowrule', 'flow');

        $errorList  = array();
        $recordList = array();
        $actionList = array();
        $fields     = $this->workflowaction->getFields($flow->module, $action->action, false);
        $dataList   = $this->getPostData($flow, $action);

        foreach($dataList as $key => $data)
        {
            $data   = (object)$data;
            $dataID = $action->action == 'batchcreate' ? 0 : $key;

            $result = $this->operate($flow, $action, $fields, '', $data, $dataID);
            if(dao::isError())
            {
                $daoErrors = dao::getError();
                if(is_string($daoErrors)) $errorList['error' . $key] = $daoErrors;
                if(is_array($daoErrors))
                {
                    foreach($daoErrors as $field => $message)
                    {
                        /* Set error key. */
                        $errorKey = '';
                        if($action->batchMode == 'same')      $errorKey = 'data' . $field;
                        if($action->batchMode == 'different') $errorKey = 'dataList' . $key . $field;

                        $errorList[$errorKey] = $message;
                    }
                }
                continue;
            }

            if(isset($result['result']) && $result['result'] == 'fail' && isset($result['message']))
            {
                $errorList['error' . $key] = $result['message'];
                continue;
            }

            if($action->action == 'batchcreate')
            {
                $dataID = $result['dataID'];
                $data   = $this->getDataByID($flow, $dataID);
                if(!$data)
                {
                    $message = !empty($result['message']) ? $result['message'] : $this->lang->fail;
                    $errorList['error' . $key] = $message;
                    continue;
                }
            }

            /* Create action. */
            $actionID = $this->action->create($flow->module, $dataID, $method ? $method : $action->action);
            if($action->action != 'batchcreate' && !empty($result['changes']))
            {
                $this->action->logHistory($actionID, $result['changes']);
            }

            $recordList[$key] = $dataID;
            $actionList[$key] = $actionID;
        }

        if($errorList)
        {
            /* If there are errors, delete the created datas. */
            if($action->action == 'batchcreate')
            {
                /* Didn't delete the sub tables. */
                if($recordList) $this->dao->delete()->from($flow->table)->where('id')->in($recordList)->exec();
                if($actionList) $this->dao->delete()->from(TABLE_ACTION)->where('id')->in($actionList)->exec();
                if($actionList) $this->dao->delete()->from(TABLE_HISTORY)->where('action')->in($actionList)->exec();
            }

            return array('result' => 'fail', 'message' => $errorList);
        }

        if($action->open == 'none') return array('result' => 'success', 'recordList' => $recordList, 'actionList' => $actionList);

        $message = !empty($result['message']) ? $result['message'] : $this->lang->saveSuccess;
        $locate  = helper::createLink($flow->module, 'browse');
        return array('result' => 'success', 'message' => $message, 'recordList' => $recordList, 'actionList' => $actionList, 'locate' => $locate);
    }

    /**
     * Operate.
     *
     * @param  object $flow
     * @param  object $action
     * @param  array  $fields
     * @param  string $editorFields
     * @param  object $data
     * @param  int    $dataID
     * @access public
     * @return void
     */
    public function operate($flow, $action, $fields, $editorFields, $data, $dataID = 0)
    {
        if($action->position == 'menu' && $action->action != 'create' && $action->action != 'batchcreate' && $this->post->dataID == '')
        {
            dao::$errors['dataID'] = sprintf($this->lang->error->notempty, $flow->name);
            return false;
        }

        list($data, $skip) = $this->processPostData($action, $fields, $editorFields, $data);

        if($action->action == 'create' or $action->action == 'batchcreate')
        {
            return $this->create($flow, $action, $fields, $data, $skip);
        }
        else
        {
            return $this->update($flow, $action, $fields, $data, $skip, $dataID);
        }
    }

    /**
     * Create a record of a flow.
     *
     * @param  object $flow
     * @param  object $action
     * @param  array  $fields
     * @param  object $data
     * @param  string $skip
     * @access public
     * @return bool | array
     */
    public function create($flow, $action, $fields, $data, $skip)
    {
        $createdBy   = zget($this->config->flow->defaultFields->createdBy,   $flow->module, 'createdBy');
        $createdDate = zget($this->config->flow->defaultFields->createdDate, $flow->module, 'createdDate');

        if($createdBy)   $data->$createdBy   = $this->app->user->account;
        if($createdDate) $data->$createdDate = helper::now();

        if(!empty($action->verifications))
        {
            $canExec = true;
            if($action->verifications->type == 'sql')
            {
                $sql = $action->verifications->sql;
                foreach($action->verifications->sqlVars as $sqlVar)
                {
                    if($sqlVar->paramType == 'form')
                    {
                        if(!isset($data->{$sqlVar->param}))
                        {
                            $canExec = false;
                            break;
                        }
                        $sqlVar->param = $data->{$sqlVar->param};
                    }
                    elseif(!empty($sqlVar->paramType) && strpos(',today,now,actor,deptManager,', ",$sqlVar->paramType,") !== false)
                    {
                        $sqlVar->param = $this->workflowhook->getParamRealValue($sqlVar->paramType);
                    }
                    $sql = str_replace("'$" . $sqlVar->varName . "'", $this->dbh->quote($sqlVar->param), $sql);
                }
                $sql = $this->workflowfield->replaceTableNames($sql);

                try
                {
                    $sqlResult = $this->dbh->query($sql)->fetch();
                    if($action->verifications->sqlResult == 'empty')
                    {
                        $canExec = empty($sqlResult);
                    }
                    elseif($action->verifications->sqlResult == 'notempty')
                    {
                        $canExec = !empty($sqlResult);
                    }
                }
                catch(PDOException $exception)
                {
                    $canExec = false;
                }
            }
            elseif($action->verifications->type == 'data')
            {
                unset($canExec);
                foreach($action->verifications->fields as $verification)
                {
                    if(!$verification->field || !$verification->operator) continue;

                    /* 数据校验中的字段数据源不包含表单数据和当前数据，仅需要判断下列3个类型。 */
                    /* The datasource of field in verifycation doesn't contain form data and current data. Just process the flow 3 types.*/
                    if(!empty($verification->paramType) && strpos(',today,now,actor,deptManager,', ",$verification->paramType,") !== false)
                    {
                        $verification->param = $this->workflowhook->getParamRealValue($verification->paramType);
                    }

                    $checkFunc = 'check' . $verification->operator;
                    $curResult = isset($data->{$verification->field}) ? validater::$checkFunc($data->{$verification->field}, $verification->param) : false;

                    if(!isset($canExec)) $canExec = !$curResult;
                    $canExec = $verification->logicalOperator == 'and' ? $canExec && !$curResult : $canExec || !$curResult;
                }
                if(!isset($canExec)) $canExec = true;
            }
            if(!$canExec) return array('result' => 'fail', 'message' => !empty($action->verifications->message) ? $action->verifications->message : $this->lang->fail);
        }

        $dao = $this->dao->insert("`$flow->table`")->data($data, $skip);
        $dao = $this->checkRules($fields, $data, $dao);
        $dao->autoCheck()->exec();
        if(dao::isError()) return false;

        $dataID = $this->dao->lastInsertID();

        /* Update the children data. */
        if($this->post->children)
        {
            $postData = fixer::input('post')->get();
            $errors   = $this->updateChildrenData($flow, $action, $postData->children, $dataID);
            if($errors)
            {
                $this->dao->delete()->from($flow->table)->where('id')->eq($dataID)->exec();

                dao::$errors = $errors;
                return false;
            }
        }

        $message = $this->workflowhook->execute($flow, $action, $dataID);
        if(dao::isError()) return false;

        return array('dataID' => $dataID, 'message' => $message);
    }

    /**
     * Update a record of a flow.
     *
     * @param  object $flow
     * @param  object $action
     * @param  array  $fields
     * @param  object $data
     * @param  string $skip
     * @param  int    $dataID
     * @access public
     * @return bool | array
     */
    public function update($flow, $action, $fields, $data, $skip, $dataID)
    {
        $oldData = $this->getDataByID($flow, $dataID, $decode = false);
        $account = $this->app->user->account;
        $now     = helper::now();

        $editedBy   = zget($this->config->flow->defaultFields->editedBy,   $flow->module, 'editedBy');
        $editedDate = zget($this->config->flow->defaultFields->editedDate, $flow->module, 'editedDate');

        if($editedBy)   $data->$editedBy   = $account;
        if($editedDate) $data->$editedDate = $now;
        $data->id = $oldData->id;

        /* Auto save who and when did the action. */
        if($action->action != 'edit' && $action->action != 'batchedit')
        {
            $actionFields = $this->workflowfield->getList($flow->module);

            $actionCode = $action->action;
            if($action->action == 'assign' or $action->action == 'batchassign') $actionCode = 'assigned';

            foreach($actionFields as $actionField)
            {
                if(strtolower($actionField->field) == strtolower($actionCode . 'By'))   $data->{$actionField->field} = $account;
                if(strtolower($actionField->field) == strtolower($actionCode . 'Date')) $data->{$actionField->field} = $now;
            }
        }

        if(!empty($action->verifications))
        {
            $canExec = true;
            if($action->verifications->type == 'sql')
            {
                $sql = $action->verifications->sql;
                foreach($action->verifications->sqlVars as $sqlVar)
                {
                    if($sqlVar->paramType == 'form')
                    {
                        if(!isset($data->{$sqlVar->param}))
                        {
                            $canExec = false;
                            break;
                        }
                        $sqlVar->param = $data->{$sqlVar->param};
                    }
                    elseif($sqlVar->paramType == 'record')
                    {
                        if(!isset($oldData->{$sqlVar->param}))
                        {
                            $canExec = false;
                            break;
                        }
                        $sqlVar->param = $oldData->{$sqlVar->param};
                    }
                    elseif(!empty($sqlVar->paramType) && strpos(',today,now,actor,deptManager,', ",$sqlVar->paramType,") !== false)
                    {
                        $sqlVar->param = $this->workflowhook->getParamRealValue($sqlVar->paramType);
                    }
                    $sql = str_replace("'$" . $sqlVar->varName . "'", $this->dbh->quote($sqlVar->param), $sql);
                }
                $sql = $this->workflowfield->replaceTableNames($sql);

                try
                {
                    $sqlResult = $this->dbh->query($sql)->fetch();
                    if($action->verifications->sqlResult == 'empty')
                    {
                        $canExec = empty($sqlResult);
                    }
                    elseif($action->verifications->sqlResult == 'notempty')
                    {
                        $canExec = !empty($sqlResult);
                    }
                }
                catch(PDOException $exception)
                {
                    $canExec = false;
                }
            }
            elseif($action->verifications->type == 'data')
            {
                unset($canExec);
                foreach($action->verifications->fields as $verification)
                {
                    if(!$verification->field || !$verification->operator) continue;

                    /* 数据校验中的字段数据源不包含表单数据和当前数据，仅需要判断下列3个类型。 */
                    /* The datasource of field in verifycation doesn't contain form data and current data. Just process the flow 3 types.*/
                    if(!empty($verification->paramType) && strpos(',today,now,actor,deptManager,', ",$verification->paramType,") !== false)
                    {
                        $verification->param = $this->workflowhook->getParamRealValue($verification->paramType);
                    }

                    $value = isset($data->{$verification->field}) ? $data->{$verification->field} : $oldData->{$verification->field};

                    $checkFunc = 'check' . $verification->operator;
                    $curResult = validater::$checkFunc($value, $verification->param);

                    if(!isset($canExec)) $canExec = !$curResult;
                    $canExec = $verification->logicalOperator == 'and' ? $canExec && !$curResult : $canExec || !$curResult;
                }
                if(!isset($canExec)) $canExec = true;
            }
            if(!$canExec) return array('result' => 'fail', 'message' => !empty($action->verifications->message) ? $action->verifications->message : $this->lang->fail);
        }

        $originData = $this->dao->select('*')->from($flow->table)->where('id')->eq($dataID)->fetch();

        $dao = $this->dao->update("`$flow->table`")->data($data, $skip);
        $dao = $this->checkRules($fields, $data, $dao, $dataID);
        $dao->where('id')->eq($dataID)->autoCheck()->exec();
        if(dao::isError()) return false;

        /* Update the children data. */
        if($this->post->children)
        {
            $postData = fixer::input('post')->get();
            $errors   = $this->updateChildrenData($flow, $action, $postData->children, $dataID);
            if($errors)
            {
                $this->dao->update($flow->table)->data($originData)->where('id')->eq($dataID)->exec();

                dao::$errors = $errors;
                return false;
            }
        }

        $message = $this->workflowhook->execute($flow, $action, $dataID);
        if(dao::isError()) return false;

        $data = $this->getDataByID($flow, $dataID, $decode = false);
        if(!$data) return true;

        return array('changes' => commonModel::createChanges($oldData, $data), 'message' => $message);
    }

    /**
     * Update children's data.
     *
     * @param  object $flow
     * @param  object $action
     * @param  array  $children
     * @param  int    $parentID
     * @access public
     * @return array
     */
    public function updateChildrenData($flow, $action, $children = array(), $parentID = 0)
    {
        $errors = array();
        foreach($children as $childModule => $child)
        {
            $childModule = str_replace('sub_', '', $childModule);

            $flow   = $this->workflow->getByModule($childModule);
            $fields = $this->workflowaction->getFields($flow->module, $action->action);

            $datas  = array();
            if($action->action != 'create')
            {
                $datas = $this->dao->select('*')->from($flow->table)
                    ->where('deleted')->eq('0')
                    ->beginIF(!$flow->buildin && $parentID)->andWhere('parent')->eq($parentID)->fi()
                    ->fetchAll('id');
            }

            $createIDList = array();
            $updateIDList = array();
            foreach($child['id'] as $key => $id)
            {
                $dataIsEmpty = true;

                /* Get post data for each field. */
                $data = new stdclass();
                foreach($fields as $field)
                {
                    if(empty($child[$field->field])) continue;
                    if(!isset($child[$field->field][$key])) continue;

                    $data->{$field->field} = $child[$field->field][$key];

                    if(is_array($data->{$field->field})) $data->{$field->field} = implode(',', array_values(array_unique(array_filter($data->{$field->field}))));

                    if(isset($this->config->workflowfield->typeList['integer'][$field->type])) $data->{$field->field} = (int)$data->{$field->field};
                    if(isset($this->config->workflowfield->typeList['decimal'][$field->type])) $data->{$field->field} = (float)$data->{$field->field};

                    if($data->{$field->field}) $dataIsEmpty = false;
                }
                if($dataIsEmpty) continue;

                $data->parent = $parentID;

                if($data->id)
                {
                    /* 更新数据。*/
                    $oldData = zget($datas, $data->id, null);
                    if(!$oldData) continue;

                    $dao = $this->dao->update($flow->table)->data($data);

                    $dao = $this->checkRules($fields, $data, $dao);

                    $dao->where('id')->eq($data->id)->autoCheck()->exec();

                    if(!dao::isError())
                    {
                        $changes = commonModel::createChanges($oldData, $data);

                        if($changes)
                        {
                            $actionID = $this->action->create($flow->module, $data->id, 'edited');
                            $this->action->logHistory($actionID, $changes);
                        }

                        $updateIDList[] = $data->id;
                    }
                }
                else
                {
                    unset($data->id);

                    /* 插入数据。*/
                    $dao = $this->dao->insert($flow->table)->data($data);
                    $dao = $this->checkRules($fields, $data, $dao);
                    $dao->autoCheck()->exec();

                    if(!dao::isError())
                    {
                        $dataID = $this->dao->lastInsertId();

                        $this->action->create($flow->module, $dataID, 'created');

                        $createIDList[] = $dataID;
                    }
                }

                if(dao::isError())
                {
                    $daoErrors = dao::getError();

                    if(is_string($daoErrors)) continue;
                    if(is_array($daoErrors))
                    {
                        foreach($daoErrors as $field => $messages)
                        {
                            /* Set error key. */
                            $errorKey = 'childrensub_' . $childModule . $field . $key;

                            $errors[$errorKey] = $messages;
                        }
                    }
                }
            }

            if(!$createIDList && !$updateIDList) continue;

            if($errors)
            {
                /* Delete the new data. */
                if($createIDList)
                {
                    $this->dao->delete()->from($flow->table)
                        ->where('parent')->eq($parentID)
                        ->andWhere('id')->in($createIDList)
                        ->exec();
                }
                /* Restore the updated data. */
                if($updateIDList)
                {
                    foreach($updateIDList as $dataID)
                    {
                        $oldData = zget($datas, $dataID, null);
                        if(!$oldData) continue;

                        $this->dao->update($flow->table)->data($oldData)->where('id')->eq($dataID)->exec();
                    }
                }
            }
            else
            {
                /* Delete other data. */
                if($datas && $updateIDList)
                {
                    $deleteIDList = array_diff(array_keys($datas), $updateIDList);

                    if($deleteIDList)
                    {
                        $this->dao->update($flow->table)->set('deleted')->eq('1')
                            ->where('parent')->eq($parentID)
                            ->andWhere('id')->in($deleteIDList)
                            ->exec();

                        foreach($deleteIDList as $dataID) $this->action->create($flow->module, $dataID, 'deleted');
                    }
                }
            }
        }

        return $errors;
    }

    /**
     * Link datas to a flow.
     *
     * @param  object $flow
     * @param  int    $dataID
     * @param  string $linkType
     * @param  array  $unlinkedIDList
     * @access public
     * @return bool
     */
    public function link($flow, $dataID, $linkType, $unlinkedIDList)
    {
        if(!$unlinkedIDList) return true;

        $this->loadModel('action');

        $flowDatas  = $this->getDataPairs($flow, $dataID);
        $linkedFlow = $this->loadModel('workflow', 'flow')->getByModule($linkType);
        if($linkedFlow)
        {
            $linkedApp    = $linkedFlow->app;
            $linkedModule = $linkedFlow->module;
            $linkedName   = $linkedFlow->name;
            $linkedDatas  = $this->getDataPairs($linkedFlow, $unlinkedIDList);
        }
        else if(in_array($linkType, $this->config->flow->linkPairs))
        {
            list($linkedApp, $linkedModule) = $this->extractAppAndModule($linkType);

            $linkedDatas = $this->loadModel($linkedModule, $linkedApp)->getPairsByIDList($unlinkedIDList);
            $linkedName  = $this->lang->$linkedModule->common;
        }

        $data = new stdclass();
        $data->objectType  = $flow->module;
        $data->objectID    = $dataID;
        $data->linkedType  = $linkType;
        $data->createdBy   = $this->app->user->account;
        $data->createdDate = helper::now();

        foreach($unlinkedIDList as $unlinkedID)
        {
            $data->linkedID = $unlinkedID;

            $this->dao->replace(TABLE_WORKFLOWLINKDATA)->data($data)->exec();

            $extra = array("{$linkedApp}.{$linkedModule}", 'view', "dataID=$unlinkedID", $linkedName . ' ' . zget($linkedDatas, $unlinkedID));
            $this->action->create($flow->module, $dataID, 'linked', '', $extra);

            $extra = array("{$flow->app}.{$flow->module}", 'view', "dataID=$dataID", $flow->name . ' ' . zget($flowDatas, $dataID));
            $this->action->create($linkType, $unlinkedID, 'linkedto', '', $extra);
        }

        return !dao::isError();
    }

    /**
     * Unlink datas from a flow.
     *
     * @param  object $flow
     * @param  int    $dataID
     * @param  string $linkType
     * @param  array  $linkedIDList
     * @access public
     * @return bool
     */
    public function unlink($flow, $dataID, $linkType, $linkedIDList)
    {
        if(!$linkedIDList) return true;

        $this->loadModel('action');

        $this->dao->delete()->from(TABLE_WORKFLOWLINKDATA)
            ->where('objectType')->eq($flow->module)
            ->andWhere('objectID')->eq($dataID)
            ->andWhere('linkedType')->eq($linkType)
            ->andWhere('linkedID')->in($linkedIDList)
            ->exec();

        $flowDatas  = $this->getDataPairs($flow, $dataID);
        $linkedFlow = $this->loadModel('workflow', 'flow')->getByModule($linkType);
        if($linkedFlow)
        {
            $linkedApp    = $linkedFlow->app;
            $linkedModule = $linkedFlow->module;
            $linkedName   = $linkedFlow->name;
            $linkedDatas  = $this->getDataPairs($linkedFlow, $linkedIDList);
        }
        else if(in_array($linkType, $this->config->flow->linkPairs))
        {
            list($linkedApp, $linkedModule) = $this->extractAppAndModule($linkType);

            $linkedDatas = $this->loadModel($linkedModule, $linkedApp)->getPairsByIDList($linkedIDList);
            $linkedName  = $this->lang->$linkedModule->common;
        }

        foreach($linkedIDList as $linkedID)
        {
            $extra = array("{$linkedApp}.{$linkedModule}", 'view', "dataID=$linkedID", $linkedName . ' ' . zget($linkedDatas, $linkedID));
            $this->action->create($flow->module, $dataID, 'unlinked', '', $extra);

            $extra = array("{$flow->app}.{$flow->module}", 'view', "dataID=$dataID", $flow->name . ' ' . zget($flowDatas, $dataID));
            $this->action->create($linkType, $linkedID, 'unlinkedfrom', '', $extra);
        }

        return !dao::isError();
    }

    /**
     * Import datas.
     *
     * @param  object $flow
     * @access public
     * @return array
     */
    public function import($flow)
    {
        $this->loadModel('action');

        $errorList  = array();
        $recordList = array();
        $actionList = array();
        $dataList   = $this->post->dataList;
        $dataList   = $this->deleteEmpty($dataList);

        $subTables = $this->dao->select('module, `table`')->from(TABLE_WORKFLOW)
            ->where('type')->eq('table')
            ->andWhere('parent')->eq($flow->module)
            ->fetchPairs();

        /* 导入数据。*/
        foreach($dataList as $key => $data)
        {
            $data['createdBy']   = $this->app->user->account;
            $data['createdDate'] = helper::now();

            /* 导入主流程数据。*/
            $this->dao->insert($flow->table)->data($data, 'sub_tables')->autoCheck()->exec();

            if(dao::isError())
            {
                $daoErrors = dao::getError();
                if(is_string($daoErrors)) $errorList['error' . $key] = $daoErrors;
                if(is_array($daoErrors))
                {
                    foreach($daoErrors as $field => $message)
                    {
                        /* Set error key. */
                        $errorKey = '';
                        $errorKey = 'dataList' . $key . $field;

                        $errorList[$errorKey] = $message;
                    }
                }

                break;
            }

            $dataID   = $this->dao->lastInsertId();
            $actionID = $this->action->create($flow->module, $dataID, 'import');

            $recordList[] = $dataID;
            $actionList[] = $actionID;

            /* 导入明细表数据。*/
            if(isset($data['sub_tables']))
            {
                foreach($data['sub_tables'] as $subModule => $subDatas)
                {
                    if(!isset($subTables[$subModule])) continue;

                    $subTable = $subTables[$subModule];

                    foreach($subDatas as $subKey => $subData)
                    {
                        $subData['parent']      = $dataID;
                        $subData['createdBy']   = $this->app->user->account;
                        $subData['createdDate'] = helper::now();

                        $this->dao->insert($subTable)->data($subData)->autoCheck()->exec();

                        if(dao::isError())
                        {
                            $daoErrors = dao::getError();
                            if(is_string($daoErrors)) $errorList['error' . $key] = $daoErrors;
                            if(is_array($daoErrors))
                            {
                                foreach($daoErrors as $field => $message)
                                {
                                    /* Set error key. */
                                    $errorKey = '';
                                    $errorKey = 'dataList' . $key . 'sub_tables' . $subTable->module . $subKey . $field;

                                    $errorList[$errorKey] = $message;
                                }
                            }
                            break;
                        }
                    }

                    if(dao::isError()) break;
                }
            }

            if(dao::isError()) break;
        }

        /* 如果存在错误则把已导入的数据删除并返回错误信息。*/
        if($errorList)
        {
            $this->dao->delete()->from($flow->table)->where('id')->in($recordList)->exec();
            $this->dao->delete()->from(TABLE_ACTION)->where('id')->in($actionList)->exec();

            foreach($subTables as $subTable)
            {
                $this->dao->delete()->from($subTable)->where('parent')->in($recordList)->exec();
            }

            return array('result' => 'fail', 'message' => $errorList);
        }

        $locate = helper::createLink($flow->module, 'browse');
        return array('result' => 'success', 'message' => $this->lang->saveSuccess, 'recordList' => $recordList, 'actionList' => $actionList, 'locate' => $locate);
    }

    /**
     * Delete empty data.
     *
     * @param  array  $dataList
     * @access public
     * @return array
     */
    public function deleteEmpty($dataList)
    {
        /* 检查是否有空数据，如有则删除该条数据。*/
        foreach($dataList as $key => $data)
        {
            unset($dataList[$key]['id']);
            unset($dataList[$key]['editedBy']);
            unset($dataList[$key]['editedDate']);

            $emptyData = true;

            /* 检查主流程数据是否有为空的情况。*/
            foreach($data as $field => $value)
            {
                if($field == 'sub_tables')
                {
                    /* 检查明细表数据是否有为空的情况。*/
                    foreach($value as $subModule => $subDatas)
                    {
                        foreach($subDatas as $subKey => $subData)
                        {
                            unset($dataList[$key]['sub_tables'][$subModule]['id']);
                            unset($dataList[$key]['sub_tables'][$subModule]['editedBy']);
                            unset($dataList[$key]['sub_tables'][$subModule]['editedDate']);

                            $emptySubData = true;

                            foreach($subData as $subField => $subValue)
                            {
                                if($subValue)
                                {
                                    $emptySubData = false;
                                    break;
                                }
                            }

                            if($emptySubData) unset($dataList[$key]['sub_tables'][$subModule][$subKey]);
                        }
                    }
                }
                else
                {
                    if($value)$emptyData = false;
                }
            }

            if($emptyData) unset($dataList[$key]);
        }

        return $dataList;
    }

    /**
     * Extract app name and module name.
     *
     * @param  string $module
     * @access public
     * @return array
     */
    public function extractAppAndModule($module)
    {
        if(strpos($module, '.') === false) return array('', $module);
        if(strpos($module, '.') === false) return explode('.', $module);
    }

    /**
     * Set search params.
     *
     * @param  object $flow
     * @param  object $action       This param is used in other extensions. Don't remove it.
     * @param  string $actionURL
     * @access public
     * @return void
     */
    public function setSearchParams($flow, $action = null, $actionURL = '')
    {
        $fieldList = $this->loadModel('workflowfield', 'flow')->getList($flow->module, 'searchOrder, `order`, id');

        $canSearch = false;
        foreach($fieldList as $field)
        {
            if($field->canSearch)
            {
                $canSearch = true;
                break;
            }
        }
        if(!$canSearch) return false;

        if(!isset($this->config->{$flow->module})) $this->config->{$flow->module} = new stdclass();
        $this->config->{$flow->module}->search['module'] = $flow->module;

        $fieldValues = array();
        $formName    = $flow->module . 'Form';
        if($this->session->$formName)
        {
            foreach($this->session->$formName as $formKey => $formField)
            {
                if(strpos($formKey, 'field') !== false)
                {
                    $fieldNO    = substr($formKey, 5);
                    $fieldValue = zget($this->session->$formName, "value{$fieldNO}", '');

                    if($fieldValue) $fieldValues[$formField][$fieldValue] = $fieldValue;
                }
            }
        }

        foreach($fieldList as $field)
        {
            if(empty($field->canSearch)) continue;

            if(in_array($field->control, $this->config->workflowfield->optionControls))
            {
                $field->options = $this->workflowfield->getFieldOptions($field, true, zget($fieldValues, $field->field, ''), '', $this->config->flowLimit);
            }

            $this->config->{$flow->module}->search['fields'][$field->field] = $field->name;

            if($field->control == 'label')        $this->config->{$flow->module}->search['params'][$field->field] = array('operator' => '=',       'control' => 'input',  'values' => '');
            if($field->control == 'input')        $this->config->{$flow->module}->search['params'][$field->field] = array('operator' => 'include', 'control' => 'input',  'values' => '');
            if($field->control == 'textarea')     $this->config->{$flow->module}->search['params'][$field->field] = array('operator' => 'include', 'control' => 'input',  'values' => '');
            if($field->control == 'select')       $this->config->{$flow->module}->search['params'][$field->field] = array('operator' => '=',       'control' => 'select', 'values' => array('' => '') + $field->options);
            if($field->control == 'multi-select') $this->config->{$flow->module}->search['params'][$field->field] = array('operator' => 'include', 'control' => 'select', 'values' => array('' => '') + $field->options);
            if($field->control == 'radio')        $this->config->{$flow->module}->search['params'][$field->field] = array('operator' => '=',       'control' => 'select', 'values' => array('' => '') + $field->options);
            if($field->control == 'checkbox')     $this->config->{$flow->module}->search['params'][$field->field] = array('operator' => 'include', 'control' => 'select', 'values' => array('' => '') + $field->options);
            if($field->control == 'richtext')     $this->config->{$flow->module}->search['params'][$field->field] = array('operator' => 'include', 'control' => 'input',  'values' => '');
            if($field->control == 'date')         $this->config->{$flow->module}->search['params'][$field->field] = array('operator' => '>=',      'control' => 'input',  'values' => '', 'class' => 'date');
            if($field->control == 'datetime')     $this->config->{$flow->module}->search['params'][$field->field] = array('operator' => '>=',      'control' => 'input',  'values' => '', 'class' => 'datetime');
            if($field->control == 'decimal')      $this->config->{$flow->module}->search['params'][$field->field] = array('operator' => '=',       'control' => 'input',  'values' => '');
            if($field->control == 'integer')      $this->config->{$flow->module}->search['params'][$field->field] = array('operator' => '=',       'control' => 'input',  'values' => '');
        }

        /* Build search form. */
        if(!$actionURL) $actionURL = helper::createLink($flow->module, 'browse', "mode=bysearch&label=myQueryID");
        $this->config->{$flow->module}->search['actionURL'] = $actionURL;
        $this->loadModel('search')->setSearchParams($this->config->{$flow->module}->search);
    }

    /**
     * Set editor of a action of a flow.
     *
     * @param  string $module
     * @param  string $action
     * @param  array  $fields
     * @access public
     * @return void
     */
    public function setFlowEditor($module, $action, $fields)
    {
        $editor = '';
        if(!isset($this->lang->$module)) $this->lang->$module = new stdclass();
        foreach($fields as $key => $field)
        {
            $this->lang->$module->{$field->field} = $field->name;
            if($field->control == 'richtext' && $field->show && !$field->readonly) $editor .= ',' . $field->field;
        }
        $editor = trim($editor, ',');

        if($editor)
        {
            if(!isset($this->config->flow)) $this->config->flow = new stdclass();
            $this->config->flow->editor = new stdclass();
            $this->config->flow->editor->$action = array('id' => $editor, 'tools' => 'simple');
        }
    }

    /**
     * Set fields the excel functions needed.
     *
     * @param  string $module
     * @param  array  $fields
     * @access public
     * @return void
     */
    public function setExcelFields($module, $fields)
    {
        $dateFields   = array();
        $editorFields = array();

        foreach($fields as $field)
        {
            if($field->control == 'date' or $field->control == 'datetime') $dateFields[] = $field->field;
            if($field->control == 'richtext') $editorFields[] = $field->field;
        }

        if(!isset($this->config->excel)) $this->config->excel = new stdclass();

        $this->config->excel->dateFields      = $dateFields;
        $this->config->excel->editor[$module] = $editorFields;
    }

    /**
     * Set children of a flow.
     *
     * @param  string $module
     * @param  string $action
     * @param  array  $fields
     * @param  int    $dataID
     * @access public
     * @return void
     */
    public function setFlowChild($module, $action, $fields, $dataID = 0)
    {
        $this->loadModel('workflowaction');

        $childFields  = array();
        $childDatas   = array();
        $childModules = $this->loadModel('workflow', 'flow')->getList('table', '', $module);
        foreach($childModules as $childModule)
        {
            $key = 'sub_' . $childModule->module;

            if(isset($fields[$key]) && $fields[$key]->show)
            {
                $childData = $this->getDataList($childModule, '', 0, '', $dataID, 'id_asc');

                $childFields[$key] = $this->workflowaction->getFields($childModule->module, $action, true, $childData);
                $childDatas[$key]  = $childData;
            }
        }

        return array($childFields, $childDatas);
    }

    /**
     * Send notice or mail.
     *
     * @param  object $flow
     * @param  object $action
     * @param  array  $result
     * @access public
     * @return void
     */
    public function sendNotice($flow, $action, $result)
    {
        if($action->type == 'single')
        {
            if(!empty($result['recordID']) && !empty($result['actionID']))
            {
                $toList = array();
                if($this->post->assignedTo) $toList[] = $this->post->assignedTo;
                if($this->post->mailto)     $toList   = array_merge($toList, $this->post->mailto);
                if($action->toList)         $toList   = array_merge($toList, explode(',', $action->toList));

                if(!empty($toList)) $this->sendmail($flow, $action, $toList, $result['recordID'], $result['actionID']);
            }
        }
        else
        {
            if(!empty($result['recordList']) && !empty($result['actionList']))
            {
                if($action->batchMode == 'same')
                {
                    $toList = array();
                    if($this->post->assignedTo) $toList[] = $this->post->assignedTo;
                    if($this->post->mailto)     $toList   = array_merge($toList, $this->post->mailto);
                    if($action->toList)         $toList   = array_merge($toList, explode(',', $action->toList));

                    if(!empty($toList))
                    {
                        foreach($result['recordList'] as $key => $dataID)
                        {
                            if(!empty($dataID) && !empty($result['actionList'][$key])) $this->sendmail($flow, $action, $toList, $dataID, $result['actionList'][$key]);
                        }
                    }
                }
                else
                {
                    $actionToList = $action->toList ? explode(',', $action->toList) : array();
                    foreach($result['recordList'] as $key => $dataID)
                    {
                        if(empty($dataID) or empty($result['actionList'][$key])) continue;

                        $toList = array();
                        if(!empty($_POST['dataList'][$key]['assignedTo'])) $toList[] = $_POST['dataList'][$key]['assignedTo'];
                        if(!empty($_POST['dataList'][$key]['mailto']))     $toList   = array_merge($toList, $_POST['dataList'][$key]['mailto']);
                        if(!empty($actionToList))                          $toList   = array_merge($toList, $actionToList);

                        if(!empty($toList)) $this->sendmail($flow, $action, $toList, $dataID, $result['actionList'][$key]);
                    }
                }
            }
        }
    }

    /**
     * Send mail.
     *
     * @param  object $flow
     * @param  object $method
     * @param  array  $noticeUsers
     * @param  int    $dataID
     * @param  int    $actionID
     * @access public
     * @return void
     */
    public function sendmail($flow, $method, $noticeUsers, $dataID, $actionID)
    {
        /* Get action info. */
        $action          = $this->loadModel('action')->getByID($actionID);
        $history         = $this->action->getHistory($actionID);
        $action->history = isset($history[$actionID]) ? $history[$actionID] : array();

        /* Set toList and ccList. */
        $data  = $this->getDataByID($flow, $dataID);
        $users = $this->loadModel('user')->getDeptPairs();

        $toList = '';
        if($noticeUsers)
        {
            $toList = array();
            $this->loadModel('workflowhook', 'flow');
            foreach($noticeUsers as $toUser)
            {
                if(!$toUser) continue;

                if($toUser == 'deptManager')
                {
                    $toList[] = $this->workflowhook->getParamRealValue($toUser);
                }
                else
                {
                    if(isset($users[$toUser])) $toList[] = $toUser;
                    if(isset($data->$toUser))
                    {
                        if(is_array($data->$toUser))
                        {
                            foreach($data->$toUser as $user) $toList[] = $user;
                        }
                        else
                        {
                            $toList[] = $data->$toUser;
                        }
                    }
                }
            }
            $toList = implode(',', array_unique($toList));
        }

        /* send notice if user is online and return failed accounts. */
        $ccList = '';
        $toList = $this->action->sendNotice($actionID, $toList);
        $ccList = explode(',', trim($toList, ','));
        $toList = array_shift($ccList);
        $ccList = join(',', $ccList);

        $fields = $this->loadModel('workflowaction', 'flow')->getFields($flow->module, $method->action, true, $data);

        list($childFields, $childDatas) = $this->setFlowChild($flow->module, $method->action, $fields, $dataID);

        /* Create the email content. */
        $createdBy = zget($this->config->flow->defaultFields->createdBy, $flow->module, 'createdBy');
        $subject   = "{$flow->name}{$method->name}#{$data->id} " . ($createdBy ? zget($users, $data->$createdBy) : '');

        /* Get mail content. */
        $mailTitle  = $subject;
        $oldcwd     = getcwd();
        $modulePath = $this->app->getModulePath($appName = 'sys', 'flow');
        $viewFile   = $modulePath . 'view/sendmail.html.php';
        chdir($modulePath . 'view');

        if(file_exists($modulePath . 'ext/view/sendmail.html.php'))
        {
            $viewFile = $modulePath . 'ext/view/sendmail.html.php';
            chdir($modulePath . 'ext/view');
        }

        ob_start();
        include $viewFile;
        foreach(glob($modulePath . 'ext/view/sendmail.*.html.hook.php') as $hookFile) include $hookFile;
        $mailContent = ob_get_contents();
        ob_end_clean();

        chdir($oldcwd);

        /* Send emails. */
        $this->loadModel('mail')->send($toList, $subject, $mailContent, $ccList);
        if($this->mail->isError()) error_log(join("\n", $this->mail->getError()));
    }

    /**
     * Check if user can access.
     *
     * @param  object $flow
     * @param  object $action
     * @access public
     * @return void
     */
    public function checkPrivilege($flow, $action)
    {
        /* 防止用户直接输入类似flow-browse-$module的链接绕过模块的权限检查。*/
        if(!commonModel::hasPriv($flow->module, $action->action))
        {
            $vars     = "module=$flow->module&method=$action->action";
            $denyLink = helper::createLink('user', 'deny', $vars);
            die(js::locate($denyLink));
        }

        $layoutFields = $this->loadModel('workflowlayout', 'flow')->getFields($flow->module, $action->action);
        if(!empty($action->open) && $action->open != 'none')
        {
            if(!$layoutFields) die(js::alert(sprintf($this->lang->flow->error->emptyLayoutFields, $flow->name, $action->name)) . js::locate('back'));
        }
    }

    /**
     * Check label privilege.
     *
     * @param  object  $flow
     * @param  array   $labels
     * @param  int     $label
     * @access public
     * @return void
     */
    public function checkLabel($flow, $labels, $label)
    {
        /* Check the privilege of the label, if the label has no privilege, locate to the privileged label. */
        if(!commonModel::hasPriv($flow->module, $label))
        {
            foreach($labels as $labelID)
            {
                if(commonModel::hasPriv($flow->module, $labelID)) die(js::locate(helper::createLink($flow->module, 'browse', "label={$labelID}")));
            }

            $vars     = "module=$flow->module&method=browse&label=$label";
            $denyLink = helper::createLink('user', 'deny', $vars);
            die(js::locate($denyLink));
        }
    }

    /**
     * Check if the category is valid and parse it.
     *
     * @param  string $module
     * @param  string $category
     * @access public
     * @return array
     */
    public function checkCategory($module, $category)
    {
        $params = explode('=', $category);
        if(count($params) != 2) return array('', '', '');

        $categoryField = $params[0];
        $categoryValue = $params[1];

        $field = $this->loadModel('workflowfield', 'flow')->getByField($module, $categoryField);
        if(!$field) return array('', '', '');

        if($field->control == 'multi-select' or $field->control == 'checkbox')
        {
            $categoryQuery = "(`$categoryField` = '" . (int)$categoryValue . "' OR `$categoryField` LIKE '" . (int)$categoryValue . ",%' OR `$categoryField` LIKE '%," . (int)$categoryValue . ",%' OR `$categoryField` LIKE '%," . (int)$categoryValue . "')";
        }
        else
        {
            $categoryQuery = "`$categoryField` = '" . (int)$categoryValue . "'";
        }

        if($field->options == 'category')
        {
            $categoryType = $module . '_' . $field->field;

            return array($categoryType, $categoryQuery, $categoryValue);
        }
        elseif((int)$field->options > 0)
        {
            $datasource = $this->loadModel('workflowdatasource', 'flow')->getByID((int)$field->options);

            if(zget($datasource, 'type') == 'category')
            {
                $categoryType = 'datasource_' . $datasource->id;

                return array($categoryType, $categoryQuery, $categoryValue);
            }
        }

        return array('', '', '');
    }

    /**
     * Check rules.
     *
     * @param  array  $fields
     * @param  object $data
     * @param  object $dao
     * @param  int    $dataID
     * @access public
     * @return object
     */
    public function checkRules($fields, $data, $dao, $dataID = 0)
    {
        $tmpData = clone $data;

        foreach($fields as $field)
        {
            /* If the field don't show in view, don't check it. */
            if(empty($field->show) || empty($field->rules) || $field->control == 'file') continue;

            if(!isset($data->{$field->field})) $data->{$field->field} = false;

            $rules = explode(',', trim($field->rules, ','));
            $rules = array_unique($rules);
            /* Check rules of fields. */
            foreach($rules as $rule)
            {
                if(empty($rule)) continue;

                $tmpDao = clone $dao;
                $rule   = $this->workflowrule->getByID($rule);
                $dao    = $tmpDao;

                if(empty($rule)) continue;

                if($rule->type == 'system')
                {
                    if($rule->rule == 'unique' && $dao->sqlobj->data->{$field->field} == '' ) continue;

                    $condition = '';
                    if($rule->rule == 'unique' and !empty($dataID)) $condition = "id != '{$dataID}'";
                    if(!empty($data->id)) $condition = "id != '{$data->id}'";
                    $dao->check($field->field, $rule->rule, $condition);
                }
                elseif($rule->type == 'regex')
                {
                    $dao->check($field->field, 'reg', $rule->rule);
                }
                elseif($rule->type == 'func')
                {
                    /* To do something. */
                }
            }
        }

        $data = $tmpData;

        return $dao;
    }

    /**
     * Check condition is available.
     *
     * @param  array|string  $conditions
     * @param  object        $data
     * @access public
     * @return bool
     */
    public function checkConditions($conditions, $data)
    {
        if(is_string($conditions)) $conditions = json_decode($conditions);
        if(empty($conditions)) return true;

        $this->loadModel('workflowfield', 'flow');
        $this->loadModel('workflowhook', 'flow');

        foreach($conditions as $condition)
        {
            $enabled = true;
            if($condition->conditionType == 'data')
            {
                $index = 1;
                foreach($condition->fields as $field)
                {
                    if(!isset($data->{$field->field})) return false;

                    $checkFunc = 'check' . $field->operator;
                    $var       = $data->{$field->field};
                    $param     = $field->param;

                    if(in_array($field->param, $this->config->flow->variables))
                    {
                        $param = $this->workflowhook->getParamRealValue($param);
                    }

                    if(is_array($var) && !is_array($param)) $param = explode(',', $param);

                    $result = validater::$checkFunc($var, $param);

                    if($index == 1) $enabled = $result;
                    if($index > 1)
                    {
                        $logicalOperator = zget($field, 'logicalOperator', 'and');

                        if($logicalOperator == 'and') $enabled = $enabled && $result;
                        if($logicalOperator == 'or')  $enabled = $enabled || $result;
                    }

                    $index++;
                }
            }
            elseif($condition->conditionType == 'sql')
            {
                $sql = $this->workflowfield->replaceTableNames($condition->sql);
                try
                {
                    $sqlResult = $this->dbh->query($sql)->fetch();
                    if($condition->sqlResult == 'empty')
                    {
                        $enabled = empty($sqlResult);
                    }
                    elseif($condition->sqlResult == 'notempty')
                    {
                        $enabled = !empty($sqlResult);
                    }
                }
                catch(PDOException $exception)
                {
                    $enabled = false;
                }
            }

            if($enabled) return true;
        }

        return false;
    }

    /**
     * Add ditto to field options.
     *
     * @param  array  $fields
     * @access public
     * @return array
     */
    public function addDitto($fields)
    {
        foreach($fields as $field)
        {
            if(!$field->show) continue;
            if($field->control != 'select' or empty($field->options)) continue;

            $ditto  = array('ditto' => $this->lang->flow->ditto);
            $option = reset($field->options);
            if($option == '')
            {
                $key = key($field->options);
                unset($field->options[$key]);
                $field->options = array($key => $option) + $ditto + $field->options;
            }
            else
            {
                $field->options = $ditto + $field->options;
            }
        }

        return $fields;
    }

    /**
     * Build control.
     *
     * @param  object $field
     * @param  string $fieldValue
     * @param  string $element
     * @param  string $childModule
     * @param  bool   $emptyValue
     * @param  bool   $preview
     * @access public
     * @return string
     */
    public function buildControl($field, $fieldValue, $element = '', $childModule = '', $emptyValue = false, $preview = false)
    {
        $isMobile = $this->app->viewType === 'mhtml';

        if(empty($fieldValue)) $fieldValue = zget($field, 'defaultValue', zget($field, 'default', ''));
        if($field->control == 'date' && $fieldValue == '0000-00-00') $fieldValue = zget($field, 'defaultValue', zget($field, 'default', ''));
        if($field->control == 'datetime' && $fieldValue == '0000-00-00 00:00:00') $fieldValue = zget($field, 'defaultValue', zget($field, 'default', ''));

        if(in_array($fieldValue, $this->config->flow->variables)) $fieldValue = $this->loadModel('workflowhook', 'flow')->getParamRealValue($fieldValue, 'control');
        if($field->control == 'date') $fieldValue = substr($fieldValue, 0, 10);

        if($emptyValue) $fieldValue = '';

        $options = $this->loadModel('workflowfield', 'flow')->getFieldOptions($field, true, $fieldValue, '', $this->config->flowLimit);

        /* If field type is number, set empty value with key as 0. */
        if(in_array($field->type, $this->config->workflowfield->numberTypes))
        {
            unset($options['']);
            $options = array(0 => '') + $options;

            $field = $this->workflowfield->processNumberField($field);
        }

        foreach($options as $optionKey => $optionValue)
        {
            if($field->control != 'select' && !$optionValue) unset($options[$optionKey]);
        }

        if(!$element) $element = $field->field;

        $placeholder = '';
        if($childModule)
        {
            if(!$element) $element = "children[$childModule][{$field->field}][KEY]";
            if($field->control == 'radio')    $field->control = 'select';
            if($field->control == 'checkbox') $field->control = 'multi-select';

            $placeholder = ($field->control == 'select' || $field->control == 'multi-select') ? "data-placeholder='{$field->name}'" : "placeholder='{$field->name}'";
        }

        if($field->control == 'multi-select' && substr($element, -2) != '[]' ) $element .= '[]';

        $control = '';
        if($isMobile)  $control = $this->buildMobileControl($field, $fieldValue, $options, $element, $placeholder);
        if(!$isMobile) $control = $this->buildDesktopControl($field, $fieldValue, $childModule, $options, $element, $placeholder, $preview);

        if($field->field == 'subStatus')
        {
            $flow = $this->loadModel('workflow', 'flow')->getByModule($field->module);

            if($flow->type == 'flow') $control .= $this->getSubStatusScript($field);
        }

        return $control;
    }

    /**
     * Build control for desktop.
     *
     * @param  object $field
     * @param  string $value
     * @param  string $childModule
     * @param  array  $options
     * @param  string $element
     * @param  string $placeholder
     * @param  bool   $preview
     * @access public
     * @return string
     */
    public function buildDesktopControl($field, $value, $childModule, $options, $element, $placeholder, $preview = false)
    {
        $required     = '';
        $notEmptyRule = $this->loadModel('workflowrule', 'flow')->getByTypeAndRule('system', 'notempty');
        if(!$childModule && $notEmptyRule && strpos(",$field->rules,", ",$notEmptyRule->id,") !== false)
        {
            $required = "<div class='required required-wrapper'></div>";
        }

        $elementID = str_replace(array('[', ']'), '', $element);
        $dataValue = is_array($value) ? implode(',', $value) : $value;
        $data      = "data-module='$field->module' data-field='$field->field' data-value='$dataValue'";
        $class     = $preview ? 'chosen' : 'picker-select';

        /* Display 3 datas in preview mode. */
        if($preview)
        {
            $tmpOptions = array();

            $index = 1;
            foreach($options as $k => $v)
            {
                if($index > 3) break;

                $tmpOptions[$k] = $v;

                $index++;
            }

            $options = $tmpOptions;
        }

        switch($field->control)
        {
            case 'label':
                return "<label>$value</label>" . html::hidden($element, $value);
            case 'input':
                return $required . html::input($element, $value, "id='$elementID' class='form-control' $placeholder $data");
            case 'decimal':
            case 'integer':
            case 'formula':
                return $required . html::number($element, $value, "id='$elementID' class='form-control' $placeholder max='{$field->max}' min='{$field->min}' step='{$field->step}' $data");
            case 'textarea':
            case 'richtext':
                return $required . html::textarea($element, $value, "id='$elementID' rows='3' class='form-control' $placeholder $data");
            case 'select':
                return $required . html::select($element, $options, $value, "id='$elementID' class='form-control $class' $placeholder $data");
            case 'multi-select':
                return $required . html::select($element, $options, $value, "id='$elementID' class='form-control $class' multiple $placeholder $data");
            case 'radio':
                return $required . "<div id='$elementID'  class='checkboxDIV'>" . html::radio($element, $options, $value, $data) . '</div>';
            case 'checkbox':
                return $required . "<div id='$elementID' class='radioDIV'>" . html::checkbox($element, $options, $value, $data) . '</div>';
            case 'date':
                return $required . html::input($element, $value, "id='$elementID' class='form-control form-date' $placeholder $data");
            case 'datetime':
                return $required . html::input($element, $value, "id='$elementID' class='form-control form-datetime' $placeholder $data");
            case 'time':
                return $required . html::input($element, $value, "id='$elementID' class='form-control form-time' $placeholder $data");
        }

        return "<label>$value</label>" . html::hidden($element, $value);
    }

    /**
     * Build control for mobile.
     *
     * @param  object $field
     * @param  string $value
     * @param  array  $options
     * @param  string $element
     * @param  string $placeholder
     * @access public
     * @return string
     */
    public function buildMobileControl($field, $value, $options, $element, $placeholder)
    {
        switch($field->control)
        {
            case 'label':
                return "<label>$value</label>" . html::hidden($element, $value);
            case 'input':
                return html::input($element, $value, "class='input' $placeholder");
            case 'decimal':
            case 'integer':
                return html::number($element, $value, "class='input' $placeholder max='{$field->max}' min='{$field->min}' step='{$field->step}'");
            case 'textarea':
            case 'richtext':
                return html::textarea($element, $value, "rows='3' class='textarea' $placeholder");
            case 'select':
                return "<div class='select'>" . html::select($element, $options, $value, "$placeholder") . "</div>";
            case 'multi-select':
                return "<div class='select'>" . html::select($element, $options, $value, "multiple='multiple' $placeholder") . "</div>";
            case 'radio':
                $html = '';
                foreach($options as $key => $value)
                {
                    $html .= "<div class='radio inline-block'>";
                    $html .= "<input type='radio' name='{$element}' value='{$key}'>";
                    $html .= "<label for='{$element}'>{$value}</label>";
                    $html .= "</div>";
                }
                return $html;
            case 'checkbox':
                $html = '';
                foreach($options as $key => $option)
                {
                    $checked = ($value && $key == $value) ? "checked='checked'" : '';
                    $html .= "<div class='checkbox inline-block'>";
                    $html .= "<input type='checkbox' name='{$element}[]' id='{$element}{$key}' value='{$key}' {$checked}>";
                    $html .= "<label for='{$element}[]'>{$option}</label>";
                    $html .= "</div>";
                }
                return $html;
            case 'date':
                return "<input type='date' class='input' id='$element' name='$element' value='{$value}' $placeholder>";
            case 'datetime':
                if($value && $value != '0000-00-00 00:00:00') $value = date('Y-m-d\TH:i', strtotime($value));
                return "<input type='datetime-local' class='input' id='$element' name='$element' value='{$value}' $placeholder>";
            case 'time':
                return "<input type='time' class='input' id='$element' name='$element' value='{$value}' $placeholder>";
        }

        return "<label>$value</label>" . html::hidden($element, $value);
    }

    /**
     * Build operate menu.
     *
     * @param  object $flow
     * @param  object $data
     * @param  string $type     menu 菜单栏 | browse 仅列表页 | view 仅详情页 | browseandview 列表页和详情页同时显示
     * @access public
     * @return string
     */
    public function buildOperateMenu($flow, $data, $type = 'browse')
    {
        if($type != 'menu' && zget($data, 'deleted', '0') == '1' || !$flow) return '';

        $this->loadModel('workflow', 'flow');
        $this->loadModel('workflowaction', 'flow');
        $this->loadModel('workflowfield', 'flow');
        $this->loadModel('workflowrelation', 'flow');

        $isMobile   = $this->app->viewType === 'mhtml';
        $relations  = $this->workflowrelation->getList($flow->module);
        $viewAction = $this->workflowaction->getByModuleAndAction($flow->module, 'view');

        $dataID = isset($data->id) ? $data->id : 0;
        $btn    = $type == 'menu' ? 'btn btn-primary' : ($type == 'view' ? 'btn' : '');
        $menu   = ($type == 'view' && !$isMobile && !$flow->buildin) ? "<div class='main-actions'><div class='btn-toolbar'>" : '';
        if($type == 'view' && $flow->buildin) $menu .= "<div class='divider'></div>";

        if($type == 'view' && !$isMobile && !$flow->buildin)
        {
            $menu .= $this->session->flowList ? baseHTML::a($this->session->flowList, $this->lang->goback, "class='btn btn-back'") : html::backButton();
            $menu .= "<div class='divider'></div>";
        }

        $actions = $this->workflowaction->getList($flow->module);
        if($type != 'menu' && $relations && $data)
        {
            $relationMenu = '';
            $flowPairs    = $this->loadModel('workflow', 'flow')->getPairs();

            /* Build entrance of other flows by the relations. */
            foreach($relations as $relation)
            {
                $relationActions = array();
                foreach($actions as $action)
                {
                    if($action->virtual)
                    {
                        $relationAction = new stdclass();
                        $relationAction->name       = $action->name;
                        $relationAction->status     = $action->status;
                        $relationAction->conditions = $action->conditions;

                        $relationActions[$action->action] = $relationAction;
                    }
                }

                if(strpos(",$relation->actions,", ',one2one,') !== false)
                {
                    $relationAction = zget($relationActions, "{$relation->next}_create", '');

                    if(!$relationAction) continue;
                    if(!commonModel::hasPriv($relation->next, 'create')) continue;
                    if($relationAction->status == 'disable') continue;

                    $enabled = $this->checkConditions($relationAction->conditions, $data);

                    $actionName = $relationAction->name;
                    if($enabled)
                    {
                        $actionLink = helper::createLink($relation->next, 'create', "step=form&prevModule=$flow->module&prevDataID={$data->id}");
                        if($isMobile)  $relationMenu .= "<a data-remote='{$actionLink}' data-display='modal' data-placement='bottom'>{$actionName}</a>";
                        if(!$isMobile) $relationMenu .= baseHTML::a($actionLink, $actionName, "class='$btn'");
                    }
                    else
                    {
                        if($type == 'browse') $relationMenu .= baseHTML::a('javascript:;', $actionName, "class='disabled'");
                    }
                }
                if(strpos(",$relation->actions,", ',one2many,') !== false)
                {
                    $relationAction = zget($relationActions, "{$relation->next}_batchcreate", '');

                    if(!$relationAction) continue;
                    if(!commonModel::hasPriv($relation->next, 'batchcreate')) continue;
                    if($relationAction->status == 'disable') continue;

                    $enabled = $this->checkConditions($relationAction->conditions, $data);

                    $actionName = $relationAction->name;
                    if($enabled)
                    {
                        $actionLink = helper::createLink($relation->next, 'batchcreate', "step=form&prevModule=$flow->module&prevDataID={$data->id}");
                        if($isMobile)  $relationMenu .= "<a data-remote='{$actionLink}' data-display='modal' data-placement='bottom'>{$actionName}</a>";
                        if(!$isMobile) $relationMenu .= baseHTML::a($actionLink, $actionName, "class='$btn'");
                    }
                    else
                    {
                        if($type == 'browse') $relationMenu .= baseHTML::a('javascript:;', $actionName, "class='disabled'");
                    }
                }
            }
            if($relationMenu)
            {
                $menu .= $relationMenu;
                if($type == 'view' && !$isMobile && !$flow->buildin)
                {
                    $menu .= "<div class='divider'></div>";
                }
            }
        }

        $dropdownMenu = '';

        foreach($actions as $action)
        {
            if($action->buildin) continue;
            if($action->virtual) continue;
            if($action->status != 'enable') continue;
            if($action->action == 'browse') continue;
            if($action->action == 'export') continue;
            if($action->action == 'exporttemplate') continue;
            if($action->action == 'import') continue;
            if($action->action == 'showimport') continue;
            if($action->action == 'link') continue;
            if($action->action == 'unlink') continue;
            if($action->action == 'report') continue;
            if($type == 'menu' && $action->open == 'none') continue;
            if($type == 'menu' && $action->action == 'delete') continue;
            if($type != 'menu' && $action->type == 'batch') continue;
            if($type == 'menu' && $action->type == 'batch' && $action->action != 'batchcreate') continue;
            if(strpos($action->position, $type) === false) continue;
            if(!commonModel::hasPriv($flow->module, $action->action)) continue;

            $enabled = true;
            if($type != 'menu') $enabled = $this->checkConditions($action->conditions, $data);

            if($enabled)
            {
                $icon   = "<i class='icon-cogs'> </i>";
                $params = "dataID=$dataID";
                if($action->action == 'create' or $action->action == 'batchcreate')
                {
                    $icon   = "<i class='icon-plus'> </i>";
                    $params = '';
                }
                if($action->action == 'edit') $icon = "<i class='icon-pencil'> </i>";

                $label       = $type == 'menu' ? $icon . $action->name : $action->name;
                $loadInModal = ($type == 'view' && $viewAction->open == 'modal' && $action->open == 'modal') ? "loadInModal" : '';
                $reload      = ($type != 'menu' && $action->open == 'none' && $action->action != 'delete') ? 'reloadPage' : '';
                $attr        = ($type != 'menu' && $action->open == 'modal' && $loadInModal == '') ? "data-toggle='modal'" : '';
                $deleter     = $action->action == 'delete' ? 'deleter' : '';
                $url         = helper::createLink($flow->module, $action->action, $params);
                $link        = baseHTML::a($url, $label, "class='$loadInModal $reload $btn $deleter' $attr");

                if($type == 'browse' && $action->show == 'dropdownlist')
                {
                    $dropdownMenu .= "<li>" . $link . "</li>";
                }
                else
                {
                    if($isMobile)
                    {
                        if($action->action == 'delete')
                        {
                            $menu .=  "<a data-remote='$url' data-display='ajaxAction' data-ajax-delete='true' data-locate=''>{$label}</a>";
                        }
                        else
                        {
                            if($reload)  $menu .= "<a data-remote='{$url}' data-display='ajaxAction' data-locate='self'>{$label}</a>";
                            if(!$reload) $menu .= "<a data-remote='{$url}' data-display='modal' data-placement='bottom'>{$label}</a>";
                        }
                    }
                    else
                    {
                        $menu .= $link;
                    }
                }
            }
            else
            {
                if($type == 'browse' and $action->show == 'direct') $menu .= baseHTML::a('javascript:;', $action->name, "class='disabled'");
            }
        }

        if($type == 'browse' && $dropdownMenu != '')
        {
            $dropdownBefore  = "<div class='dropdown'><a href='javascript:;' data-toggle='dropdown'>{$this->lang->more}<span class='caret'> </span></a>";
            $dropdownBefore .= "<ul class='dropdown-menu pull-right'>";
            $dropdownMenu    = $dropdownBefore . $dropdownMenu . "</ul></div>";
        }

        $menu .= $dropdownMenu;

        if($type == 'view' && !$isMobile && !$flow->buildin) $menu .= '</div></div>';

        return $menu;
    }

    /**
     * Build menu actions of a flow.
     *
     * @param  object $flow
     * @access public
     * @return string
     */
    public function buildMenuActions($flow)
    {
        $menuActions       = "<div id='menuActions'>";
        $canImport         = $this->isClickable($flow->module, 'import');
        $canExportData     = $this->isClickable($flow->module, 'export');
        $canExportTemplate = $this->isClickable($flow->module, 'exporttemplate');
        if($canImport or $canExportTemplate)
        {
            $menuActions .= "<div class='btn-toolbar'>";
            $menuActions .= baseHTML::a('javascript:;', $this->lang->importIcon . $this->lang->import . " <span class='caret'></span>", "class='btn btn-secondary dropdown-toggle' data-toggle='dropdown'");
            $menuActions .= "<ul class='dropdown-menu'>";
            if($canImport)         $menuActions .= '<li>' . baseHTML::a(helper::createLink($flow->module, 'import'), $this->lang->workflowaction->default->actions['import'], "data-toggle='modal'") . '</li>';
            if($canExportTemplate) $menuActions .= '<li>' . baseHTML::a(helper::createLink($flow->module, 'exporttemplate'), $this->lang->workflowaction->default->actions['exporttemplate'], "class='iframe'") . '</li>';
            $menuActions .= '</ul></div>';
        }
        if($canExportData)
        {
            $menuActions .= "<div class='btn-toolbar'>";
            $menuActions .= baseHTML::a('javascript:;', $this->lang->exportIcon . $this->lang->export . " <span class='caret'></span>", "class='btn btn-secondary dropdown-toggle' data-toggle='dropdown'");
            $menuActions .= "<ul class='dropdown-menu'>";
            $menuActions .= '<li>' . baseHTML::a(helper::createLink($flow->module, 'export', 'mode=all'), $this->lang->exportAll, "class='iframe'") . '</li>';
            $menuActions .= '<li>' . baseHTML::a(helper::createLink($flow->module, 'export', 'mode=thisPage'), $this->lang->exportThisPage, "class='iframe'") . '</li>';
            $menuActions .= '</ul></div>';
        }
        $menuActions .= $this->buildOperateMenu($flow, $data = null, $type = 'menu');
        $menuActions .= '</div>';

        return $menuActions;
    }

    /**
     * Build batch actions of a flow.
     *
     * @param  string $module
     * @access public
     * @return string
     */
    public function buildBatchActions($module)
    {
        $batchActions = '';

        $index   = 1;
        $actions = $this->loadModel('workflowaction', 'flow')->getList($module);
        foreach($actions as $action)
        {
            if($action->status   != 'enable') continue;
            if($action->type     != 'batch')  continue;
            if($action->position != 'browse') continue;
            if(!commonModel::hasPriv($module, $action->action)) continue;

            if($index == 1)
            {
                $actionLink    = helper::createLink($module, $action->action);
                $batchActions .= baseHTML::a('javascript:;', $action->name, "class='btn' onclick=\"setFormAction('$actionLink')\"");
            }
            else
            {
                if($index == 2)
                {
                    $batchActions .= "<button type='button' class='btn dropdown-toggle' data-toggle='dropdown'><span class='caret'></span> </button>";
                    $batchActions .= "<ul class='dropdown-menu' role='menu'>";
                }
                $actionLink    = helper::createLink($module, $action->action);
                $batchActions .= '<li>' . baseHTML::a('javascript:;', $action->name, "onclick=\"setFormAction('$actionLink')\"") . '</li>';
            }

            $index++;
        }

        if($batchActions)
        {
            if($index > 2) $batchActions .= '</ul>';   // $index > 2 means there are more than one batch action.

            $batchActions = "<div class='btn-toolbar dropup'>{$batchActions}</div>";
        }

        $relations = $this->loadModel('workflowrelation', 'flow')->getList($module);
        if($relations)
        {
            $relationActions = array();
            foreach($actions as $action)
            {
                if($action->virtual)
                {
                    $relationAction = new stdclass();
                    $relationAction->name   = $action->name;
                    $relationAction->status = $action->status;

                    $relationActions[$action->action] = $relationAction;
                }
            }

            $flows = $this->loadModel('workflow', 'flow')->getPairs();
            foreach($relations as $relation)
            {
                if(strpos(",$relation->actions,", ',many2one,') !== false)
                {
                    $relationAction = zget($relationActions, "{$relation->next}_create", '');

                    if(!$relationAction) continue;
                    if(!commonModel::hasPriv($relation->next, 'create')) continue;
                    if($relationAction->status == 'disable') continue;

                    $actionName    = $relationAction->name;
                    $actionLink    = helper::createLink($relation->next, 'create', "step=form&prevModule=$module");
                    $batchActions .= baseHTML::a('javascript:;', $actionName, "class='btn' onclick=\"setFormAction('$actionLink')\"");
                }
                if(strpos(",$relation->actions,", ',many2many,') !== false)
                {
                    $relationAction = zget($relationActions, "{$relation->next}_batchcreate", '');

                    if(!$relationAction) continue;
                    if(!commonModel::hasPriv($relation->next, 'batchcreate')) continue;
                    if($relationAction->status == 'disable') continue;

                    $actionName    = $relationAction->name;
                    $actionLink    = helper::createLink($relation->next, 'batchcreate', "step=form&prevModule=$module");
                    $batchActions .= baseHTML::a('javascript:;', $actionName, "class='btn' onclick=\"setFormAction('$actionLink')\"");
                }
            }
        }

        return $batchActions;
    }

    /**
     * Check if an action is clickable.
     *
     * @param  string $module
     * @param  mixed  $action
     * @param  object $data
     * @access public
     * @return bool
     */
    public function isClickable($module, $action, $data = null)
    {
        if(is_string($action)) $action = $this->loadModel('workflowaction', 'flow')->getByModuleAndAction($module, $action);

        if(!$action) return false;
        if($action->status != 'enable') return false;
        if(!commonModel::hasPriv($module, $action->action)) return false;

        if($data) return $this->checkConditions($action->conditions, $data);

        return true;
    }

    /**
     * Process blocks.
     *
     * @param  array  $blocks
     * @param  array  $fields
     * @param  bool   $hasInfo
     * @access public
     * @return array
     */
    public function processBlocks($blocks, $fields, $hasInfo = false)
    {
        $positionFields = array();
        foreach($fields as $field)
        {
            if(!isset($positionFields[$field->position])) $positionFields[$field->position] = array();
            $positionFields[$field->position][] = $field;
        }

        $processBlocks = array();

        if($hasInfo)
        {
            $infoBlock = new stdclass();
            $infoBlock->name   = $this->lang->workflowlayout->positionList['view']['info'];
            $infoBlock->fields = zget($positionFields, 'info', array());
            $processBlocks['info'] = $infoBlock;
        }

        if(empty($blocks))
        {
            $basicBlock = new stdclass();
            $basicBlock->name   = $this->lang->workflowlayout->positionList['view']['basic'];
            $basicBlock->fields = zget($positionFields, 'basic', array());
            $processBlocks['basic'] = $basicBlock;
        }
        else
        {
            foreach($blocks as $blockKey => $block)
            {
                $blockData = new stdclass();
                if(!empty($block->tabs))
                {
                    $blockData->tabs = array();
                    foreach($block->tabs as $tabKey => $tabName)
                    {
                        $tabData = new stdclass();
                        $tabData->name   = $tabName;
                        $tabData->fields = zget($positionFields, "block{$blockKey}_tab{$tabKey}", array());

                        $blockData->tabs["tab{$tabKey}"] = $tabData;
                    }
                }
                else
                {
                    $blockData->name   = $block->showName ? $block->name : '';
                    $blockData->fields = zget($positionFields, "block{$blockKey}", array());
                }
                $processBlocks["block{$blockKey}"] = $blockData;
            }
        }

        return $processBlocks;
    }

    /**
     * Process dimension and fileds for report object,and add legend property.
     *
     * @param  string   $module
     * @param  array    $reportList
     * @access public
     * @return array
     */
    public function processReportList($module, $reportList)
    {
        foreach($reportList as $key => $report)
        {
            /* Only process user chosen reports. */
            if(!in_array($report->id, $this->post->reports) || empty($report->dimension))
            {
                unset($reportList[$key]);
                continue;
            }

            $report->dimension = json_decode($report->dimension);
            if($report->countType == 'count')
            {
                $report->legend = false;
                continue;
            }
            $report->fields = json_decode($report->fields);
            $report->legend = count($report->fields) > 1;
        }

        return $reportList;
    }

    /**
     * Query data by session select confition and query data of subTabls.
     *
     * @param  string $module
     * @param  array  $charts
     * @param  array  $fieldList
     * @param  array  $chartModules
     * @access public
     * @return array
     */
    public function getDataByCondition($module, $charts, $fieldList, $chartModules)
    {
        $records = array();

        /* Query data by session sql. */
        $records[$module] = array();
        $queryCondition = $this->session->{$module . 'QueryCondition'};
        if(strpos($queryCondition, 'LIMIT') !== false) $queryCondition = substr($queryCondition, 0, strpos($queryCondition, 'LIMIT'));
        $stmt = $this->dbh->query($queryCondition);
        while($row = $stmt->fetch()) $records[$module][$row->id] = $row;

        /* Select all data from subTables. */
        $childTables   = $this->loadModel('workflow', 'flow')->getList('table', '', $module);
        $defaultFields = $this->config->workflowfield->default->fields;

        foreach($childTables as $childTable)
        {
            if(!isset($chartModules[$childTable->module])) continue;

            $childDatas = $this->getDataList($childTable);

            foreach($childDatas as $childKey => $childData)
            {
                /* If record in the child table but not in main table, then continue. */
                if(!isset($records[$module][$childData->parent]))
                {
                    unset($childDatas[$childKey]);
                    continue;
                }

                $data = new stdclass();
                /* Change the field of child table, because different tables may have the same field name. */
                foreach($childData as $field => $value)
                {
                    if(isset($defaultFields[$field])) continue;

                    $key = $childTable->module . '_' . $field;
                    $data->$key = $value;
                }

                /* Query related main table data of sub table. */
                $parent = $records[$module][$childData->parent];
                foreach($parent as $field => $value)
                {
                    $key = $module . '_' . $field;
                    $data->$key = $value;
                }

                $childDatas[$childKey] = $data;
            }

            $records[$childTable->module] = $childDatas;
        }

        foreach($records as $module => $datas)
        {
            foreach($datas as $key => $data)
            {
                foreach($data as $field => $value)
                {
                    if(!isset($fieldList[$module][$field])) continue;

                    $fieldObject = $fieldList[$module][$field];
                    if($fieldObject->control == 'multi-select' or $fieldObject->control == 'checkbox') $data->$field = explode(',', $value);
                    if($fieldObject->control == 'date' or $fieldObject->control == 'datetime')
                    {
                        $data->$field = formatTime($value);
                    }
                }

                $records[$module][$key] = $data;
            }
        }

        return $records;
    }

    /**
     * Processing into statistical data with records.
     *
     * @param  string   $module
     * @param  array    $charts
     * @access public
     * @return array
     */
    public function getChartData($module, $charts)
    {
        $this->loadModel('workflowfield', 'flow');
        /* Need query modules array. */
        $chartModules = array();
        foreach($charts as $chart)
        {
            $chartModules[$chart->dimension->module][$chart->dimension->field] = $chart->dimension->field;
            if(isset($chart->fields) && is_array($chart->fields))
            {
                foreach($chart->fields as $field) $chartModules[$field->module][$field->field] = $field->field;
            }
        }

        /* Generate field of need modules ,include options、name...*/
        $fieldList = array();
        foreach($chartModules as $chartModule => $chartFields)
        {
            $fields = $this->workflowfield->getList($chartModule);
            foreach($fields as $field)
            {
                if(!isset($chartFields[$field->field])) continue;

                $fieldList[$chartModule][$field->field] = $field;
            }
        }

        /* Query and process data of child table. */
        $records = $this->getDataByCondition($module, $charts, $fieldList, $chartModules);
        foreach($fieldList as $fieldModule => $fields) $fields = $this->loadModel('workflowaction', 'flow')->processFields($fields, true, zget($records, $fieldModule, array()));

        $chartData = array();
        foreach($charts as $chart)
        {
            if(!isset($fieldList[$chart->dimension->module][$chart->dimension->field])) continue;

            $dimenField = $fieldList[$chart->dimension->module][$chart->dimension->field];

            $data = array();
            foreach($records as $recordModule => $moduleRecords)
            {
                $dimensionField = $recordModule == $module ? $chart->dimension->field : $chart->dimension->module . '_' . $chart->dimension->field;

                foreach($moduleRecords as $record)
                {
                    if(!isset($record->$dimensionField)) continue;

                    $dimension = $record->$dimensionField;

                    if(isset($dimenField->options))
                    {
                        if(is_array($dimension))
                        {
                            /* Show option value if field is multi-select or checkbox. */
                            $dimensionValues = array();
                            foreach($dimension as $value) $dimensionValues[] = zget($dimenField->options, $value);
                            $dimension = implode(',', $dimensionValues);
                        }
                        else
                        {
                            /* Show option value if field is select or radio. */
                            $dimension = zget($dimenField->options, $dimension);
                        }
                    }
                    /* Set dimension granularity if field is date or datetime. */
                    if($dimension && isset($chart->dimension->granularity)) $dimension = $this->processDimension($chart->dimension->granularity, $dimension);

                    if(!$dimension) $dimension = $this->lang->flow->notset;

                    /* Use it as key to count sum. */
                    if($chart->countType == 'count')
                    {
                        /* dimension module == main table module and dimension == record module. */
                        if($chart->dimension->module == $module)
                        {
                            if($module != $recordModule) continue;
                        }
                        else
                        {
                            if($chart->dimension->module != $recordModule) continue;
                        }

                        if(isset($data[$dimension]))
                        {
                            $data[$dimension]++;
                        }
                        else
                        {
                            $data[$dimension] = 1;
                        }
                    }
                    else
                    {
                        /* Total statistics. */
                        foreach($chart->fields as $field)
                        {
                            /* Set default value of no dimension in data when dimension is main table and fields is sub table. */
                            if($dimension != $this->lang->flow->notset && !isset($data[$dimension][$field->field])) $data[$dimension][$field->field] = 0;

                            /* When have sub tables, skip useless fields in this record. */
                            if($chart->dimension->module == $module && $field->module == $module && $recordModule != $module) continue;

                            $fieldKey = $recordModule == $module ? $field->field : $field->module . '_' . $field->field;
                            if(!isset($record->$fieldKey)) continue;

                            if(isset($data[$dimension][$field->field]))
                            {
                                $data[$dimension][$field->field] += (float)$record->$fieldKey;
                            }
                            else
                            {
                                $data[$dimension][$field->field] = (float)$record->$fieldKey;
                            }
                        }
                    }
                }
            }

            /* Generate default value for no result of the dimension. */
            if($chart->countType == 'sum')
            {
                foreach($data as $dimension => $fieldValue)
                {
                    foreach($chart->fields as $field)
                    {
                        if(!isset($data[$dimension][$field->field])) $data[$dimension][$field->field] = 0;
                    }
                }
            }

            /* Count percent. */
            if($chart->displayType == 'percent') $data = $this->countPercent($chart->countType, $data);

            ksort($data);

            $dataKey = 'chart-' . $chart->id;
            $chartData[$dataKey] = $this->formatDataByType($chart, $data, $fieldList);
        }

        return $chartData;
    }

    /**
     * Process dimension if dimension is date or datetime.
     *
     * @param  string $granularity
     * @param  string $dimension
     * @access public
     * @return void
     */
    public function processDimension($granularity, $dimension)
    {
        switch($granularity)
        {
            case 'year': return date('Y', strtotime($dimension));
            case 'month': return date('Y-m', strtotime($dimension));
            case 'week':
                $year       = date('Y', strtotime($dimension));
                $week       = date('W', strtotime($dimension));
                $clientLang = $this->app->getClientLang();

                if($clientLang == 'zh-cn' or $clientLang == 'zh-tw') return sprintf($this->lang->flow->reportGranularity['week'], $year, $week);
                return sprintf($this->lang->flow->reportGranularity['week'], $week, $year);
            case 'day': return date('Y-m-d', strtotime($dimension));
            case 'quarter':
                $year  = date('Y', strtotime($dimension));
                $month = date('m', strtotime($dimension));

                if($month < 4) return sprintf($this->lang->flow->reportGranularity['Q1'], $year);
                if($month < 7) return sprintf($this->lang->flow->reportGranularity['Q2'], $year);
                if($month < 10) return sprintf($this->lang->flow->reportGranularity['Q3'], $year);
                return sprintf($this->lang->flow->reportGranularity['Q4'], $year);
        }

        return $dimension;
    }

    /**
     * Count percent.
     *
     * @param  string   $countType
     * @param  array    $data
     * @access public
     * @return array
     */
    public function countPercent($countType, $data)
    {
        if($countType == 'count')
        {
            $sum = array_sum($data);
            foreach($data as $dimension => $count) $data[$dimension] = $sum ? round($count / $sum * 100, 2) : 0;
        }

        if($countType == 'sum')
        {
			/* Count sum. */
			$sum = array();
			foreach($data as $fieldValues)
			{
				foreach($fieldValues as $field => $value) $sum[$field] = isset($sum[$field]) ? $sum[$field] + $value : $value ;
			}

			/* Count percent. */
            foreach($data as $dimension => $fieldValues)
            {
                foreach($fieldValues as $field => $value) $data[$dimension][$field] = $sum[$field] ? round($value / $sum[$field] * 100, 2) : 0;
            }
        }

        return $data;
    }

    /**
     * Format datas by chart type.
     *
     * @param  array    $chart
     * @param  array    $datas
     * @param  array    $fieldList
     * @access public
     * @return array | object
     */
    public function formatDataByType($chart, $datas, $fieldList)
    {
        if($chart->type == 'pie')
        {
            $chartDatas = array();
            foreach($datas as $label => $data)
            {
                $chartData = new stdClass();
                $chartData->label = $label;
                $chartData->value = $chart->countType == 'count' ? $data : reset($data);
                $chartDatas[] = $chartData;
            }

            return $chartDatas;
        }
        else
        {
            $chartData = new stdClass();
            $chartData->labels   = array_keys($datas);
            $chartData->datasets = array();

            if($chart->countType == 'count')
            {
				$dataset = new stdClass();
				$dataset->data = array_values($datas);
                $chartData->datasets[] = $dataset;
                return $chartData;
            }

            $datasets = array();
            foreach($chart->fields as $field)
            {
                $field = $fieldList[$field->module][$field->field];

                $data = new stdclass();
                $data->label = $field->name;

                $datasets[$field->field] = $data;
            }
            foreach($datas as $fieldsData)
            {
                foreach($fieldsData as $field => $value)
                {
                    $datasets[$field]->data[] = $value;
                }
            }

            $chartData->datasets = array_values($datasets);

            return $chartData;
        }
    }
}