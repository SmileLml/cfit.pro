<?php

/**
 * 发送内部超时提醒邮件
 * @return mixed
 */
public function sendmailByOutTime()
{
    return $this->loadExtension('cfjk')->sendmailByOutTime();
}

/**
 * 发送外部超时提醒邮件
 * @return mixed
 */
public function sendmailByOutTimeOutSide()
{
    return $this->loadExtension('cfjk')->sendmailByOutTimeOutSide();
}
