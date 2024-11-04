<?php
class baseapplicationModel extends model
{
    public function getPairs()
    {
        return $this->dao->select('number, CASE WHEN code != "" THEN concat(concat(code,"_"),name) ELSE name END as name')->from(TABLE_BASEAPPLICATION)
            ->where('deleted')->eq(0)
            ->orderBy('id_desc')
            ->fetchPairs();
    }
}