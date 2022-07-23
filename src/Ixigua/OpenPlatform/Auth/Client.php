<?php

namespace EasyShortVideo\Ixigua\OpenPlatform\Auth;

use EasyShortVideo\Kernel\BaseClient;

class Client extends BaseClient
{
    protected $needAccessToken = false;
    protected $postAccessToken = false;
    protected $baseUri = 'http://aaa.com';

    public function test()
    {
        return $this->httpGet('api/test');
    }
}