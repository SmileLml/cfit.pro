<?php
/**
 * __construct 
 * 
 * @access public
 * @return void
 */
public function __construct($appName = '')
{
    parent::__construct($appName);

    $this->loadExtension('flow')->loadCustomLang();
}

/**
 * Update accounts.
 *
 * @param  int    $groupID
 * @access public
 * @return void
 */
public function updateAccounts($groupID)
{
    $this->loadExtension('flow')->updateAccounts($groupID);
}
