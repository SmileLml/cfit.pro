<?php
class componentparam extends control
{

    public function paramset($field = ''){
        $currentLang = $this->app->getClientLang();


        $lang = 'zh-cn';
        $module = 'component';

        $field = 'englishClassifyList';


        $this->app->loadLang($module);
        $fieldList = zget($this->lang->$module, $field, '');
        $this->loadModel('custom');

        if(!empty($_POST))
        {
            $data = fixer::input('post')->get();
            $lang = $_POST['lang'];
            $oldCustoms = $this->custom->getItems("lang=$lang&module=$module&section=$field");


            foreach($_POST['keys'] as $index => $key)
            {
                if(!empty($key)) $key = trim($key);
                if(!$key){
                    $this->send(array('result' => 'fail', 'message' => $this->lang->componentparam->englishkeyEmptyError));
                }
                if(isset($data->values[$index]) && !$data->values[$index]){
                    $this->send(array('result' => 'fail', 'message' => $this->lang->componentparam->englishvaluesEmptyError));
                }
                /* Invalid key. It should be numbers. (It includes severityList in bug module and priList in stroy, task, bug, testcasea, testtask and todo module.) */
                if(!empty($key) and !isset($oldCustoms[$key]) and $key != 'n/a' and !validater::checkREG($key, '/^[a-zA-Z_0-9]+$/')) $this->send(array('result' => 'fail', 'message' => $this->lang->custom->notice->invalidStringKey));
            }


            $unique_english_values = array_unique($data->values);
            if(count($unique_english_values) != count($data->values)){
                $msg = array_diff_assoc($data->values,$unique_english_values);

                $this->send(array('result' => 'fail', 'message' => $this->lang->componentparam->englishvalueRepeatError.':'.implode(',',$msg)));

            }

            //验证key重复


            $unique_english_keys = array_unique($data->keys);
            if(count($unique_english_keys) != count($data->keys)){
                $msg = array_diff_assoc($data->keys,$unique_english_keys);

                $this->send(array('result' => 'fail', 'message' => $this->lang->componentparam->englishkeyRepeatError.':'.implode(',',$msg)));

            }

            try {
                $this->dao->begin();
                $this->custom->deleteItems("lang=$lang&module=$module&section=$field");


                foreach($data->keys as $index => $key)
                {

                    if($key){
                        $value  = $data->values[$index];
                        $system = $data->systems[$index];
                        $this->custom->setItem("{$lang}.{$module}.{$field}.{$key}.{$system}", $value);
                    }

                }
                //英文数据入库

                if(!dao::isError()){
                    $this->dao->commit();
                    $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $this->createLink('componentparam', 'paramset', "")));
                }else{
                    $this->dao->rollBack();
                    $this->send(array('result' => 'fail', 'message' => dao::getError()));
                }


            }catch (Error $e){
                $this->dao->rollBack();
                $this->send(array('result' => 'fail', 'message' => $e->getMessage()));
            }




        }

        /* Check whether the current language has been customized. */
        $lang = str_replace('_', '-', $lang);
        $dbFields = $this->custom->getItems("lang=$lang&module=$module&section=$field");



//            if(empty($dbFields)) $dbFields = $this->custom->getItems("lang=" . ($lang == $currentLang ? 'all' : $currentLang) . "&module=$module&section=$field");

        if($dbFields)
        {
            $dbField = reset($dbFields);
            if($lang != $dbField->lang)
            {
                $lang = str_replace('-', "_", $dbField->lang);
                foreach($fieldList as $key => $value)
                {
                    if(isset($dbFields[$key]) and $value != $dbFields[$key]->value) $fieldList[$key] = $dbFields[$key]->value;
                }
            }
        }


        $this->view->title       = $this->lang->custom->common . $this->lang->colon . $this->lang->$module->common;
        $this->view->position[]  = $this->lang->custom->common;
        $this->view->position[]  = $this->lang->$module->common;
        $this->view->fieldList   = $fieldList;

        $this->view->dbFields    = $dbFields;

        $this->view->field       = $field;
        $this->view->lang2Set     = str_replace('_', '-', $lang);
        $this->view->module      = $module;
        $this->view->currentLang = $currentLang;
        $this->view->canAdd      = strpos($this->config->custom->canAdd[$module], $field) !== false;
        $this->display();
    }
    public function paramsetbak($field = ''){
        $currentLang = $this->app->getClientLang();


        $lang = 'zh-cn';
        $module = 'component';
        if($field == ''){
            $field = 'chineseClassifyList';
        }

        $this->app->loadLang($module);
        $fieldList = zget($this->lang->$module, $field, '');
        $this->loadModel('custom');

        if(!empty($_POST))
        {
            $data = fixer::input('post')->get();
            $lang = $_POST['lang'];
            $oldCustoms = $this->custom->getItems("lang=$lang&module=$module&section=$field");
            $oldCustoms2 = $this->custom->getItems("lang=$lang&module=$module&section=englishClassifyList");
            foreach($_POST['keys'] as $index => $key)
            {
                if(!empty($key)) $key = trim($key);
                if(!$key){
                    $this->send(array('result' => 'fail', 'message' => $this->lang->componentparam->chinesskeyEmptyError));
                }
                if(isset($data->values[$index]) && !$data->values[$index]){
                    $this->send(array('result' => 'fail', 'message' => $this->lang->componentparam->chinessvaluesEmptyError));
                }
                /* Invalid key. It should be numbers. (It includes severityList in bug module and priList in stroy, task, bug, testcasea, testtask and todo module.) */
                if(!empty($key) and !isset($oldCustoms[$key]) and $key != 'n/a' and !validater::checkREG($key, '/^[a-zA-Z_0-9]+$/')) $this->send(array('result' => 'fail', 'message' => $this->lang->custom->notice->invalidStringKey));
            }

            foreach($_POST['keys2'] as $index2 => $key2)
            {
                if(!empty($key2)) $key2 = trim($key2);
                if(!$key2){
                    $this->send(array('result' => 'fail', 'message' => $this->lang->componentparam->englishkeyEmptyError));
                }
                if(isset($data->values2[$index2]) && !$data->values2[$index2]){
                    $this->send(array('result' => 'fail', 'message' => $this->lang->componentparam->englishvaluesEmptyError));
                }
                /* Invalid key. It should be numbers. (It includes severityList in bug module and priList in stroy, task, bug, testcasea, testtask and todo module.) */
                if(!empty($key2) and !isset($oldCustoms2[$key2]) and $key2 != 'n/a' and !validater::checkREG($key2, '/^[a-zA-Z_0-9]+$/')) $this->send(array('result' => 'fail', 'message' => $this->lang->custom->notice->invalidStringKey));
            }


            $unique_chinese_values = array_unique($data->values);
            if(count($unique_chinese_values) != count($data->values)){

                $msg = array_diff_assoc($data->values,$unique_chinese_values);

                $this->send(array('result' => 'fail', 'message' => $this->lang->componentparam->chinessvalueRepeatError.':'.implode(',',$msg)));

            }
            $unique_english_values = array_unique($data->values2);
            if(count($unique_english_values) != count($data->values2)){
                $msg = array_diff_assoc($data->values2,$unique_english_values);

                $this->send(array('result' => 'fail', 'message' => $this->lang->componentparam->englishvalueRepeatError.':'.implode(',',$msg)));

            }

            //验证key重复
            $unique_chinese_keys = array_unique($data->keys);
            if(count($unique_chinese_keys) != count($data->keys)){

                $msg = array_diff_assoc($data->keys,$unique_chinese_keys);

                $this->send(array('result' => 'fail', 'message' => $this->lang->componentparam->chinesskeyRepeatError.':'.implode(',',$msg)));

            }
            $unique_english_keys2 = array_unique($data->keys2);
            if(count($unique_english_keys2) != count($data->keys2)){
                $msg = array_diff_assoc($data->keys2,$unique_english_keys2);

                $this->send(array('result' => 'fail', 'message' => $this->lang->componentparam->englishkeyRepeatError.':'.implode(',',$msg)));

            }
            if(count($unique_chinese_keys) != count($unique_english_keys2)){
                $this->send(array('result' => 'fail', 'message' => $this->lang->componentparam->keyneqKey2Error));
            }

            try {
                $this->dao->begin();
                $this->custom->deleteItems("lang=$lang&module=$module&section=$field");
                //英文分类
                $this->custom->deleteItems("lang=$lang&module=$module&section=englishClassifyList");

                foreach($data->keys as $index => $key)
                {

                    if($key){
                        $value  = $data->values[$index];
                        $system = $data->systems[$index];
                        $this->custom->setItem("{$lang}.{$module}.{$field}.{$key}.{$system}", $value);
                    }

                }
                //英文数据入库
                foreach($data->keys2 as $index2 => $key2)
                {

                    if($key2){
                        $value2  = $data->values2[$index2];
                        $system2 = $data->systems2[$index2];
                        $this->custom->setItem("{$lang}.{$module}.englishClassifyList.{$key2}.{$system2}", $value2);
                    }

                }
                if(!dao::isError()){
                    $this->dao->commit();
                    $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $this->createLink('componentparam', 'paramset', "")));
                }else{
                    $this->dao->rollBack();
                    $this->send(array('result' => 'fail', 'message' => dao::getError()));
                }


            }catch (Error $e){
                $this->dao->rollBack();
                $this->send(array('result' => 'fail', 'message' => $e->getMessage()));
            }




        }

        /* Check whether the current language has been customized. */
        $lang = str_replace('_', '-', $lang);
        $dbFields = $this->custom->getItems("lang=$lang&module=$module&section=$field");

        $dbFields2 = $this->custom->getItems("lang=$lang&module=$module&section=englishClassifyList");

//            if(empty($dbFields)) $dbFields = $this->custom->getItems("lang=" . ($lang == $currentLang ? 'all' : $currentLang) . "&module=$module&section=$field");

        if($dbFields)
        {
            $dbField = reset($dbFields);
            if($lang != $dbField->lang)
            {
                $lang = str_replace('-', "_", $dbField->lang);
                foreach($fieldList as $key => $value)
                {
                    if(isset($dbFields[$key]) and $value != $dbFields[$key]->value) $fieldList[$key] = $dbFields[$key]->value;
                }
            }
        }
        $fieldList2 = zget($this->lang->$module, 'englishClassifyList', '');
        if($dbFields2)
        {
            $dbField = reset($dbFields2);
            if($lang != $dbField->lang)
            {
                $lang = str_replace('-', "_", $dbField->lang);
                foreach($fieldList2 as $key => $value)
                {
                    if(isset($dbFields2[$key]) and $value != $dbFields2[$key]->value) $fieldList2[$key] = $dbFields2[$key]->value;
                }
            }
        }

        $this->view->title       = $this->lang->custom->common . $this->lang->colon . $this->lang->$module->common;
        $this->view->position[]  = $this->lang->custom->common;
        $this->view->position[]  = $this->lang->$module->common;
        $this->view->fieldList   = $fieldList;
        $this->view->fieldList2   = $fieldList2;
        $this->view->dbFields    = $dbFields;
        $this->view->dbFields2    = $dbFields2;
        $this->view->field       = $field;
        $this->view->lang2Set     = str_replace('_', '-', $lang);
        $this->view->module      = $module;
        $this->view->currentLang = $currentLang;
        $this->view->canAdd      = strpos($this->config->custom->canAdd[$module], $field) !== false;
        $this->display();
    }

    public function delete(){

        if(strtolower($this->server->request_method) == "post")
        {
            $data = fixer::input('post')->get();
            //中文分类

            if(!$data->keys){
                $this->send(['code'=>200,'msg'=>$this->lang->componentparam->successNotice],'json');
            }
            if(!$data->keys){
                $this->send(['code'=>100,'msg'=>$this->lang->componentparam->deleteKeyError],'json');
            }

            $info = $this->dao->select("id")->from(TABLE_COMPONENT_RELEASE)->where('englishClassify')->eq($data->keys)->andWhere('deleted')->eq('0')->fetch();

            //英文分类

            if($info){

                $this->send(['code'=>100,'msg'=>$this->lang->componentparam->notDeleteNotice],'json');
            }else{
                $this->loadModel('custom');
                $this->custom->deleteItems("lang=zh-cn&module=component&section=englishClassifyList&key={$data->keys}");
                $this->send(['code'=>200,'msg'=>$this->lang->componentparam->successNotice],'json');
            }

        }



    }
    public function deletebak(){

        if(strtolower($this->server->request_method) == "post")
        {
            $data = fixer::input('post')->get();
            //中文分类

            if(!$data->keys && !$data->keys2){
                $this->send(['code'=>200,'msg'=>$this->lang->componentparam->successNotice],'json');
            }
            if(!$data->keys || !$data->keys2){
                $this->send(['code'=>100,'msg'=>$this->lang->componentparam->deleteKeyError],'json');
            }

            $info = $this->dao->select("id")->from(TABLE_COMPONENT_RELEASE)->where('chineseClassify')->eq($data->keys)->andWhere('deleted')->eq('0')->fetch();

            //英文分类

            $info2 = $this->dao->select("id")->from(TABLE_COMPONENT_RELEASE)->where('englishClassify')->eq($data->keys2)->andWhere('deleted')->eq('0')->fetch();

            if($info || $info2){

                $this->send(['code'=>100,'msg'=>$this->lang->componentparam->notDeleteNotice],'json');
            }else{
                $this->loadModel('custom');
                $this->custom->deleteItems("lang=zh-cn&module=component&section=chineseClassifyList&key={$data->keys}");

                $this->custom->deleteItems("lang=zh-cn&module=component&section=englishClassifyList&key={$data->keys2}");
                $this->send(['code'=>200,'msg'=>$this->lang->componentparam->successNotice],'json');
            }

        }



    }
}
