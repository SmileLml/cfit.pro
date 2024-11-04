<?php
/**
 * Extends execute method for zentaopro.
 * 
 * @param  string $fromVersion 
 * @access public
 * @return bool
 */
public function execute($fromVersion)
{
if($this->session->step != 'biz' and $this->session->step != 'pro') return $this->loadExtension('zentaomax')->execute($fromVersion);

if($this->session->step != 'pro') return $this->loadExtension('zentaobiz')->execute($fromVersion);

    return $this->loadExtension('bizext')->execute($fromVersion);
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
if($this->session->step != 'pro' and $this->session->step != 'biz') return $this->loadExtension('zentaomax')->getConfirm($fromVersion);

if($this->session->step != 'pro') return $this->loadExtension('zentaobiz')->getConfirm($fromVersion);

    return $this->loadExtension('bizext')->getConfirm($fromVersion);
}

/**
 * Upgrade to zentaopro from free.
 * 
 * @access public
 * @return void
 */
public function upgradeFreeToPro()
{
    return $this->loadExtension('bizext')->upgradeFreeToPro();
}

/**
 * Record finished task effort.
 * 
 * @access public
 * @return bool
 */
public function recordFinished()
{
    return $this->loadExtension('bizext')->recordFinished();
}

/**
 * Fix repo prefix.
 * 
 * @access public
 * @return void
 */
public function fixRepo()
{
    return $this->loadExtension('bizext')->fixRepo();
}

/**
 * Fix report for add unique key.
 * 
 * @access public
 * @return bool
 */
public function fixReport()
{
    return $this->loadExtension('bizext')->fixReport();
}

public function checkURAndSR()
{
    return $this->loadExtension('bizext')->checkURAndSR();
}

