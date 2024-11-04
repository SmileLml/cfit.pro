<?php
class flowCommon extends commonModel
{
    /**
     * Merge lang from flow.
     *
     * @access public
     * @return void
     */
    public function mergeLangFromFlow()
    {
        if(defined('IN_UPGRADE')) return;
        if(!$this->config->db->name) return;

        $this->app->setOpenApp();

        $rawModule  = $this->app->rawModule;
        $rawMethod  = $this->app->rawMethod;
        $openApp    = $this->app->openApp;

        $primaryFlows   = array();
        $secondaryFlows = array();
        $flows          = $this->dao->select('*')->from(TABLE_WORKFLOW)->where('buildin')->eq('0')->andWhere('status')->eq('normal')->orderBy('navigator_asc')->fetchAll('id');
        foreach($flows as $flow)
        {
            if($flow->navigator == 'primary') $primaryFlows[$flow->module] = $flow;
            if($flow->navigator == 'secondary' and $flow->app == $openApp) $secondaryFlows[$flow->module] = $flow;
        }

        $this->sortFlows($primaryFlows, 'primary');
        foreach($primaryFlows as $flow)
        {
            $this->lang->{$flow->module} = new stdclass();
            $this->lang->{$flow->module}->common  = $flow->name;
            $this->lang->mainNav->{$flow->module} = "{$this->lang->navIcons['workflow']} {$flow->name}|{$flow->module}|browse|";
            if($openApp == $flow->module)
            {
                $flowLabels = $this->dao->select('*')->from(TABLE_WORKFLOWLABEL)->where('module')->eq($flow->module)->orderBy('`order`')->fetchAll('id');
                foreach($flowLabels as $flowLabel)
                {
                    if(!isset($this->lang->{$flow->module})) $this->lang->{$flow->module} = new stdclass();
                    if(!isset($this->lang->{$flow->module}->menu)) $this->lang->{$flow->module}->menu = new stdclass();
                    $this->lang->{$flow->module}->menu->{$flowLabel->code} = array('link' => "{$flowLabel->label}|{$flowLabel->module}|browse|label={$flowLabel->id}");
                    $this->lang->{$flow->module}->menuOrder[$flowLabel->order] = $flowLabel->code;
                }
            }
        }

        $this->sortFlows($secondaryFlows, 'secondary');
        foreach($secondaryFlows as $flow)
        {
            $this->lang->{$flow->module} = new stdclass();
            $this->lang->{$flow->module}->common = $flow->name;
            $this->lang->{$flow->app}->menu->{$flow->module} = array('link' => "{$flow->name}|{$flow->module}|browse|");
        }

        if($this->app->isFlow)
        {
            if($openApp == 'product' or $openApp == 'qa')
            {
                $products  = $this->loadModel('product')->getPairs('nocode');
                $productID = $this->product->saveState(0, $products);
                $branch    = (int)$this->cookie->preBranch;
                if($openApp == 'product') $this->product->setMenu($productID, $branch, 0, '', $currentID ? $currentID : '');
                if($openApp == 'qa') $this->loadModel('qa')->setMenu($productID, $branch, $currentID ? $currentID : '');
            }
            elseif($openApp == 'project')
            {
                $projects  = $this->loadModel('project')->getExecutionPairs($this->session->project, 'nocode');
                $projectID = $this->project->saveState(0, $projects);
                $this->project->setMenu($projectID, 0, $currentID ? $currentID : '');
            }
        }
    }

    public function sortFlows($flows, $navigator = 'primary')
    {
        $openApp   = $this->app->openApp;
        $menuOrder = array();
        if($navigator == 'primary')
        {
            $menuOrder = $this->lang->mainNav->menuOrder;
        }
        elseif($navigator == 'secondary')
        {
            if($openApp == 'project')
            {
                $menuOrder = $this->lang->scrum->menuOrder;
            }
            elseif(isset($this->lang->{$openApp}->menuOrder))
            {
                $menuOrder = $this->lang->{$openApp}->menuOrder;
            }
        }
        if(empty($menuOrder)) return true;

        foreach($flows as $flow) $menuOrder[] = $flow->module;
        $menuOrderFlip = array_flip($menuOrder);

        foreach($flows as $flow) $menuOrderFlip = $this->computeMenuOrder($flow, $menuOrderFlip, $flows);

        $menuOrder = array_flip($menuOrderFlip);
        if($navigator == 'primary')
        {
            $this->lang->mainNav->menuOrder = $menuOrder;
        }
        elseif($navigator == 'secondary')
        {
            if($openApp == 'project')
            {
                $this->lang->scrum->menuOrder = $menuOrder;
            }
            elseif(isset($this->lang->{$openApp}->menuOrder))
            {
                $this->lang->{$openApp}->menuOrder = $menuOrder;
            }
        }

        return true;
    }

    public function computeMenuOrder($flow, $menuOrderFlip, $flows)
    {
        if(strpos($flow->position, 'before') !== false or strpos($flow->position, 'after') !== false)
        {
            $mode   = strpos($flow->position, 'before') !== false ? 'before' : 'after';
            $module = str_replace($mode, '', $flow->position);
            if(isset($flows[$module])) $menuOrderFlip = $this->computeMenuOrder($flows[$module], $menuOrderFlip, $flows);

            if(isset($menuOrderFlip[$module]))
            {
                $menuOrderFlip[$module] = $menuOrderFlip[$module] * 5;
                $order = $menuOrderFlip[$module];
                $order = $mode == 'before' ? (string)($order - 1) : (string)($order + 1);
                $menuOrderFlip[$flow->module] = $order;
            }
        }
        return $menuOrderFlip;
    }
}
