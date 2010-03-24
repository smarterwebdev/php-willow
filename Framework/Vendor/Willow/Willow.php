<?php
/* $Id$ */
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */


/**
 * Before adding functionality to this class, careful consideration should be given 
 * as to if it should exist here or in seperate class.  In most cases, the later will
 * be true.
 */
final class Willow
{

    /**
     * @var array Alias lookup cache
     */
    private $_aliases = array();

    /**
     * Prevent public construction of object
     */
    private function __construct()
    {
    }

    /**
     * @var string Current app deployment directory name
     */
    private $_deployment = null;

    /**
     * Get the current app deployment directory name
     *
     * @return string Deployment directory name
     */
    public static function getDeployment()
    {
        if (self::_instance()->_deployment === null)
        {
            $domainTree = explode('.', $_SERVER['HTTP_HOST']);

            $deploymentRoot = self::getRoot() . DIRECTORY_SEPARATOR . 'Deployment';

            do
            {
                $deployment = implode('.', $domainTree);

                if (is_dir($deploymentRoot . DIRECTORY_SEPARATOR . $deployment))
                {
                    self::_instance()->_deployment = $deployment;
                    break;
                }
            }
            while (array_shift($domainTree) !== null);

            if (self::_instance()->_deployment === null)
            {
                $deployment = 'default';
            }
        }

        return self::_instance()->_deployment;
    }

    /**
     * @var string Current app code root directory full path
     */
    private $_root = null;

    /**
     * ...
     */
    public static function getRoot()
    {
        if (self::_instance()->_root === null)
        {
            self::_instance()->_root = realpath(dirname(__FILE__) . '/../../');
        }

        return self::_instance()->_root;
    }

    /**
     * ...
     */
    private $_appDir = 'App';

    /**
     * ...
     */
    public static function setAppDir($dir)
    {
        self::_instance()->_appDir = basename(realpath($dir));
    }

    /**
     * ...
     */
    public static function getAppDir()
    {
        return self::_instance()->_appDir;
    }

    /**
     * @var Willow_Yaml_Node Application config
     */
    private $_config;

    /**
     * Get the application config
     *
     * @return Willow_Yaml_Node
     */
    public static function getConfig()
    {
        if ((self::_instance()->_config instanceof Willow_Yaml_Node) === false)
        {
            self::_instance()->_config = self::_buildConfig();
        }

        return self::_instance()->_config;
    }

    /**
     * Build the application config
     *
     * @return Willow_Yaml_Node
     */
    private static function _buildConfig()
    {
        /**
         * Start with config in core framework
         */
        $conf = new Willow_Yaml_Node(
            Willow_Loader::getRealPath('Core:Config:Willow', false, 'yml')
        );

        /**
         * load any configs from plugins
         */
        foreach (Willow_Plugin_Loader::getConfigs() as $config)
        {
            $conf->assimilate($config);
        }

        /**
         * Assimilate app config
         */
        if (($yaml = Willow_Loader::getRealPath('App:Config:Willow', false, 'yml')) !== false)
        {
            $conf->assimilate($yaml);
        }

        /**
         * Assimilate deployment config
         */
        if (($yaml = Willow_Loader::getRealPath('Deployment:Config:Willow', false, 'yml')) !== false)
        {
            $conf->assimilate($yaml);
        }

        /**
         * Create routes node if it does not exist
         */
        if (($routes = $conf->routes) === null)
        {
            $routes = $conf->addChildNode('routes');
        }

        /**
         * Add app routes
         */
        if (($yaml = Willow_Loader::getRealPath('App:Config:Routes', false, 'yml')) !== false)
        {
            $routes->assimilate($yaml);
        }

        /**
         * Add deployment routes
         */
        if (($yaml = Willow_Loader::getRealPath('Deployment:Config:Routes', false, 'yml')) !== false)
        {
            $routes->assimilate($yaml);
        }

        return $conf;
    }

    /**
     * @var Willow Instance of self
     */
    private static $_instance = null;

    /**
     * Get the singleton instance of self
     */
    private static function _instance()
    {
        if ((self::$_instance instanceof Willow) === false)
        {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

}
