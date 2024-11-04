<?php
class zentaobizCommon extends commonModel
{
    public function setCompany()
    {
        if(function_exists('ioncube_license_properties') and !isset($_SESSION['bizIoncubeProperties']))
        {
            $properties = ioncube_license_properties();

            if($properties and isset($this->app->user->feedback))
            {
                $ioncubeProperties = new stdclass();
                foreach($properties as $key => $property) $ioncubeProperties->$key = $property['value'];

                $inNonRD = (!empty($this->app->user->feedback) or $this->cookie->feedbackView);
                $user = $this->dao->select("COUNT('*') as count")->from(TABLE_USER)
                    ->where('deleted')->eq(0)
                    ->beginIF(!$inNonRD)->andWhere('feedback')->eq(0)->fi()
                    ->beginIF($inNonRD)->andWhere('feedback')->eq(1)->fi()
                    ->fetch();
                if(isset($properties['user']) and $properties['user']['value'] < $user->count) $ioncubeProperties->userLimited = true;
                $this->session->set('bizIoncubeProperties', $ioncubeProperties);
            }
        }
    }
}
