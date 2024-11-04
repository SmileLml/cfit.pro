<?php
helper::import(dirname(__FILE__) . DS . 'zentaobiz.class.php');
class zentaomaxUpgrade extends zentaobizUpgrade
{
    /**
     * Extends execute method for zentaomax.
     *
     * @param  string $fromVersion
     * @access public
     * @return bool
     */
    public function execute($fromVersion)
    {
        $this->maxFromVersion = $fromVersion;
        $maxInstalled = strpos($fromVersion, 'max') !== false;

        if(!$maxInstalled)
        {
            if(strpos($fromVersion, 'biz') === false && strpos($fromVersion, 'pro') === false)
            {
                $basicModel = new upgradeModel();
                $basicModel->execute($fromVersion);
                $this->execSQL($this->getUpgradeFile('bizinstall'));
                $this->execSQL($this->getUpgradeFile('proinstall'));
                $this->loadModel('effort')->convertEstToEffort();
                if(!empty($this->config->isINT))
                {
                    $xuanxuanSql = $this->app->getAppRoot() . 'db' . DS . 'xuanxuan.sql';
                    $this->execSQL($xuanxuanSql);
                }
                parent::importBuildinModules();
                parent::addSubStatus();
            }
            else
            {
                $zentaoVersion = $fromVersion;
                if(strpos($fromVersion, 'biz') !== false)
                {
                    $zentaoVersion = empty($this->config->bizVersion[$fromVersion]) ? $fromVersion : $this->config->bizVersion[$fromVersion];
                }
                elseif(strpos($fromVersion, 'pro') !== false)
                {
                    $zentaoVersion = empty($this->config->proVersion[$fromVersion]) ? $fromVersion : $this->config->proVersion[$fromVersion];
                }

                $proVersion = array_search($zentaoVersion, $this->config->proVersion);
                $bizVersion = array_search($zentaoVersion, $this->config->bizVersion);

                if(strpos($fromVersion, 'biz') === false)
                {
                    $this->session->set('step', 'pro');
                    parent::execute($proVersion);
                }
                else
                {
                    $this->session->set('step', 'biz');
                    parent::execute($bizVersion);
                }
            }

            $this->upgrade2Max($fromVersion);
        }
        else
        {
            if($fromVersion == 'max2_0_beta4')
            {
                $this->saveLogs("Execute $fromVersion");
                $this->execSQL($this->getUpgradeFile('max2.0.beta4'));
            }
            if($fromVersion == 'max2_0_beta4' and $this->config->version != 'max2.0.rc1') $fromVersion = 'max2_0_rc1';

            $this->maxFromVersion = $fromVersion;
            $this->session->set('maxInstalled', strpos($fromVersion, 'max') !== false);

            $bizVersion = $fromVersion;
            if($this->session->maxInstalled)
            {
                $this->session->set('bizInstalled', true);
                $zentaoVersion = empty($this->config->maxVersion[$fromVersion]) ? $fromVersion : $this->config->maxVersion[$fromVersion];
                $bizVersion    = array_search($zentaoVersion, $this->config->bizVersion);
                if(empty($bizVersion)) $bizVersion = $zentaoVersion;
            }

            $this->session->set('step', 'biz');
            if(strpos($bizVersion, 'max') !== 0) parent::execute($bizVersion);

            $this->session->set('step', 'max');
        }

        return true;
    }

    public function appendExec($zentaoVersion)
    {
        parent::appendExec($zentaoVersion);
        if(!$this->session->maxInstalled) return false;

        static $zentaoAndMaxPairs;
        if(empty($zentaoAndMaxPairs))
        {
            foreach($this->config->maxVersion as $maxVersion => $zentaoV) $zentaoAndMaxPairs[$zentaoV][] = $maxVersion;
        }

        if(isset($zentaoAndBizPairs[$zentaoVersion]))
        {
            $maxVersions = $zentaoAndMaxPairs[$zentaoVersion];
            foreach($maxVersions as $maxVersion)
            {
                if(version_compare(str_replace('_', '.', $this->maxFromVersion), str_replace('_', '.', $maxVersion)) > 0) continue;

                $this->saveLogs("Execute $maxVersion");
                $this->execSQL($this->getUpgradeFile(str_replace('_', '.', $maxVersion)));
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
        $confirmContent = '';

        $maxInstalled = strpos($fromVersion, 'max') !== false;
        if(!$maxInstalled)
        {
            if(strpos($fromVersion, 'biz') === false && strpos($fromVersion, 'pro') === false)
            {
                $basicModel      = new upgradeModel();
                $confirmContent .= $basicModel->getConfirm($fromVersion);
                $confirmContent .= file_get_contents($this->getUpgradeFile('proinstall'));
                $confirmContent .= file_get_contents($this->getUpgradeFile('bizinstall'));
            }
            else
            {
                $zentaoVersion = $fromVersion;
                if(strpos($fromVersion, 'biz') !== false)
                {
                    $zentaoVersion = empty($this->config->bizVersion[$fromVersion]) ? $fromVersion : $this->config->bizVersion[$fromVersion];
                }
                elseif(strpos($fromVersion, 'pro') !== false)
                {
                    $zentaoVersion = empty($this->config->proVersion[$fromVersion]) ? $fromVersion : $this->config->proVersion[$fromVersion];
                }

                $proVersion = array_search($zentaoVersion, $this->config->proVersion);
                $bizVersion = array_search($zentaoVersion, $this->config->bizVersion);

                if(strpos($fromVersion, 'biz') === false)
                {
                    $this->session->set('step', 'pro');
                    $confirmContent .= parent::getConfirm($proVersion);
                }

                $this->session->set('step', 'biz');
                $confirmContent .= parent::getConfirm($bizVersion);
            }

            $confirmContent .= file_get_contents($this->getUpgradeFile('maxinstall'));
            $confirmContent .= file_get_contents($this->getUpgradeFile('functions'));
        }
        else
        {
            if($fromVersion == 'max2_0_beta4') $confirmContent .= file_get_contents($this->getUpgradeFile('max2.0.beta4'));
            if($fromVersion == 'max2_0_beta4' && $this->config->version != 'max2.0.rc1') $fromVersion = 'max2_0_rc1';

            $zentaoVersion = $this->config->maxVersion[$fromVersion];
            $bizVersion = array_search($zentaoVersion, $this->config->bizVersion);

            $this->session->set('step', 'biz');
            $confirmContent .= parent::getConfirm($bizVersion);

            $this->session->set('step', 'max');
            switch($fromVersion)
            {
            case 'max2_0_rc1':
            }
        }

        return str_replace('zt_', $this->config->db->prefix, $confirmContent);
    }

    /**
     * Upgrade to zentaomax.
     *
     * @access public
     * @return bool
     */
    public function upgrade2Max()
    {
        $this->saveLogs("Execute for upgrade to max");

        set_time_limit(0);
        $this->execSQL($this->getUpgradeFile('maxinstall'));
        $this->execSQL($this->getUpgradeFile('functions'));
        $this->adjustBudgetUnit();

        return true;
    }
}
