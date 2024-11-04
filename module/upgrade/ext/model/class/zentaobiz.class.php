<?php
helper::import(dirname(__FILE__) . DS . 'bizext.class.php');
class zentaobizUpgrade extends bizextUpgrade
{
    /**
     * Extends execute method for zentaobiz.
     *
     * @param  string $fromVersion
     * @access public
     * @return bool
     */
    public function execute($fromVersion)
    {
        $this->bizFromVersion = $fromVersion;
        if(strpos($fromVersion, 'biz') === false)
        {
            $zentaobiz = $this->dao->select('*')->from(TABLE_EXTENSION)->where('code')->like('zentaobiz%')->andWhere('status')->eq('installed')->fetch();
            if(!empty($zentaobiz)) $fromVersion = 'biz' . str_replace('.', '_', $zentaobiz->version);
        }
        $this->session->set('bizInstalled', strpos($fromVersion, 'biz') !== false);

        $proVersion = $fromVersion;
        if($this->session->bizInstalled)
        {
            $this->session->set('proInstalled', true);
            $zentaoVersion = empty($this->config->bizVersion[$fromVersion]) ? $fromVersion : $this->config->bizVersion[$fromVersion];
            $proVersion    = array_search($zentaoVersion, $this->config->proVersion);
            if(empty($proVersion)) $proVersion = $zentaoVersion;
        }

        $this->session->set('step', 'pro');
        parent::execute($proVersion);

        $this->session->set('step', 'biz');
        if(!$this->session->bizInstalled) $this->upgrade2Biz();

        $this->dao->delete()->from(TABLE_EXTENSION)->where('code')->like('zentaobiz%')->exec();
        return true;
    }

    /**
     * Extend appendExec method.
     *
     * @param  string $zentaoVersion
     * @access public
     * @return void
     */
    public function appendExec($zentaoVersion)
    {
        parent::appendExec($zentaoVersion);
        if(!$this->session->bizInstalled) return false;

        static $zentaoAndBizPairs;
        if(empty($zentaoAndBizPairs))
        {
            foreach($this->config->bizVersion as $bizVersion => $zentaoV) $zentaoAndBizPairs[$zentaoV][] = $bizVersion;
        }
        static $executeXuanxuan = false;

        if(isset($zentaoAndBizPairs[$zentaoVersion]))
        {
            $bizVersions = $zentaoAndBizPairs[$zentaoVersion];
            foreach($bizVersions as $bizVersion)
            {
                if(version_compare(str_replace('_', '.', $this->bizFromVersion), str_replace('_', '.', $bizVersion)) > 0) continue;

                $this->saveLogs("Execute $bizVersion");
                $this->execSQL($this->getUpgradeFile(str_replace('_', '.', $bizVersion)));
                switch($bizVersion)
                {
                case 'biz2_0_beta':
                    $executeXuanxuan = true;
                    break;
                case 'biz2_3_1':
                    $this->adjustFeedbackViewData();
                    break;
                case 'biz3_0':
                    if(!empty($this->config->isINT) and !$executeXuanxuan)
                    {
                        $xuanxuanSql = $this->app->getAppRoot() . 'db' . DS . 'upgradexuanxuan2.3.0.sql';
                        $this->execSQL($xuanxuanSql);
                        $this->dao->update(TABLE_CONFIG)->set('value')->eq('off')->where('`key`')->eq('isHttps')->andWhere('`section`')->eq('xuanxuan')->andWhere('`value`')->eq('0')->exec();
                        $this->dao->update(TABLE_CONFIG)->set('value')->eq('on')->where('`key`')->eq('isHttps')->andWhere('`section`')->eq('xuanxuan')->andWhere('`value`')->eq('1')->exec();
                    }
                    break;
                case 'biz3_2_1':
                    if(!empty($this->config->isINT))
                    {
                        if(!$executeXuanxuan)
                        {
                            $xuanxuanSql = $this->app->getAppRoot() . 'db' . DS . 'upgradexuanxuan2.4.0.sql';
                            $this->execSQL($xuanxuanSql);
                            $xuanxuanSql = $this->app->getAppRoot() . 'db' . DS . 'upgradexuanxuan2.5.0.sql';
                            $this->execSQL($xuanxuanSql);
                        }
                        $this->updateXX_11_5();
                    }
                    break;
                case 'biz3_4':
                    $this->importBuildinModules();
                    break;
                case 'biz3_5_alpha':
                    $this->addSubStatus();
                case 'biz3_5_beta':
                    $this->processSubTables();
                    break;
                case 'biz3_6':
                    $this->addDefaultActions();
                    $this->importCaseLibModule();
                    $this->deleteBuildinFields();
                    break;
                case 'biz3_6_1':
                    $this->addWorkflowActions();
                    $this->processWorkflowLayout();
                    $this->processWorkflowLabel();
                    $this->processWorkflowCondition();
                    if(!empty($this->config->isINT) and !$executeXuanxuan)
                    {
                        $xuanxuanSql = $this->app->getAppRoot() . 'db' . DS . 'upgradexuanxuan3.1.1.sql';
                        $this->execSQL($xuanxuanSql);
                    }
                    break;
                case 'biz3_7':
                    $this->processWorkflowFields();
                    break;
                case 'biz3_7_2':
                    $this->processFlowStatus();
                    break;
                case 'biz4_0_1':
                    $this->addMailtoFields();
                    break;
                case 'biz4_0_3':
                    $this->updateAttendStatus();
                    $this->initView4WorkflowDatasource();
                    break;
                }
            }
        }
    }

    /**
     * Extends getConfirm method for zentaobiz.
     *
     * @param  string $fromVersion
     * @access public
     * @return string
     */
    public function getConfirm($fromVersion)
    {
        if(strpos($fromVersion, 'biz') === false)
        {
            $zentaobiz = $this->dao->select('*')->from(TABLE_EXTENSION)->where('code')->like('zentaobiz%')->andWhere('status')->eq('installed')->fetch();
            if(!empty($zentaobiz)) $fromVersion = 'biz' . str_replace('.', '_', $zentaobiz->version);
        }

        $proVersion = $fromVersion;
        if(strpos($fromVersion, 'biz') !== false)
        {
            $zentaoVersion = empty($this->config->bizVersion[$fromVersion]) ? $fromVersion : $this->config->bizVersion[$fromVersion];
            $proVersion    = array_search($zentaoVersion, $this->config->proVersion);
        }

        $confirmContent = '';
        $this->session->set('step', 'pro');
        if(!empty($proVersion)) $confirmContent .= parent::getConfirm($proVersion);

        $this->session->set('step', 'biz');
        if(strpos($fromVersion, 'biz') === false) $confirmContent .= file_get_contents($this->getUpgradeFile('bizinstall'));

        switch($fromVersion)
        {
        case 'biz1_0': $confirmContent .= file_get_contents($this->getUpgradeFile('biz1.0'));
        case 'biz1_1':
        case 'biz1_1_1':
        case 'biz1_1_2':
        case 'biz1_1_3':
        case 'biz1_1_4':
        case 'biz2_0_beta':
        case 'biz2_1':
        case 'biz2_2': $confirmContent .= file_get_contents($this->getUpgradeFile('biz2.2'));
        case 'biz2_3':
        case 'biz2_3_1': $confirmContent .= file_get_contents($this->getUpgradeFile('biz2.3.1'));
        case 'biz2_4': $confirmContent .= file_get_contents($this->getUpgradeFile('biz2.4'));
        case 'biz3_0':
            if(!empty($this->config->isINT))
            {
                $xuanxuanSql     = $this->app->getAppRoot() . 'db' . DS . 'upgradexuanxuan2.3.0.sql';
                $confirmContent .= file_get_contents($xuanxuanSql);
            }
        case 'biz3_1':
        case 'biz3_2': $confirmContent .= file_get_contents($this->getUpgradeFile('biz3.2'));
        case 'biz3_2_1':
        case 'biz3_3':       $confirmContent .= file_get_contents($this->getUpgradeFile('biz3.3'));
        case 'biz3_4':       $confirmContent .= file_get_contents($this->getUpgradeFile('biz3.4'));
        case 'biz3_5_alpha': $confirmContent .= file_get_contents($this->getUpgradeFile('biz3.5.alpha'));
        case 'biz3_5_beta':  $confirmContent .= file_get_contents($this->getUpgradeFile('biz3.5.beta'));
        case 'biz3_5':
        case 'biz3_5_1':
        case 'biz3_6':
        case 'biz3_6_1': $confirmContent .= file_get_contents($this->getUpgradeFile('biz3.6.1'));
        case 'biz3_7':   $confirmContent .= file_get_contents($this->getUpgradeFile('biz3.7'));
        case 'biz3_7_1': $confirmContent .= file_get_contents($this->getUpgradeFile('biz3.7.1'));
        case 'biz3_7_2': $confirmContent .= file_get_contents($this->getUpgradeFile('biz3.7.2'));
        case 'biz4_0':   $confirmContent .= file_get_contents($this->getUpgradeFile('biz4.0'));
        case 'biz4_0_1': $confirmContent .= file_get_contents($this->getUpgradeFile('biz4.0.1'));
        case 'biz4_0_2': $confirmContent .= file_get_contents($this->getUpgradeFile('biz4.0.2'));
        case 'biz4_0_3': $confirmContent .= file_get_contents($this->getUpgradeFile('biz4.0.3'));
        case 'biz4_0_4': $confirmContent .= file_get_contents($this->getUpgradeFile('biz4.0.4'));
        case 'biz4_1':
        case 'biz4_1_1':
        case 'biz4_1_2':
        case 'biz4_1_3': $confirmContent .= file_get_contents($this->getUpgradeFile('biz4.1.3'));
        case 'biz5_0_rc1': $confirmContent .= file_get_contents($this->getUpgradeFile('biz5.0.rc1'));
        }

        return str_replace('zt_', $this->config->db->prefix, $confirmContent);
    }

    /**
     * Upgrade to zentaobiz.
     *
     * @access public
     * @return void
     */
    public function upgrade2Biz()
    {
        $this->execSQL($this->getUpgradeFile('bizinstall'));
        if(!empty($this->config->isINT))
        {
            $xuanxuanSql = $this->app->getAppRoot() . 'db' . DS . 'xuanxuan.sql';
            $this->execSQL($xuanxuanSql);
        }
        $this->importBuildinModules();
        $this->addSubStatus();
    }

    /**
     * Adjust feedback view data.
     *
     * @access public
     * @return bool
     */
    public function adjustFeedbackViewData()
    {
        $this->saveLogs('Run Method ' . __FUNCTION__);
        $desc = $this->dao->query('DESC ' . TABLE_FEEDBACKVIEW)->fetchAll();
        $this->saveLogs($this->dao->get());
        $hasProductsField = false;
        foreach($desc as $field)
        {
            if($field->Field == 'products') $hasProductsField = true;
        }
        if(!$hasProductsField) return true;

        $feedbackView = $this->dao->select('account, products')->from(TABLE_FEEDBACKVIEW)->fetchPairs();
        $this->dao->delete()->from(TABLE_FEEDBACKVIEW)->exec();
        $this->saveLogs($this->dao->get());
        foreach($feedbackView as $account => $products)
        {
            if(empty($products)) continue;

            foreach(explode(',', $products) as $productID)
            {
                $productID = trim($productID);
                if(empty($productID)) continue;

                $view = new stdclass();
                $view->account = $account;
                $view->product = $productID;
                $this->dao->replace(TABLE_FEEDBACKVIEW)->data($view)->exec();
                $this->saveLogs($this->dao->get());
            }
        }
        $this->dao->exec("ALTER TABLE " . TABLE_FEEDBACKVIEW . " DROP `products`");
        $this->saveLogs($this->dao->get());
        return true;
    }

    /**
     * Import zentao module for workflow.
     *
     * @access public
     * @return void
     */
    public function importBuildinModules()
    {
        $this->saveLogs('Run Method ' . __FUNCTION__);
        $this->loadModel('workflow');
        $this->loadModel('workflowaction');
        $this->loadModel('workflowfield');
        $this->loadModel('workflowlayout');

        $modules       = $this->config->workflow->buildin->modules;
        $actions       = $this->config->workflowaction->buildin->actions;
        $actionOpens   = $this->config->workflowaction->buildin->opens;
        $actionLayouts = $this->config->workflowaction->buildin->layouts;
        $fields        = $this->config->workflowfield->buildin->fields;
        $layouts       = $this->config->workflowlayout->buildin->layouts;

        $account = $this->app->user->account;
        $now     = helper::now();

        /* Insert buildin modules to TABLE_WORKFLOW. */
        $data = new stdclass();
        $data->buildin     = 1;
        $data->createdBy   = $account;
        $data->createdDate = $now;
        $data->status      = 'normal';
        foreach($modules as $app => $appModules)
        {
            $data->app = $app;
            foreach($appModules as $module => $options)
            {
                $this->app->loadLang($module);

                $data->module    = $module;
                $data->name      = isset($this->lang->$module->common) ? $this->lang->$module->common : $module;
                $data->table     = str_replace('`', '', zget($options, 'table', ''));
                $data->navigator = zget($options, 'navigator', 'secondary');

                $this->dao->replace(TABLE_WORKFLOW)->data($data)->exec();
            }
        }

        /* Insert actions of buildin modules to TABLE_WORKFLOWACTION. */
        $data = new stdclass();
        $data->buildin       = 1;
        $data->extensionType = 'none';
        $data->createdBy     = $account;
        $data->createdDate   = $now;
        foreach($actions as $module => $moduleActions)
        {
            $data->module = $module;
            foreach($moduleActions as $action)
            {
                $data->action = $action;

                /* Use default action name if not set flow action name. */
                $name = '';
                if(isset($this->lang->$module->$action)) $name = $this->lang->$module->$action;
                if(empty($name) && in_array($action, array_keys($this->config->flowAction)))
                {
                    $methodName = $this->config->flowAction[$action];
                    if(isset($this->lang->$module->$methodName)) $name = $this->lang->$module->$methodName;
                }
                if(empty($name) && isset($this->lang->workflowaction->default->actions[$action])) $name = $this->lang->workflowaction->default->actions[$action];
                if(empty($name)) $name = $action;
                $data->name   = $name;

                $data->open   = isset($actionOpens[$module][$action]) ? $actionOpens[$module][$action] : 'normal';
                $data->layout = isset($actionLayouts[$module][$action]) ? $actionLayouts[$module][$action] : 'normal';

                $this->dao->replace(TABLE_WORKFLOWACTION)->data($data)->exec();
            }
        }

        /* Insert fields of buildin modules to TABLE_WORKFLOWFIELD. */
        $data = new stdclass();
        $data->createdBy   = $account;
        $data->createdDate = $now;
        foreach($fields as $module => $moduleFields)
        {
            $order = 1;
            $data->module = $module;
            foreach($moduleFields as $field => $options)
            {
                $data->field    = $field;
                $data->name     = isset($this->lang->$module->$field) ? $this->lang->$module->$field : $field;
                $data->type     = zget($options, 'type', 'varchar');
                $data->length   = zget($options, 'length', '');
                $data->control  = zget($options, 'control', 'input');
                $data->options  = zget($options, 'options', '[]');
                $data->default  = zget($options, 'default', '');
                $data->buildin  = zget($options, 'buildin', 1);
                $data->order    = $order++;
                $data->readonly = ($field == 'subStatus') ? '0' : '1';

                if(is_object($data->options) or is_array($data->options)) $data->options = helper::jsonEncode($data->options);

                $this->dao->replace(TABLE_WORKFLOWFIELD)->data($data)->exec();
            }
        }

        /* Insert layouts of buildin modules to TABLE_WORKFLOWLAYOUT. */
        $data = new stdclass();
        foreach($layouts as $module => $moduleLayouts)
        {
            $data->module = $module;
            foreach($moduleLayouts as $action => $layoutFields)
            {
                $order = 1;
                $data->action = $action;
                foreach($layoutFields as $field => $options)
                {
                    $data->field      = $field;
                    $data->width      = zget($options, 'width', 0);
                    $data->mobileShow = zget($options, 'mobileShow', 0);
                    $data->order      = $order++;

                    $this->dao->replace(TABLE_WORKFLOWLAYOUT)->data($data)->exec();
                }
            }
        }

        /* Insert labels of buildin modules to TABLE_WORKFLOWLABEL. */
        $data = new stdclass();
        $data->buildin     = 1;
        $data->params      = '[]';
        $data->createdBy   = $account;
        $data->createdDate = $now;
        foreach($modules as $app => $appModules)
        {
            foreach($appModules as $module => $options)
            {
                $labels = array();
                if($module == 'product')
                {
                    if(isset($this->lang->product->featureBar['all'])) $labels = $this->lang->product->featureBar['all'];
                }
                elseif($module == 'story')
                {
                    if(isset($this->lang->product->featureBar['browse'])) $labels = $this->lang->product->featureBar['browse'];
                }
                elseif($module == 'execution')
                {
                    if(isset($this->lang->execution->featureBar['all'])) $labels = $this->lang->execution->featureBar['all'];
                }
                elseif($module == 'task')
                {
                    if(isset($this->lang->execution->featureBar['task'])) $labels = $this->lang->execution->featureBar['task'];
                }
                elseif($module == 'bug')
                {
                    if(isset($this->lang->bug->featureBar['browse'])) $labels = $this->lang->bug->featureBar['browse'];
                }
                elseif($module == 'feedback')
                {
                    if(isset($this->lang->feedback->menu) && (is_object($this->lang->feedback->menu) or is_array($this->lang->feedback->menu)))
                    {
                        foreach($this->lang->feedback->menu as $key => $menuItem)
                        {
                            $menus = explode('|', zget($menuItem, 'link', $menuItem));
                            $labels[$key] = zget($menus, 0, $menuItem);
                        }
                    }
                }
                else
                {
                    if(isset($this->lang->$module->featureBar['browse'])) $labels = $this->lang->$module->featureBar['browse'];
                }

                $order = 1;
                $data->module = $module;
                foreach($labels as $key => $label)
                {
                    $data->code  = $key;
                    $data->label = trim(strip_tags($label));
                    $data->order = $order++;

                    $this->dao->replace(TABLE_WORKFLOWLABEL)->data($data)->exec();
                }
            }
        }
    }

    /**
     * Add sub status for built-in modules.
     *
     * @access public
     * @return bool
     */
    public function addSubStatus()
    {
        $this->saveLogs('Run Method ' . __FUNCTION__);
        $this->app->loadModuleConfig('workflow');
        $modules = $this->config->workflow->buildin->subStatus->modules;

        $statusOrders = $this->dao->select('module, `order`')->from(TABLE_WORKFLOWFIELD)
            ->where('field')->eq('status')
            ->andWhere('module')->in($modules)
            ->fetchPairs();

        $field = new stdclass();
        $field->field    = 'subStatus';
        $field->type     = 'varchar';
        $field->length   = 30;
        $field->control  = 'select';
        $field->buildin  = 0;
        $field->options  = '[]';

        foreach($modules as $module)
        {
            $this->app->loadLang($module);

            $order = $statusOrders[$module] + 1;

            $field->module = $module;
            $field->name   = $this->lang->$module->subStatus;
            $field->order  = $order;

            $this->dao->replace(TABLE_WORKFLOWFIELD)->data($field)->exec();
            $this->dao->update(TABLE_WORKFLOWFIELD)->set('`order` = `order` + 1')
                ->where('module')->eq($module)
                ->andWhere('`order`')->gt($order)
                ->exec();
        }

        return !dao::isError();
    }

    /**
     * Add batch create action and batch edit action for exist flows.
     *
     * @access public
     * @return bool
     */
    public function addDefaultActions()
    {
        $this->saveLogs('Run Method ' . __FUNCTION__);
        $this->loadModel('workflowaction');

        $actionLang     = $this->lang->workflowaction->default;
        $actionConfig   = $this->config->workflowaction->default;
        $defaultActions = $this->config->workflowaction->defaultActions;
        $flows          = $this->dao->select('module,buildin')->from(TABLE_WORKFLOW)->where('type')->eq('flow')->fetchPairs('module', 'buildin');

        $action = new stdclass();
        $action->show        = 'direct';
        $action->createdBy   = $this->app->user->account;
        $action->createdDate = helper::now();

        foreach($flows as $module => $buildin)
        {
            if($buildin) continue;

            $action->module = $module;
            foreach($defaultActions as $actionCode)
            {
                $action->action    = $actionCode;
                $action->name      = $actionLang->actions[$actionCode];
                $action->type      = $actionConfig->types[$actionCode];
                $action->batchMode = $actionConfig->batchModes[$actionCode];
                $action->open      = $actionConfig->opens[$actionCode];
                $action->position  = $actionConfig->positions[$actionCode];

                $this->dao->replace(TABLE_WORKFLOWACTION)->data($action)->autoCheck()->exec();
            }
        }

        return !dao::isError();
    }

    /**
     * process sub tables.
     *
     * @access public
     * @return void
     */
    public function processSubTables()
    {
        $this->saveLogs('Run Method ' . __FUNCTION__);
        $subTables  = $this->dao->select('parent, module')->from(TABLE_WORKFLOW)
            ->where('parent')->ne('')
            ->andWhere('type')->eq('table')
            ->fetchPairs();

        foreach($subTables as $parent => $module)
        {
            $this->dao->update(TABLE_WORKFLOWLAYOUT)
                ->set('field')->eq('sub_' . $module)
                ->where('module')->eq($parent)
                ->andWhere('field')->eq($module)
                ->exec();
        }

        return !dao::isError();
    }

    /**
     * Import caselib module for workflow.
     *
     * @access public
     * @return void
     */
    public function importCaseLibModule()
    {
        $this->saveLogs('Run Method ' . __FUNCTION__);
        $this->loadModel('workflow');
        $this->loadModel('workflowaction');
        $this->loadModel('workflowfield');
        $this->loadModel('workflowlayout');

        $caselibOptions = $this->config->workflow->buildin->modules->qa->caselib;
        $actions        = $this->config->workflowaction->buildin->actions['caselib'];
        $actionOpens    = $this->config->workflowaction->buildin->opens['caselib'];
        $fields         = $this->config->workflowfield->buildin->fields['caselib'];
        $layouts        = $this->config->workflowlayout->buildin->layouts->caselib;

        $account = $this->app->user->account;
        $now     = helper::now();

        /* Insert buildin modules to TABLE_WORKFLOW. */
        $this->app->loadLang('caselib');
        $data = new stdclass();
        $data->buildin     = 1;
        $data->createdBy   = $account;
        $data->createdDate = $now;
        $data->app         = 'qa';
        $data->module      = 'caselib';
        $data->name        = isset($this->lang->caselib->common) ? $this->lang->caselib->common : 'caselib';
        $data->table       = str_replace('`', '', zget($caselibOptions, 'table', ''));
        $data->navigator   = zget($caselibOptions, 'navigator', 'secondary');
        $this->dao->replace(TABLE_WORKFLOW)->data($data)->exec();

        /* Insert actions of buildin modules to TABLE_WORKFLOWACTION. */
        $data = new stdclass();
        $data->buildin       = 1;
        $data->extensionType = 'none';
        $data->createdBy     = $account;
        $data->createdDate   = $now;
        $data->module        = 'caselib';
        foreach($actions as $action)
        {
            $data->action = $action;
            $data->name   = isset($this->lang->caselib->$action) ? $this->lang->caselib->$action : $action;
            $data->open   = isset($actionOpens['caselib'][$action]) ? $actionOpens['caselib'][$action] : 'normal';
            $data->layout = 'normal';

            $this->dao->replace(TABLE_WORKFLOWACTION)->data($data)->exec();
        }

        /* Insert fields of buildin modules to TABLE_WORKFLOWFIELD. */
        $data = new stdclass();
        $data->createdBy   = $account;
        $data->createdDate = $now;
        $data->module      = 'caselib';

        $order = 1;
        foreach($fields as $field => $options)
        {
            $data->field    = $field;
            $data->name     = isset($this->lang->caselib->$field) ? $this->lang->caselib->$field : $field;
            $data->type     = zget($options, 'type', 'varchar');
            $data->length   = zget($options, 'length', '');
            $data->control  = zget($options, 'control', 'input');
            $data->options  = zget($options, 'options', '[]');
            $data->default  = zget($options, 'default', '');
            $data->buildin  = zget($options, 'buildin', 1);
            $data->order    = $order++;
            $data->readonly = ($field == 'subStatus') ? '0' : '1';

            if(is_object($data->options) or is_array($data->options)) $data->options = helper::jsonEncode($data->options);

            $this->dao->replace(TABLE_WORKFLOWFIELD)->data($data)->exec();
        }

        /* Insert layouts of buildin modules to TABLE_WORKFLOWLAYOUT. */
        $data = new stdclass();
        $data->module = 'caselib';
        foreach($layouts as $action => $layoutFields)
        {
            $order = 1;
            $data->action = $action;
            foreach($layoutFields as $field => $options)
            {
                $data->field      = $field;
                $data->width      = zget($options, 'width', 0);
                $data->mobileShow = zget($options, 'mobileShow', 0);
                $data->order      = $order++;

                $this->dao->replace(TABLE_WORKFLOWLAYOUT)->data($data)->exec();
            }
        }
    }

    /**
     * Delete buildin fields.
     *
     * @access public
     * @return void
     */
    public function deleteBuildinFields()
    {
        $this->saveLogs('Run Method ' . __FUNCTION__);
        $deleteIdList = $this->dao->select('t1.id')->from(TABLE_WORKFLOWLAYOUT)->alias('t1')
            ->leftJoin(TABLE_WORKFLOWFIELD)->alias('t2')->on('t1.field=t2.field && t1.module = t2.module')
            ->leftJoin(TABLE_WORKFLOWACTION)->alias('t3')->on('t1.action=t3.action && t1.module = t3.module')
            ->where('t2.buildin')->eq(1)
            ->andWhere('t3.extensionType')->eq('extend')
            ->fetchPairs('id', 'id');
        $this->dao->delete()->from(TABLE_WORKFLOWLAYOUT)->where('id')->in($deleteIdList)->exec();
    }

    /**
     * Add new actions for exist flows.
     *
     * @access public
     * @return bool
     */
    public function addWorkflowActions()
    {
        $this->saveLogs('Run Method ' . __FUNCTION__);
        $this->loadModel('workflowaction');

        $newActions   = array('batchcreate', 'batchedit', 'link', 'unlink', 'exporttemplate', 'import', 'showimport');
        $actionLang   = $this->lang->workflowaction->default;
        $actionConfig = $this->config->workflowaction->default;
        $flows        = $this->dao->select('module,buildin')->from(TABLE_WORKFLOW)->where('type')->eq('flow')->fetchPairs('module', 'buildin');

        $action = new stdclass();
        $action->show        = 'direct';
        $action->createdBy   = $this->app->user->account;
        $action->createdDate = helper::now();

        foreach($flows as $module => $buildin)
        {
            $action->module = $module;

            foreach($newActions as $actionCode)
            {
                if($buildin and !in_array($actionCode, zget($this->config->workflowaction->buildin->actions, $module, array()))) continue;

                $open = $actionConfig->opens[$actionCode];
                if($buildin and isset($this->config->workflowaction->buildin->opens[$module][$actionCode])) $open = $this->config->workflowaction->buildin->opens[$module][$actionCode];

                $action->action        = $actionCode;
                $action->name          = $actionLang->actions[$actionCode];
                $action->type          = $actionConfig->types[$actionCode];
                $action->batchMode     = $actionConfig->batchModes[$actionCode];
                $action->open          = $open;
                $action->position      = $actionConfig->positions[$actionCode];
                $action->buildin       = $buildin;
                $action->layout        = 'normal';
                $action->extensionType = $buildin ? 'none' : 'override';

                $this->dao->replace(TABLE_WORKFLOWACTION)->data($action)->autoCheck()->exec();
            }
        }

        $this->dao->update(TABLE_WORKFLOWACTION)->set('name')->eq($actionLang->actions['export'])->where('action')->eq('export')->exec();

        return !dao::isError();
    }

    /**
     * Process workflow layout.
     *
     * @access public
     * @return bool
     */
    public function processWorkflowLayout()
    {
        $this->saveLogs('Run Method ' . __FUNCTION__);
        $subTables  = $this->dao->select('parent, module')->from(TABLE_WORKFLOW)
            ->where('parent')->ne('')
            ->andWhere('type')->eq('table')
            ->fetchPairs();

        foreach($subTables as $parent => $module)
        {
            $this->dao->update(TABLE_WORKFLOWLAYOUT)
                ->set('field')->eq('sub_' . $module)
                ->where('module')->eq($parent)
                ->andWhere('field')->eq($module)
                ->exec();
        }

        return !dao::isError();
    }

    /**
     * Make sure the params of a label has the condition deleted='0'.
     *
     * @access public
     * @return bool
     */
    public function processWorkflowLabel()
    {
        $this->saveLogs('Run Method ' . __FUNCTION__);
        $labels = $this->dao->select('id, params')->from(TABLE_WORKFLOWLABEL)->where('module')->eq('test')->fetchPairs();
        foreach($labels as $id => $params)
        {
            $index   = 0;
            $deleted = false;
            $params  = json_decode($params, true);

            foreach($params as $index => $param)
            {
                if(zget($param, 'field') == 'deleted' && zget($param, 'operator') == '=' && zget($param, 'value') == '0')
                {
                    $deleted = true;
                    break;
                }
            }

            if(!$deleted)
            {
                $index++;

                $params[$index]['field']    = 'deleted';
                $params[$index]['operator'] = '=';
                $params[$index]['value']    = '0';

                $params = array_reverse($params);
            }

            $params = helper::jsonEncode($params);

            $this->dao->update(TABLE_WORKFLOWLABEL)->set('params')->eq($params)->where('id')->eq($id)->exec();
        }

        return !dao::isError();
    }

    /**
     * Process workflow conditions to array.
     *
     * @access public
     * @return bool
     */
    public function processWorkflowCondition()
    {
        $this->saveLogs('Run Method ' . __FUNCTION__);
        $actions = $this->dao->select('id, conditions')->from(TABLE_WORKFLOWACTION)->fetchPairs();

        foreach($actions as $id => $conditions)
        {
            $conditions = json_decode($conditions);
            if(!$conditions) continue;

            $conditions = helper::jsonEncode(array($conditions));

            $this->dao->update(TABLE_WORKFLOWACTION)->set('conditions')->eq($conditions)->where('id')->eq($id)->exec();
        }

        return !dao::isError();
    }

    /**
     * Process workflow fields.
     *
     * @access public
     * @return void
     */
    public function processWorkflowFields()
    {
        $sqls['id']           = "ALTER TABLE %s CHANGE `id`           `id`           mediumint(8) unsigned NOT NULL AUTO_INCREMENT";
        $sqls['parent']       = "ALTER TABLE %s CHANGE `parent`       `parent`       mediumint(8) unsigned NOT NULL";
        $sqls['assignedTo']   = "ALTER TABLE %s CHANGE `assignedTo`   `assignedTo`   varchar(30) NOT NULL";
        $sqls['status']       = "ALTER TABLE %s CHANGE `status`       `status`       varchar(30) NOT NULL";
        $sqls['subStatus']    = "ALTER TABLE %s CHANGE `subStatus`    `subStatus`    varchar(30) NOT NULL";
        $sqls['createdBy']    = "ALTER TABLE %s CHANGE `createdBy`    `createdBy`    varchar(30) NOT NULL";
        $sqls['createdDate']  = "ALTER TABLE %s CHANGE `createdDate`  `createdDate`  datetime NOT NULL";
        $sqls['editedBy']     = "ALTER TABLE %s CHANGE `editedBy`     `editedBy`     varchar(30) NOT NULL";
        $sqls['editedDate']   = "ALTER TABLE %s CHANGE `editedDate`   `editedDate`   datetime NOT NULL";
        $sqls['assignedBy']   = "ALTER TABLE %s CHANGE `assignedBy`   `assignedBy`   varchar(30) NOT NULL";
        $sqls['assignedDate'] = "ALTER TABLE %s CHANGE `assignedDate` `assignedDate` datetime NOT NULL";
        $sqls['deleted']      = "ALTER TABLE %s CHANGE `deleted`      `deleted`      enum('0', '1') NOT NULL DEFAULT '0'";

        $flows = $this->dao->select('module, `table`')->from(TABLE_WORKFLOW)
            ->where('type')->eq('flow')
            ->andWhere('buildin')->eq('0')
            ->orderBy('id_desc')
            ->fetchPairs();
        $fields = $this->dao->select('module, field, `default`')->from(TABLE_WORKFLOWFIELD)
            ->where('module')->in(array_keys($flows))
            ->fetchGroup('module', 'field');

        $magicQuote = (version_compare(phpversion(), '5.4', '<') and function_exists('get_magic_quotes_gpc') and get_magic_quotes_gpc());

        foreach($flows as $module => $table)
        {
            foreach($sqls as $field => $sql)
            {
                if(!isset($fields[$module][$field])) continue;

                $sql = sprintf($sql, $table);

                if($field == 'status' or $field == 'subStatus')
                {
                    $default = $fields[$module][$field]->default;
                    if($default)
                    {
                        if($magicQuote) $default = stripslashes($default);

                        $default = $this->dbh->quote($default);

                        $sql .= " DEFAULT {$default}";
                    }
                }

                try
                {
                    $this->dbh->query($sql);
                }
                catch(PDOException $e)
                {
                    self::$errors[] = $e->getMessage() . "<p>The sql is: $sql</p>";

                    return false;
                }
            }
        }

        return true;
    }

    public function processFlowStatus()
    {
        $this->loadModel('workflow', 'flow');

        $flowPairs = $this->dao->select('module')->from(TABLE_WORKFLOW)->where('type')->eq('flow')->andWhere('buildin')->eq(0)->fetchPairs();
        foreach($flowPairs as $module)
        {
            $errors = $this->workflow->checkFieldAndLayout($module);

            $status = !empty($errors) ? 'wait' : 'normal';
            $this->dao->update(TABLE_WORKFLOW)->set('status')->eq($status)->where('module')->eq($module)->exec();
        }

        return !dao::isError();
    }

    public function addMailtoFields()
    {
        $this->app->loadLang('workflowfield');

        $mailto = new stdclass();
        $mailto->field    = 'mailto';
        $mailto->control  = 'multi-select';
        $mailto->type     = 'text';
        $mailto->name     = $this->lang->workflowfield->default->fields['mailto'];
        $mailto->options  = 'user';
        $mailto->readonly = 1;

        $flows = $this->dao->select('module, `table`')->from(TABLE_WORKFLOW)->where('`type`')->eq('flow')->fetchPairs();
        foreach($flows as $module => $table)
        {
            $hasMailto = false;

            $fields = $this->dbh->query("DESC $table")->fetchAll();
            foreach($fields as $field)
            {
                if($field->Field == 'mailto')
                {
                    $hasMailto = true;
                    break;
                }
            }

            if(!$hasMailto)
            {
                $mailto->module = $module;

                $this->dao->insert(TABLE_WORKFLOWFIELD)->data($mailto)->autoCheck()->exec();

                if(dao::isError()) return false;

                try
                {
                    $sql = "ALTER TABLE `{$table}` ADD `mailto` text NOT NULL";
                    $this->dbh->query($sql);
                }
                catch(PDOException $exception)
                {
                    self::$errors[] = $exception->getMessage() . "<p>The sql is: $sql</p>";

                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Update attend status.
     *
     * @access public
     * @return bool
     */
    public function updateAttendStatus()
    {
        /* Get leave list. */
        $leaveList = $this->loadModel('leave')->getList('personal', '', '', '', '', 'pass');

        /* Cycle leave list to modify the attend status. */
        foreach($leaveList as $leave)
        {
            $this->dao->update(TABLE_ATTEND)
                ->set('status')->eq('leave')
                ->set('reason')->eq('leave')
                ->where('`date`')->between($leave->begin, $leave->end)
                ->andWhere('account')->eq($leave->createdBy)
                ->exec();
        }

        return true;
    }

    /**
     * Init view for workflow datasource.
     *
     * @access public
     * @return bool
     */
    public function initView4WorkflowDatasource()
    {
        $datasources = $this->dao->select('*')->from(TABLE_WORKFLOWDATASOURCE)->where('type')->eq('sql')->andWhere('view')->eq('')->fetchAll('id');
        foreach($datasources as $id => $datasource)
        {
            $options = array();

            try
            {
                $view = 'view_datasource_tmp';
                $sql  = "CREATE VIEW $view AS {$datasource->datasource}";
                $this->dbh->query($sql);

                $fields  = $this->dbh->query("DESC $view")->fetchAll();
                foreach($fields as $field) $options[$field->Field] = $field->Field;

                $sql = "DROP VIEW $view";
                $this->dbh->query($sql);
            }
            catch(PDOException $exception)
            {
            }

            try
            {
                $view       = "view_datasource_$id";
                $keyField   = array_shift($options);
                $valueField = array_shift($options);

                $sqls   = array();
                $sqls[] = "DROP VIEW IF EXISTS $view";
                $sqls[] = "CREATE VIEW $view AS $datasource->datasource";

                foreach($sqls as $sql) $this->dbh->query($sql);

                $this->dao->update(TABLE_WORKFLOWDATASOURCE)->set('view')->eq($view)->set('keyField')->eq($keyField)->set('valueField')->eq($valueField)->where('id')->eq($id)->exec();
            }
            catch(PDOException $exception)
            {
                dao::getError();
            }
        }

        return true;
    }
}
