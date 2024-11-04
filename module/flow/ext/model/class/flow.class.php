<?php
class flowFlow extends flowModel
{
    /**
     * Construct for get not empty rule.
     *
     * @param  string $appName
     * @access public
     * @return void
     */
    public function __construct($appName = '')
    {
        parent::__construct($appName);
        $this->notEmptyRule = $this->loadModel('workflowrule')->getByTypeAndRule('system', 'notempty');
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
        if($flow->navigator == 'primary' && isset($this->lang->{$flow->module}->menu)) $this->lang->flow->menu = $this->lang->{$flow->module}->menu;
        if($flow->navigator == 'secondary' && isset($this->lang->{$flow->app}->menu))  $this->lang->flow->menu = $this->lang->{$flow->app}->menu;
        return false;
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
        $result = parent::post($flow, $action, $dataID, $prevModule);

        if(isset($result['result']) and $result['result'] == 'success' && $action->open == 'modal' && helper::isAjaxRequest())
        {
            $result['locate'] = 'reload';
        }
        elseif(isset($result['result']) and $result['result'] == 'success' && $flow->buildin == 1)
        {
            if($dataID > 0)
            {
                $result['locate'] = helper::createLink($flow->module, 'view', "id={$dataID}");
            }
            elseif($flow->module == 'story' or $flow->module == 'task')
            {
                $locate = $flow->module == 'story' ? helper::createLink('product', 'browse') : helper::createLink('project', 'browse');
            }
        }

        return $result;
    }

    /**
     * Print workflow defined fields for view and form page.
     *
     * @param  string $moduleName
     * @param  string $methodName
     * @param  object $object       bug | build | feedback | product | productplan | project | release | story | task | testcase | testsuite | testtask
     * @param  string $type         The parent component which fileds displayed in. It should be table or div.
     * @param  string $extras       The extra params.
     *                              columns=1|2|3|5         Number of the columns merged to display the fields. The default is 1.
     *                              position=left|right     The position which the fields displayed in a page.
     *                              inForm=0|1              The fields displayed in a form or not. The default is 1.
     *                              inCell=0|1              The fields displayed in a div with class cell or not. The default is 0.
     * @access public
     * @return string
     */
    public function printFields($moduleName, $methodName, $object, $type, $extras = '')
    {
        $action = $this->loadModel('workflowaction')->getByModuleAndAction($moduleName, $methodName);
        if(empty($action) or $action->extensionType == 'none') return null;

        parse_str(str_replace(array(',', ' '), array('&', ''), $extras), $params);

        $moreLinks = $this->config->moreLinks;
        $function  = "printFieldsIn" . $type;

        $fields  = $this->workflowaction->getFields($moduleName, $methodName);
        $layouts = $this->loadModel('workflowlayout')->getFields($moduleName, $methodName);

        $html = '';
        if($layouts)
        {
            foreach($this->config->moreLinks as $fieldName => $moreLink)
            {
                if(isset($layouts[$fieldName])) continue;
                if(isset($moreLinks[$fieldName]))
                {
                    $this->config->moreLinks[$fieldName] = $moreLinks[$fieldName];
                    continue;
                }

                unset($this->config->moreLinks[$fieldName]);
            }

            $allFields = $this->dao->select('*')->from(TABLE_WORKFLOWFIELD)->where('module')->eq($moduleName)->fetchAll('field');
            foreach($fields as $fieldName => $field)
            {
                if(isset($allFields[$fieldName])) $field->default = $allFields[$fieldName]->default;
            }

            $html .= $this->$function($object, $layouts, $fields, $params);
        }

        $html .= $this->getFormulaScript($moduleName, $action, $fields);
        if($action->linkages) $html .= $this->getLinkageScript($action, $fields);

        return $html;
    }

    /**
     * Print fields in table.
     *
     * @param  object   $object
     * @param  array    $layouts
     * @param  array    $fields
     * @param  array    $params
     * @access public
     * @return string
     */
    public function printFieldsInTable($object, $layouts, $fields, $params = '')
    {
        $html    = '';
        $columns = zget($params, 'columns', 1);
        $inForm  = zget($params, 'inForm', 1);
        $colspan = $columns > 1 ? "colspan='$columns'" : '';

        if(!is_object($object)) $object = (object)$object;
        if(!$object) $object = new stdclass();

        foreach($fields as $field)
        {
            if($field->buildin or !$field->show or !isset($layouts[$field->field])) continue;

            if($field->default and empty($field->defaultValue)) $field->defaultValue = $field->default;
            if(empty($object->{$field->field})) $object->{$field->field} = $field->defaultValue;

            $require = '';
            if($inForm && !$field->readonly && $this->notEmptyRule && strpos(",$field->rules,", ",{$this->notEmptyRule->id},") !== false) $require = "class='required'";

            if(($field->control == 'textarea' or $field->control == 'richtext') and empty($colspan) and $inForm) $colspan = "colspan='2'";

            $content = $inForm ? $this->getFieldControl($field, $object) : $this->getFieldValue($field, $object);
            $html .= "<tr><th>{$field->name}</th>";
            $html .= "<td $colspan $require>{$content}</td></tr>";
        }

        return $html;
    }

    /**
     * Print fields in div.
     *
     * @param  object $object
     * @param  array  $layouts
     * @param  array  $fields
     * @param  array  $params
     * @access public
     * @return string
     */
    public function printFieldsInDiv($object, $layouts, $fields, $params = '')
    {
        $html     = '';
        $position = zget($params, 'position', 'right');
        $inCell   = zget($params, 'inCell', 0);
        $inForm   = zget($params, 'inForm', 1);

        if($position == 'right')
        {
            if($inCell) $html .= "<div class='cell'>";

            $html .= "<div class='detail'>";
            $html .= "<div class='detail-title'>{$this->lang->extInfo}</div>";
            $html .= $inCell ? "<table class='table table-data'>" : "<table class='table table-form'>";

            $tableContent = '';
            foreach($fields as $field)
            {
                if($field->buildin or !$field->show or !isset($layouts[$field->field]) or $field->position != 'basic') continue;

                $require = '';
                if($inForm && !$field->readonly && $this->notEmptyRule && strpos(",$field->rules,", ",{$this->notEmptyRule->id},") !== false) $require = "class='required'";

                $content = $inForm ? $this->getFieldControl($field, $object) : $this->getFieldValue($field, $object);

                $tableContent .= "<tr><th class='thWidth'>{$field->name}</th>";
                $tableContent .= "<td $require>{$content}</td></tr>";
            }

            if(!$tableContent) return false;

            $html .= $tableContent;
            $html .= '</table>';
            $html .= '</div>';

            if($inCell) $html .= '</div>';
        }

        if($position == 'left')
        {
            foreach($fields as $field)
            {
                if($field->buildin or !$field->show or !isset($layouts[$field->field]) or $field->position != 'info') continue;

                $require = '';
                if($inForm && !$field->readonly && $this->notEmptyRule && strpos(",$field->rules,", ",{$this->notEmptyRule->id},") !== false) $require = "required";

                $content = $inForm ? $this->getFieldControl($field, $object) : $this->getFieldValue($field, $object);

                if($inCell) $html .= "<div class='cell'>";

                $html .= "<div class='detail'>";
                $html .= "<div class='detail-title'>{$field->name}</div>";
                $html .= "<div class='detail-content $require'>{$content}</div>";
                $html .= '</div>';

                if($inCell) $html .= '</div>';
            }
        }

        return $html;
    }

    /**
     * Get control of a field.
     *
     * @param  object    $field
     * @param  object    $object
     * @access public
     * @return string
     */
    public function getFieldControl($field, $object, $controlName = '')
    {
        $control  = '';
        $readonly = $field->readonly ? 'disabled' : '';

        if($field->control == 'checkbox' or $field->control == 'radio' and isset($field->options[''])) unset($field->options['']);

        if($field->control == 'input')        $control = html::input($controlName    ? $controlName : $field->field, $object ? $object->{$field->field} : '', "class='form-control' $readonly");
        if($field->control == 'decimal')      $control = html::input($controlName    ? $controlName : $field->field, $object ? $object->{$field->field} : '', "class='form-control' $readonly");
        if($field->control == 'integer')      $control = html::input($controlName    ? $controlName : $field->field, $object ? $object->{$field->field} : '', "class='form-control' $readonly");
        if($field->control == 'formula')      $control = html::input($controlName    ? $controlName : $field->field, $object ? $object->{$field->field} : '', "class='form-control' $readonly");
        if($field->control == 'select')       $control = html::select($controlName   ? $controlName : $field->field, $field->options, $object ? $object->{$field->field} : '', "class='form-control chosen' $readonly");
        if($field->control == 'multi-select') $control = html::select($controlName   ? $controlName : $field->field . '[]', $field->options, $object ? $object->{$field->field} : '', "class='form-control chosen' multiple $readonly");
        if($field->control == 'checkbox')     $control = html::checkbox($controlName ? $controlName : $field->field, $field->options, $object ? $object->{$field->field} : '', "class='form-control' $readonly");
        if($field->control == 'radio')        $control = html::radio($controlName    ? $controlName : $field->field, $field->options, $object ? $object->{$field->field} : '', "$readonly");
        if($field->control == 'date')         $control = html::input($controlName    ? $controlName : $field->field, $object ? $object->{$field->field} : '', "class='form-control form-date' $readonly");
        if($field->control == 'datetime')     $control = html::input($controlName    ? $controlName : $field->field, $object ? $object->{$field->field} : '', "class='form-control form-datetime' $readonly");
        if($field->control == 'time')         $control = html::input($controlName    ? $controlName : $field->field, $object ? $object->{$field->field} : '', "class='form-control form-time' $readonly");
        if($field->control == 'richtext' or $field->control == 'textarea')
        {
            $object  = $this->loadModel('file')->replaceImgURL($object, $field->field);
            $control = html::textarea($controlName ? $controlName : $field->field, $object ? $object->{$field->field} : '', "class='form-control' $readonly");
            if($readonly) $control = $object ? $object->{$field->field} : '';
        }

        if($field->field == 'subStatus')  $control .= $this->getSubStatusScript($field);

        return $control;
    }

    /**
     * Get value string of one field.
     *
     * @param  object    $field
     * @param  object    $object
     * @access public
     * @return string
     */
    public function getFieldValue($field, $object)
    {
        if($field->control == 'richtext' or $field->control == 'textarea')
        {
            $object = $this->loadModel('file')->replaceImgURL($object, $field->field);
            return $object->{$field->field};
        }
        if($field->control != 'checkbox' and $field->control != 'multi-select') return zget($field->options, $object->{$field->field}, $object->{$field->field});

        $content = '';
        $values  = json_decode($object->{$field->field}, true);
        if(empty($values)) $values = explode(',', str_replace(' ', '', $object->{$field->field}));
        if(!is_array($values)) $values = array($values);
        foreach($values as $value) $content .= ' ' . zget($field->options, $value, $value);

        return $content;
    }

    /**
     * Print workflow defined fields from browse page.
     *
     * @param string $module
     * @param object $object
     * @param string $fieldCode
     * @access public
     * @return void
     */
    public function printFlowCell($module, $object, $fieldCode)
    {
        static $fields  = array();
        static $options = array();

        $fieldKey = $module . '_' . $fieldCode;

        if(isset($fields[$fieldKey]))
        {
            $field = $fields[$fieldKey];
        }
        else
        {
            $field = $this->loadModel('workflowfield')->getByField($module, $fieldCode);

            $fields[$fieldKey] = $field;
        }

        if(isset($field->buildin) && $field->buildin == 0)
        {
            if(strpos('select,radio,checkbox,multi-select', $field->control) === false)
            {
                echo $object->{$field->field};
            }
            else
            {
                if(isset($options[$fieldKey]))
                {
                    $field->options = $options[$fieldKey];
                }
                else
                {
                    $field->options = $this->loadModel('workflowfield')->getFieldOptions($field);

                    $options[$fieldKey] = $field->options;
                }

                if($field->control == 'multi-select' or $field->control == 'checkbox')
                {
                    foreach(explode(',', $object->{$field->field}) as $fieldKey) echo zget($field->options, $fieldKey) . ' ';
                }
                else
                {
                    echo zget($field->options, $object->{$field->field});
                }
            }
        }
    }

    /**
     * Import from excel
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

        $this->loadModel('workflowrule');
        $fields = $this->loadModel('workflowfield')->getExportFields($flow->module);
        $rules  = array();
        foreach($fields as $field => $fieldName)
        {
            $fields[$field] = $this->workflowfield->getByField($flow->module, $field);

            if(empty($fields[$field]->rules)) continue;

            $fieldRules = explode(',', trim($fields[$field]->rules, ','));
            $fieldRules = array_unique($fieldRules);
            foreach($fieldRules as $ruleID)
            {
                if(empty($ruleID)) continue;

                $rule = $this->workflowrule->getByID($ruleID);
                if(empty($rule)) continue;

                $rules[$ruleID] = $rule;
            }
        }

        /* Check rules. */
        foreach($dataList as $i => $data)
        {
            foreach($fields as $fieldKey => $field)
            {
                if(isset($data[$fieldKey]) and is_array($data[$fieldKey])) $data[$fieldKey] = join(',', $data[$fieldKey]);
                if(!isset($data[$fieldKey]) and strpos(',radio,checkbox,multi-select,', ",$field->control,") !== false) $data[$fieldKey] = '';

                if(empty($field->rules)) continue;

                $fieldRules = explode(',', trim($field->rules, ','));
                $fieldRules = array_unique($fieldRules);
                foreach($fieldRules as $ruleID)
                {
                    $rule = $rules[$ruleID];
                    if($rule->type == 'system')
                    {
                        $functionName = 'check' . $rule->rule;
                        if(!validater::$functionName($data[$fieldKey]))
                        {
                            $this->dao->logError($rule->rule, $fieldKey, $field->name);
                            foreach(dao::$errors[$fieldKey] as $error)
                            {
                                $errorKey = 'dataList' . $i . $fieldKey;
                                if(!isset(dao::$errors[$errorKey])) dao::$errors[$errorKey] = '';
                                dao::$errors[$errorKey] .= $error;
                            }
                            unset(dao::$errors[$fieldKey]);
                        }
                    }
                    elseif($rule->type == 'regex' and !validater::checkREG($data[$fieldKey], $rule->rule))
                    {
                        $errorKey = 'dataList' . $i . $fieldKey;
                        dao::$errors[$errorKey] = sprintf($this->lang->error->reg, $field->name, $rule->rule);
                    }
                }
            }

            $dataList[$i] = $data;
        }
        if(dao::isError()) return array('result' => 'fail', 'message' => dao::getError());

        $subTables = $this->dao->select('module, `table`')->from(TABLE_WORKFLOW)
            ->where('type')->eq('table')
            ->andWhere('parent')->eq($flow->module)
            ->fetchPairs();

        /* 导入数据。*/
        foreach($dataList as $key => $data)
        {
            if(!empty($this->post->dataList[$key]['id']) and empty($_POST['insert']))
            {
                $this->dao->update($flow->table)->data($data, 'sub_tables')->where('id')->eq($this->post->dataList[$key]['id'])->exec();

                $dataID   = $this->post->dataList[$key]['id'];
                $actionID = $this->action->create($flow->module, $dataID, 'edited');
            }
            else
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
            }

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
     * Get extend fields.
     *
     * @param  string $moduleName
     * @param  string $methodName
     * @access public
     * @return array
     */
    public function getExtendFields($moduleName, $methodName)
    {
        $action = $this->loadModel('workflowaction')->getByModuleAndAction($moduleName, $methodName);
        if(empty($action) or $action->extensionType == 'none') return array();

        $fields  = $this->workflowaction->getFields($moduleName, $methodName);
        $layouts = $this->loadModel('workflowlayout')->getFields($moduleName, $methodName);

        $extendFields = array();
        foreach($fields as $field)
        {
            if($field->buildin or !$field->show or !isset($layouts[$field->field])) continue;
            $extendFields[] = $field;
        }

        return $extendFields;
    }

    /**
     * Check flow field rule.
     *
     * @param  object $field
     * @param  string $value
     * @access public
     * @return false|string
     */
    public function checkRule($field, $value)
    {
        $rules = trim($field->rules, ',');
        if(empty($rules)) return false;

        $rules = $this->dao->select('*')->from(TABLE_WORKFLOWRULE)->where('id')->in($rules)->orderBy('id_desc')->fetchAll('id');
        foreach($rules as $rule)
        {
            if($rule->type == 'system' and $rule->rule == 'notempty')
            {
                if($value === '') return sprintf($this->lang->error->notempty, $field->name);
                if(strpos($field->type, 'int') !== false and $field->control == 'select' and empty($value)) return sprintf($this->lang->error->notempty, $field->name);
            }
            elseif($rule->type == 'system')
            {
                $checkFunc = 'check' . $rule->rule;
                if(!validater::$checkFunc($value))
                {
                    $error = zget($this->lang->error, $rule->rule, '');
                    if($error) $error = sprintf($error, $field->name);
                    if(empty($error)) $error = sprintf($this->lang->error->reg, $field->name, $rule->rule);

                    return $error;
                }
            }
            elseif($rule->type == 'regex')
            {
                if(!validater::checkREG($value, $rule->rule)) return sprintf($this->lang->error->reg, $field->name, $rule->rule);
            }
        }

        return false;
    }

    public function buildControl($field, $fieldValue, $element = '', $childModule = '', $emptyValue = false, $preview = false)
    {
        if(!empty($fieldValue))
        {
            if($field->control == 'date' && !helper::isZeroDate($fieldValue)) $fieldValue = date('Y-m-d', strtotime($fieldValue));
            if($field->control == 'datetime' && !helper::isZeroDate($fieldValue)) $fieldValue = date('Y-m-d H:i:s', strtotime($fieldValue));
            if($field->control == 'select' or $field->control == 'multi-select' or $field->control == 'checkbox' or $field->control == 'radio')
            {
                if(is_array($fieldValue)) $fieldValue = join(',', $fieldValue);

                $decodedFieldValue = json_decode($fieldValue);
                if(empty($decodedFieldValue)) $decodedFieldValue = explode(',', $fieldValue);
                if(!is_array($decodedFieldValue)) $decodedFieldValue = array($decodedFieldValue);
                $fieldValue = $decodedFieldValue;

                $options = $this->loadModel('workflowfield')->getFieldOptions($field);
                foreach($fieldValue as $i => $value)
                {
                    $fieldKey = array_search($value, $options);
                    if($fieldKey) $fieldValue[$i] = $fieldKey;
                }

                $fieldValue = join(',', $fieldValue);
            }
        }
        if($field->control == 'multi-select' and $element) $element .= '[]';

        return parent::buildControl($field, $fieldValue, $element, $childModule, $emptyValue, $preview);
    }

    public function getDataByID($flow, $dataID, $decode = true)
    {
        $data = parent::getDataByID($flow, $dataID, $decode);
        if(!$decode) return $data;

        $table  = $flow->table;
        $fields = $this->loadModel('workflowfield')->getList($flow->module);

        $tableData = $this->dao->select('*')->from($table)->where('id')->eq($dataID)->fetch();
        if($tableData)
        {
            foreach($fields as $field)
            {
                $fieldControl = $field->control;
                $fieldName    = $field->field;
                if($decode and ($fieldControl == 'multi-select' or $fieldControl == 'checkbox') and $tableData->$fieldName and empty($data->$fieldName))
                {
                    $data->$fieldName = explode(',', str_replace(' ', '', $tableData->$fieldName));
                }
            }
        }

        return $data;
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

        if($type != 'menu' && $relations && $data)
        {
            $relationMenu = '';
            $flowPairs    = $this->loadModel('workflow', 'flow')->getPairs();

            $nextModules = array();
            foreach($relations as $relation) $nextModules[$relation->next] = $relation->next;
            $nextActions = $this->dao->select('module,action,buildin,extensionType')->from(TABLE_WORKFLOWACTION)->where('module')->in($nextModules)->andWhere('action')->in('create,batchcreate')->fetchGroup('module', 'action');

            /* Build entrance of other flows by the relations. */
            foreach($relations as $relation)
            {
                if(strpos(",$relation->actions,", ',one2one,') !== false)
                {
                    if(!commonModel::hasPriv($relation->next, 'create')) continue;
                    if(!isset($nextActions[$relation->next])) continue;
                    if(!isset($nextActions[$relation->next]['create'])) continue;

                    $actionName = $this->lang->workflowaction->default->actions['create'] . zget($flowPairs, $relation->next);
                    $actionLink = helper::createLink($relation->next, 'create', "step=form&prevModule=$flow->module&prevDataID={$data->id}");

                    $createAction = $nextActions[$relation->next]['create'];
                    if($createAction->buildin and $createAction->extensionType != 'override') $actionLink = $this->createBuildinLink($createAction->module, $flow->module, $data->id);

                    if($isMobile)  $relationMenu .= "<a data-remote='{$actionLink}' data-display='modal' data-placement='bottom'>{$actionName}</a>";
                    if(!$isMobile) $relationMenu .= baseHTML::a($actionLink, $actionName, "class='$btn'");
                }
                if(strpos(",$relation->actions,", ',one2many,') !== false)
                {
                    if(!commonModel::hasPriv($relation->next, 'batchcreate')) continue;
                    if(!isset($nextActions[$relation->next])) continue;
                    if(!isset($nextActions[$relation->next]['batchcreate'])) continue;

                    $batchCreateAction = $nextActions[$relation->next]['batchcreate'];
                    if($batchCreateAction->buildin and $batchCreateAction->extensionType != 'override') continue;

                    $actionName = $this->lang->workflowaction->default->actions['create'] . zget($flowPairs, $relation->next);
                    $actionLink = helper::createLink($relation->next, 'batchcreate', "step=form&prevModule=$flow->module&prevDataID={$data->id}");
                    if($isMobile)  $relationMenu .= "<a data-remote='{$actionLink}' data-display='modal' data-placement='bottom'>{$actionName}</a>";
                    if(!$isMobile) $relationMenu .= baseHTML::a($actionLink, $actionName, "class='$btn'");
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
        $deleteMenu   = '';

        $actions = $this->workflowaction->getList($flow->module);
        foreach($actions as $action)
        {
            if($action->buildin) continue;
            if($action->action == 'browse') continue;
            if($action->action == 'export') continue;
            if($action->action == 'exporttemplate') continue;
            if($action->action == 'import') continue;
            if($action->action == 'showimport') continue;
            if($action->action == 'link') continue;
            if($action->action == 'unlink') continue;
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

                if($action->action == 'delete')
                {
                    if(!$isMobile) $deleteMenu .= $link;
                    if($isMobile)  $deleteMenu .=  "<a data-remote='$url' data-display='ajaxAction' data-ajax-delete='true' data-locate=''>{$this->lang->delete}</a>";
                }
                else
                {
                    if($type == 'browse' && $action->show == 'dropdownlist')
                    {
                        $dropdownMenu .= "<li>" . $link . "</li>";
                    }
                    else
                    {
                        if($isMobile)
                        {
                            if($reload)  $menu .= "<a data-remote='{$url}' data-display='ajaxAction' data-locate='self'>{$label}</a>";
                            if(!$reload) $menu .= "<a data-remote='{$url}' data-display='modal' data-placement='bottom'>{$label}</a>";
                        }
                        else
                        {
                            $menu .= $link;
                        }
                    }
                }
            }
        }

        if($type == 'browse' && $dropdownMenu != '')
        {
            $dropdownBefore  = "<div class='dropdown'><a href='javascript:;' data-toggle='dropdown'>{$this->lang->more}<span class='caret'> </span></a>";
            $dropdownBefore .= "<ul class='dropdown-menu pull-right'>";
            $dropdownMenu    = $dropdownBefore . $dropdownMenu . "<li>" . $deleteMenu . "</li></ul></div>";
        }
        else
        {
            $dropdownMenu .= $deleteMenu;
        }

        $menu .= $dropdownMenu;

        if($type == 'view' && !$isMobile && !$flow->buildin) $menu .= '</div></div>';

        return $menu;
    }

    /**
     * Create buildin module link
     *
     * @param  string $moduleName
     * @param  string $prevModule
     * @param  int    $prevDataID
     * @access public
     * @return string
     */
    public function createBuildinLink($moduleName, $prevModule, $prevDataID)
    {
        $params = '';
        $extras = "prevModule=$prevModule,prevDataID=$prevDataID";

        if($moduleName == 'story')    $params = "productID=0&branch=0&moduleID=0&storyID=0&projectID=0&bugID=0&planID=0&todoID=0&extra=$extras";
        if($moduleName == 'task')     $params = "projectID=0&storyID=0&moduleID=0&taskID=0&extras=$extras";
        if($moduleName == 'bug')      $params = "productID=0&branch=&extras=$extras";
        if($moduleName == 'testcase') $params = "productID=0&branch=&moduleID=0&from=&param=0&storyID=0&extras=$extras";
        if($moduleName == 'feedback') $params = "extras=$extras";

        return helper::createLink($moduleName, 'create', $params);
    }

    public function getDataList($flow, $mode = 'browse', $label = 0, $categoryQuery = '', $parentID = 0, $orderBy = '', $pager = null, $extraQuery = '')
    {
        $querySessionName = $flow->module . 'Query';
        if($this->session->$querySessionName == false) $this->session->set($querySessionName, ' 1 = 1');
        $searchQuery = $this->loadModel('search')->replaceDynamic($this->session->$querySessionName);

        $labelQuery = '';
        if($label)
        {
            if($mode == 'bysearch')
            {
                $query = $this->search->getQuery($label);
                $searchQuery  = $query->sql;
                $labelOrderBy = '';
                $this->session->set($flow->module . 'Form', $query->form);
            }
            else
            {
                list($labelQuery, $labelOrderBy) = $this->getLabelQueryAndOrderBy($label);
            }

            if(!$orderBy) $orderBy = $labelOrderBy;
        }

        if(!$orderBy) $orderBy = 'id_desc';

        $productRelatedModules = ",productplan,release,story,build,bug,testcase,testtask,testsuite,feedback,";
        $dataList = $this->dao->select('*')->from($flow->table)
            ->where('deleted')->eq('0')
            ->beginIF(!$flow->buildin && $parentID)->andWhere('parent')->eq($parentID)->fi()
            ->beginIF($mode == 'bysearch')->andWhere($searchQuery)->fi()
            ->beginIF($labelQuery)->andWhere($labelQuery)->fi()
            ->beginIF($categoryQuery)->andWhere($categoryQuery)->fi()
            ->beginIF($extraQuery)->andWhere($extraQuery)->fi()
            ->beginIF($flow->module == 'product')->andWhere('id')->in($this->app->user->view->products)->fi()
            ->beginIF($flow->module == 'project')->andWhere('id')->in($this->app->user->view->projects)->fi()
            ->beginIF($flow->module == 'execution')->andWhere('id')->in($this->app->user->view->sprints)->fi()
            ->beginIF($flow->module == 'task')->andWhere('execution')->in($this->app->user->view->sprints)->fi()
            ->beginIF($flow->module == 'caselib')->andWhere('product')->eq('0')->fi()
            ->beginIF(strpos($productRelatedModules, ',' . $flow->module . ',') !== false)->andWhere('product')->in($this->app->user->view->products)->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');

        $this->session->set($flow->module . 'QueryCondition', $this->dao->get());

        foreach($dataList as $data) $data = $this->processDBData($flow->module, $data);

        return $dataList;
    }

    public function processDBData($module, $data, $decode = true)
    {
        static $fields = array();
        if(empty($fields[$module])) $fields[$module] = $this->loadModel('workflowfield', 'flow')->getControlPairs($module);

        $editorFields = array();
        $this->loadModel('file');

        foreach($fields[$module] as $field => $control)
        {
            if($decode && $control == 'multi-select' or $control == 'checkbox')
            {
                $decodedValue = json_decode($data->{$field});
                if(empty($decodedValue)) $decodedValue = explode(',', $data->{$field});
                $data->$field = $decodedValue;
            }

            if($control == 'date' or $control == 'datetime') $data->$field = formatTime($data->$field);
            if($control == 'richtext') $editorFields[] = $field;
        }
        if($editorFields) $data = $this->file->replaceImgURL($data, $editorFields);

        return $data;
    }

    public function sendNotice($flow, $action, $result)
    {
        parent::sendNotice($flow, $action, $result);

        $this->loadModel('action');
        $this->loadModel('webhook');
        $method = $action->action;
        $this->lang->action->label->$method = $action->name;
        $this->config->objectTables[$flow->module] = $flow->table;

        $nameFields = $this->dao->select('field')->from(TABLE_WORKFLOWFIELD)->where('module')->eq($flow->module)->andWhere('field')->in('name,title')->fetchPairs('field', 'field');
        if(isset($nameFields['title'])) $this->config->action->objectNameFields[$flow->module] = 'title';
        if(isset($nameFields['name']))  $this->config->action->objectNameFields[$flow->module] = 'name';
        if(!isset($this->config->action->objectNameFields[$flow->module]))  $this->config->action->objectNameFields[$flow->module] = 'id';

        if($action->type == 'single' and !empty($result['recordID']) and !empty($result['actionID']))
        {
            $this->webhook->send($flow->module, $result['recordID'], $method, $result['actionID']);
        }
        elseif($action->type != 'single' and !empty($result['recordList']) and !empty($result['actionList']))
        {
            if($action->batchMode == 'same')
            {
                foreach($result['recordList'] as $key => $dataID)
                {
                    if(!empty($dataID) && !empty($result['actionList'][$key])) $this->webhook->send($flow, $dataID, $method, $result['actionList'][$key]);
                }
            }
            else
            {
                foreach($result['recordList'] as $key => $dataID)
                {
                    if(empty($dataID) or empty($result['actionList'][$key])) continue;
                    $this->webhook->send($flow, $dataID, $method, $result['actionList'][$key]);
                }
            }
        }
    }

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
}

function formatMoney($money, $unit = 1)
{
    if($money === 0) return '';

    $decimals    = 2;
    $formatMoney = number_format((float)$money / $unit, $decimals);

    /* If the formated money is too small, change decimals. */
    if($money > 0 && (float)$formatMoney == 0)
    {
        $decimals    = ceil(log10($unit));
        $formatMoney = number_format($money / $unit, $decimals);
    }

    return trim(preg_replace('/\.0*$/', '', $formatMoney));
}
