<?php
defined('BASEPATH') OR exit('No direct script access allowed');

interface AiProviderInterface
{
    /**
     * @param array $messages OpenAI-style messages: [ ['role'=>'system|user|assistant','content'=>'...'], ... ]
     * @param array $options  e.g. ['model'=>'...', 'timeout_seconds'=>20]
     * @return array          ['ok'=>bool,'text'=>string,'status'=>int,'error'=>string|null,'meta'=>array]
     */
    public function chat($messages, $options);
}

