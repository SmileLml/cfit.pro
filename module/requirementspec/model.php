<?php
class requirementspecModel extends model
{

    public function insertByData($requirement){
        return $this->dao->insert(TABLE_REQUIREMENTSPEC)->data($requirement)
            ->autoCheck()
            ->batchCheck($this->config->requirementspec->change->requiredFields, 'notempty')
            ->exec();
    }
}
