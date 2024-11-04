<?php

public function importProgress($model)
{
    return $this->loadExtension('progress')->importProgress($model);
}
