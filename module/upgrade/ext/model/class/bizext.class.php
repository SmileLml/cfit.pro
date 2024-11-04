<?php
class bizextUpgrade extends upgradeModel
{
    /**
     * Extends execute method for zentaopro.
     *
     * @param  string $fromVersion
     * @access public
     * @return bool
     */
    public function execute($fromVersion)
    {
        $this->proFromVersion = $fromVersion;

        /* Check bizext extension to set fromVersion.*/
        if(strpos($fromVersion, 'pro') === false)
        {
            $bizext = $this->dao->select('*')->from(TABLE_EXTENSION)->where('code')->like('bizext%')->andWhere('status')->eq('installed')->fetch();
            if(!empty($bizext)) $fromVersion = 'pro' . str_replace('.', '_', $bizext->version);
        }

        /* get open source zentao from version. */
        $zentaoVersion = empty($this->config->proVersion[$fromVersion]) ? $fromVersion : $this->config->proVersion[$fromVersion];

        $this->session->set('proInstalled', strpos($fromVersion, 'pro') !== false);
        parent::execute($zentaoVersion);
        if(!$this->session->proInstalled) $this->upgradeFreeToPro();

        $this->dao->delete()->from(TABLE_EXTENSION)->where('code')->like('bizext%')->exec();
        return true;
    }

    /**
     * Extends appendExec method for zentaopro.
     *
     * @param  string $zentaoVersion
     * @access public
     * @return void
     */
    public function appendExec($zentaoVersion)
    {
        if(!$this->session->proInstalled) return false;

        static $zentaoAndProPairs;
        if(empty($zentaoAndProPairs))
        {
            foreach($this->config->proVersion as $proVersion => $zentaoV) $zentaoAndProPairs[$zentaoV][] = $proVersion;
        }

        if(isset($zentaoAndProPairs[$zentaoVersion]))
        {
            $proVersions = $zentaoAndProPairs[$zentaoVersion];
            foreach($proVersions as $proVersion)
            {
                if(version_compare(str_replace('_', '.', $this->proFromVersion), str_replace('_', '.', $proVersion)) > 0) continue;

                $this->saveLogs("Execute $proVersion");
                $this->execSQL($this->getUpgradeFile(str_replace('_', '.', $proVersion)));
                switch($proVersion)
                {
                case 'pro1_1_1':
                    $this->execSQL($this->getUpgradeFile('pro1.1'));
                    break;
                case 'pro3_2_1':
                    $this->recordFinished();
                    break;
                case 'pro3_3':
                    $this->toLowerTable('pro');
                    break;
                case 'pro4_0':
                    $this->fixRepo();
                    break;
                case 'pro7_0_beta':
                    $this->fixReport();
                    break;
                case 'pro8_3':
                    $this->execSQL($this->getUpgradeFile('pro8.2'));//Fix bug #1752.
                    break;
                case 'pro8_8':
                    $this->checkURAndSR();
                    break;
                }
            }
        }
    }

    /**
     * Extends getConfirm method for zentaopro.
     *
     * @param  string $fromVersion
     * @access public
     * @return string
     */
    public function getConfirm($fromVersion)
    {
        $zentaoVersion = empty($this->config->proVersion[$fromVersion]) ? $fromVersion : $this->config->proVersion[$fromVersion];
        $confirmContent = parent::getConfirm($zentaoVersion);

        if(strpos($fromVersion, 'pro') === false)
        {
            $bizext = $this->dao->select('*')->from(TABLE_EXTENSION)->where('code')->like('bizext%')->andWhere('status')->eq('installed')->fetch();
            if(!empty($bizext)) $fromVersion = 'pro' . str_replace('.', '_', $bizext->version);
        }
        if(strpos($fromVersion, 'pro') === false) $confirmContent .= file_get_contents($this->getUpgradeFile('proinstall'));

        switch($fromVersion)
        {
        case 'pro1_0':   $confirmContent .= file_get_contents($this->getUpgradeFile('pro1.0'));
        case 'pro1_1':
        case 'pro1_1_1': $confirmContent .= file_get_contents($this->getUpgradeFile('pro1.1'));
        case 'pro1_2':
        case 'pro1_3':   $confirmContent .= file_get_contents($this->getUpgradeFile('pro1.3'));
        case 'pro2_0':
        case 'pro2_0_1':
        case 'pro2_1':   $confirmContent .= file_get_contents($this->getUpgradeFile('pro2.1'));
        case 'pro2_2_beta':
        case 'pro2_3_beta':
        case 'pro3_0_beta1':
        case 'pro3_0':   $confirmContent .= file_get_contents($this->getUpgradeFile('pro3.0'));
        case 'pro3_1':
        case 'pro3_2':
        case 'pro3_2_1':
        case 'pro3_3':
        case 'pro4_0_beta1':
        case 'pro4_0': $confirmContent .= file_get_contents($this->getUpgradeFile('pro4.0'));
        case 'pro4_1_beta':
        case 'pro4_2': $confirmContent .= file_get_contents($this->getUpgradeFile('pro4.2'));
        case 'pro4_3': $confirmContent .= file_get_contents($this->getUpgradeFile('pro4.3'));
        case 'pro4_4': $confirmContent .= file_get_contents($this->getUpgradeFile('pro4.4'));
        case 'pro4_5': $confirmContent .= file_get_contents($this->getUpgradeFile('pro4.5'));
        case 'pro4_6': $confirmContent .= file_get_contents($this->getUpgradeFile('pro4.6'));
        case 'pro4_7':
        case 'pro4_7_1': $confirmContent .= file_get_contents($this->getUpgradeFile('pro4.7.1'));
        case 'pro5_0':
        case 'pro5_0_1': $confirmContent .= file_get_contents($this->getUpgradeFile('pro5.0.1'));
        case 'pro5_1':
        case 'pro5_1_3': $confirmContent .= file_get_contents($this->getUpgradeFile('pro5.1.3'));
        case 'pro5_2':
        case 'pro5_2_1':
        case 'pro5_3':
        case 'pro5_3_1':
        case 'pro5_3_2':
        case 'pro5_3_3':
        case 'pro5_4':
        case 'pro5_4_1':
        case 'pro5_5':
        case 'pro5_5_1':
        case 'pro6_0_beta':
        case 'pro6_0':
        case 'pro6_0_1':
        case 'pro6_1':
        case 'pro6_2':
        case 'pro6_3':
        case 'pro6_3_1':
        case 'pro6_4': $confirmContent .= file_get_contents($this->getUpgradeFile('pro6.4'));
        case 'pro6_5':
        case 'pro6_5_1': $confirmContent .= file_get_contents($this->getUpgradeFile('pro6.5.1'));
        case 'pro6_6':
        case 'pro6_6_1': $confirmContent .= file_get_contents($this->getUpgradeFile('pro6.6.1'));
        case 'pro6_7':
        case 'pro6_7_1':
        case 'pro6_7_2':
        case 'pro6_7_3':
        case 'pro7_0_beta': $confirmContent .= file_get_contents($this->getUpgradeFile('pro7.0.beta'));
        case 'pro7_1':
        case 'pro7_2':
        case 'pro7_3':
        case 'pro7_4':
        case 'pro7_5':
        case 'pro7_5_1': $confirmContent .= file_get_contents($this->getUpgradeFile('pro7.5.1'));
        case 'pro8_0':
        case 'pro8_1':
        case 'pro8_2': $confirmContent .= file_get_contents($this->getUpgradeFile('pro8.2'));
        case 'pro8_3': $confirmContent .= file_get_contents($this->getUpgradeFile('pro8.3'));
        case 'pro8_3_1':
        case 'pro8_4': $confirmContent .= file_get_contents($this->getUpgradeFile('pro8.4'));
        case 'pro8_5':
        case 'pro8_5_1': $confirmContent .= file_get_contents($this->getUpgradeFile('pro8.5.1'));
        case 'pro8_5_2':
        case 'pro8_5_3':
        case 'pro8_6': $confirmContent .= file_get_contents($this->getUpgradeFile('pro8.6'));
        case 'pro8_7':
        case 'pro8_8':
        case 'pro8_8_1':
        case 'pro8_8_2':
        case 'pro8_8_3':
        case 'pro8_9':
        case 'pro8_9_1':
        case 'pro8_9_2':
        case 'pro8_9_3': $confirmContent .= file_get_contents($this->getUpgradeFile('pro8.9.3'));
        case 'pro8_9_4':
        case 'pro9_0':
        case 'pro9_0_1':
        case 'pro9_0_2':
        case 'pro9_0_3': $confirmContent .= file_get_contents($this->getUpgradeFile('pro9.0.3'));
        case 'pro10_0_rc1':
        }

        return str_replace('zt_', $this->config->db->prefix, $confirmContent);
    }

    /**
     * Upgrade to zentao from free.
     *
     * @access public
     * @return void
     */
    public function upgradeFreeToPro()
    {
        $this->execSQL($this->getUpgradeFile('proinstall'));
        $this->loadModel('effort')->convertEstToEffort();
    }

    /**
     * Record finished task effort.
     *
     * @access public
     * @return bool
     */
    public function recordFinished()
    {
        $this->saveLogs('Run Method ' . __FUNCTION__);
        $tasks = $this->dao->select('id,finishedBy,lastEditedBy,finishedDate,lastEditedDate')->from(TABLE_TASK)
            ->where('status')->in('done,closed')
            ->andWhere("(finishedDate='0000-00-00 00:00:00' or lastEditedDate='0000-00-00 00:00:00')")
            ->fetchAll('id');

        $efforts = $this->dao->select('t1.*,t2.date as actionDate')->from(TABLE_EFFORT)->alias('t1')
            ->leftJoin(TABLE_ACTION)->alias('t2')->on('t1.id=t2.objectID')
            ->where('t2.objectType')->eq('effort')
            ->andWhere('t1.left')->eq(0)
            ->andWhere('t1.objectType')->eq('task')
            ->andWhere('t1.objectID')->in(array_keys($tasks))
            ->orderBy('id')
            ->fetchAll('objectID');

        foreach($efforts as $taskID => $effort)
        {
            $data = array();
            if(empty($tasks[$taskID]->finishedBy))   $data['finishedBy']   = $effort->account;
            if(empty($tasks[$taskID]->lastEditedBy)) $data['lastEditedBy'] = $effort->account;
            if(helper::isZeroDate($tasks[$taskID]->finishedDate))   $data['finishedDate']   = $effort->actionDate;
            if(helper::isZeroDate($tasks[$taskID]->lastEditedDate)) $data['lastEditedDate'] = $effort->actionDate;
            if(!empty($data))
            {
                $this->dao->update(TABLE_TASK)->data($data)->where('id')->eq($taskID)->exec();
                $this->saveLogs($this->dao->get());
            }
        }

        return !dao::isError();
    }

    /**
     * Fix repo prefix.
     *
     * @access public
     * @return void
     */
    public function fixRepo()
    {
        $this->saveLogs('Run Method ' . __FUNCTION__);
        $this->app->loadConfig('repo');
        $repos = $this->dao->select('*')->from(TABLE_REPO)->fetchAll();
        foreach($repos as $repo)
        {
            if($repo->SCM == 'Subversion')
            {
                $scm = $this->app->loadClass('scm');
                $scm->setEngine($repo);
                $info = $scm->info('');
                $prefix = empty($info->root) ? '' : trim(str_ireplace($info->root, '', str_replace('\\', '/', $repo->path)), '/');
                if($prefix)
                {
                    $prefix = '/' . $prefix;
                    $this->dao->update(TABLE_REPO)->set('prefix')->eq($prefix)->where('id')->eq($repo->id)->exec();
                    $this->saveLogs($this->dao->get());
                }
            }
        }
    }

    /**
     * Fix report for add unique key.
     *
     * @access public
     * @return bool
     */
    public function fixReport()
    {
        $this->saveLogs('Run Method ' . __FUNCTION__);
        $reports = $this->dao->select('`code`,count(`code`) as count')->from(TABLE_REPORT)->groupBy('`code`')->orderBy('id')->fetchAll();
        $backSql = '';
        foreach($reports as $report)
        {
            if($report->count == 1) continue;

            $row = $this->dao->select('*')->from(TABLE_REPORT)->where('`code`')->eq($report->code)->orderBy('id')->limit(1)->query()->fetch(PDO::FETCH_ASSOC);

            /* Create key sql for insert. */
            $keys = array_keys($row);
            $keys = array_map('addslashes', $keys);
            $keys = join('`,`', $keys);
            $keys = "`" . $keys . "`";

            /* Create a value sql. */
            $value = array_values($row);
            $value = array_map('addslashes', $value);
            $value = join("','", $value);
            $value = "'" . $value . "'";

            $backSql .= "REPLACE INTO " . TABLE_REPORT . "($keys) VALUES (" . $value . ");\n";
            $this->dao->delete()->from(TABLE_REPORT)->where('id')->eq($row['id'])->exec();
            $this->saveLogs($this->dao->get());
        }

        if(!empty($backSql)) file_put_contents($this->app->getCacheRoot() . 'reportback.sql', $backSql);
        $codeIndex = $this->dao->query("show index from " . TABLE_REPORT . " where `key_name`= 'code'")->fetch();
        if(empty($codeIndex))
        {
            $this->dao->exec("ALTER TABLE " . TABLE_REPORT . " ADD UNIQUE `code` (`code`)");
            $this->saveLogs($this->dao->get());
        }

        return true;
    }

    public function checkURAndSR()
    {
        $hasURAndSR = $this->loadModel('setting')->getItem('owner=system&module=common&section=global&key=URAndSR');
        if($hasURAndSR) return true;

        $ur = $this->dao->select('*')->from(TABLE_STORY)->where('type')->eq('requirement')->limit(1)->fetch();
        if($ur) $this->setting->setItem('system.common.global.URAndSR', 1);
        return true;
    }
}
