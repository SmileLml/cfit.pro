<?php
public function bind()
{
if($this->post->bindType == 'add' and $this->loadModel('user')->checkBizUserLimit())
{
    dao::$errors['password1'][] = $this->lang->user->noticeUserLimit;
    return false;
}

    return $this->loadExtension('bizext')->bind();
}

public function createUser()
{
if($this->loadModel('user')->checkBizUserLimit()) return array('status' => 'fail', 'data' => $this->lang->user->noticeUserLimit);

    return $this->loadExtension('bizext')->createUser();
}
