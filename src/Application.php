<?php


namespace EasyShortVideo;



use EasyShortVideo\Kernel\ServiceContainer;

class Application
{

    /**
     * @var string
     * @Enumerate Tiktok (抖音)  Ixigua(西瓜)
     */
    protected static $platForm;


    /**
     * @param string $platForm
     * @param array $config
     * @param string|null $model
     * @return ServiceContainer
     */
    public static function From(string $platForm, array $config, string $model = null): ServiceContainer
    {
        $platForm = Kernel\Support\Str::studly($platForm);
        $namespace = $model?Kernel\Support\Str::studly($model):'OpenPlatform';
        $application = "\\EasyShortVideo\\{$platForm}\\{$namespace}\\Application";
        return new $application($config);
    }

    /**
     * @param string $name
     * @param array $config
     *
     * @return mixed
     */
    public function make(string $name, array $config)
    {
        $platForm = self::$platForm;
        $namespace = Kernel\Support\Str::studly($name);
        $application = "\\EasyShortVideo\\{$platForm}\\{$namespace}\\Application";
        return new $application($config);
    }


    /**
     * Dynamically pass methods to the application.
     *
     * @param string $name
     * @param array $arguments
     *
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        return $this->make($name, ...$arguments);
    }
}