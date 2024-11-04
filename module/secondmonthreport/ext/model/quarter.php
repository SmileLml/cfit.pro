<?php

function getModifyFinishData($start,$end,$deptID)
{
    return $this->loadExtension('quarter')->getModifyFinishData($start,$end,$deptID);
}

function problemOverallQuarter($timeFrame)
{
    return $this->loadExtension('quarter')->problemOverallQuarter($timeFrame);
}

function getTimeRangeByQuarter($isyearForm, $formtype)
{
    return $this->loadExtension('quarter')->getTimeRangeByQuarter($isyearForm, $formtype);
}

function getProblemOverallReport($wholeInfo, $deptId = -1)
{
    return $this->loadExtension('quarter')->getProblemOverallReport($wholeInfo, $deptId);
}

function problemHistoryExceedBackInQuarter($timeFrame)
{
    return $this->loadExtension('quarter')->problemHistoryExceedBackInQuarter($timeFrame);
}

function requirementHistoryInsideQuarter($timeFrame)
{
    return $this->loadExtension('quarter')->requirementHistoryInsideQuarter($timeFrame);
}

function secondOrderHistoryClassQuarter($timeFrame)
{
    return $this->loadExtension('quarter')->secondOrderHistoryClassQuarter($timeFrame);
}

function modifyHistoryNormalQuarter($timeFrame)
{
    return $this->loadExtension('quarter')->modifyHistoryNormalQuarter($timeFrame);
}

function supportHistoryQuarter($timeFrame)
{
    return $this->loadExtension('quarter')->supportHistoryQuarter($timeFrame);
}

function workloadHistoryQuarter($timeFrame)
{
    return $this->loadExtension('quarter')->workloadHistoryQuarter($timeFrame);
}

function problemCompletedPlanHistoryQuarter($timeFrame)
{
    return $this->loadExtension('quarter')->problemCompletedPlanHistoryQuarter($timeFrame);
}

function initialization($quarter)
{
    return $this->loadExtension('quarter')->initialization($quarter);
}

function initializationTime($quarter = 0, $year = 0)
{
    return $this->loadExtension('quarter')->initializationTime($quarter, $year);
}

