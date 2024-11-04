<?php
$config->file->libreOfficeTurnon = 0;
$config->file->sofficePath       = '';

if(isset($lang->excel))
{
    $config->excel->editor['feedback'] = array('desc');
    $config->excel->freeze->feedback   = 'title';
    $config->excel->noAutoFilter[] = 'feedback';
}

$config->file->convertURL['feedback']['adminview'] = '1';
