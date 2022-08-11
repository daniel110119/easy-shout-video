<?php

namespace EasyShortVideo\KuaiShou\OpenPlatform\UserData;

use EasyShortVideo\Kernel\BaseClient;

class Client extends BaseClient
{
    protected $postAccessToken = false;
    protected $needOpenid = true;

    public function userInfo()
    {
        return $this->httpGet('/openapi/user_info');
    }
}