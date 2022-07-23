<?php
namespace tests;

use EasyShortVideo\Application;
use EasyShortVideo\Kernel\Exceptions\AccessTokenException;
use EasyShortVideo\Kernel\Exceptions\HttpException;
use PHPUnit\Framework\TestCase;

class TestApplication extends TestCase
{
    public function test_fn()
    {
        $app = Application::From('ixigua',[1,2,3])->video;
        $token = $app->httpUpload('asd/asd');
        var_dump($token);
    }

    public function testFn2()
    {
        $app = Application::From('tiktok',[
            'app_id' => '123app_id',
            'secret' => '123secret',
            'openid'=>'aaa-bbb-ccc',
            'cache'  => [
                'type'       => 'Redis',
                'host'       => '127.0.0.1',
                'port'       => 6379,
                'select'     => 10,
            ],
        ])->user_data;
        var_dump($app->item());
    }

    public function testfn3()
    {
        $app = Application::From('tiktok',[
            'app_id' => '123app_id',
            'secret' => '123secret',
            'cache'  => [
                'type'       => 'Redis',
                'host'       => '127.0.0.1',
                'port'       => 6379,
                'select'     => 10,
            ],
            'http' => [
                'timeout' => 5.0
            ],
            'response_type'=>'collection',
            'test'=>true
        ])->access_token;
        var_dump($app->setAuth(123));
    }


    public function testKuaiShou()
    {
        $access_token = Application::From('kuaishou', [
            'test'=>true,
            'app_id' => '123app_id',
            'secret' => '123secret',
            'cache'=>[
                'type'=>'Redis'
            ]
        ])->access_token;
        try {
            dump($access_token->setAuth(1));
        }catch (HttpException $exception){
           dump($exception->getMessage());
        }
    }

    public function testFNN()
    {
        $access_token = Application::From('kuaishou', [
            'test'=>true,
            'app_id' => '123app_id',
            'secret' => '123secret',
            'openid'=> 'kuaishou_openid',
            'cache'=>[
                'type'=>'Redis'
            ],
            'response_type'=>'collection',
        ])->video_data;
        try {
            dump($access_token->list('127.0.0.1:8000/api/uploadImage'));
        }catch (HttpException $exception){
            dump($exception->getMessage());
        }catch (AccessTokenException $accessTokenException){
            dump($accessTokenException->getMessage());
        }
    }
}