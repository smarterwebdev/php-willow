<?php
/* $Id$ */
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */


/**
 * Set no memory limit
 */
ini_set('memory_limit', -1);

/**
 * Set locales
 */
setlocale(LC_ALL, 'en_US.UTF-8');
setlocale(LC_MONETARY, 'en_US');

/**
 * Define APPLICATION_PATH
 */
define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../'));

/**
 * Define APPLICATION_PATH
 */
define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../'));

/**
 * Bootstrap Willow framework
 */
require APPLICATION_PATH . '/../Vendor/Willow/Bootstrap.php';

/**
 * Register accepted protocols
 */
Willow_Request::registerProtocols('http', 'json', 'xml', 'csv');

/**
 * Register aliases for HTTP protocol
 */
Willow_Request::registerProtocolAlias('html', 'http');
Willow_Request::registerProtocolAlias('php', 'http');
Willow_Request::registerProtocolAlias('do', 'http');

/**
 * Make HTTP the default protocol
 */
Willow_Request::registerDefaultProtocol('http');

/**
 * Get an instance of the application
 */
$app = Willow_Application::getInstance();

/**
 * Create an instance of the request
 */
$request = new Willow_Request();

/**
 * Setup request defaults
 */
$request->setDefaultModule('Default')
        ->setDefaultSection('Index')
        ->setDefaultAction('Index');

/**
 * Setup the request in the application
 */
$app->setRequest($request);

/**
 * Setup the router (we will use the rewrite router and routes from config)
 */
$app->setRouter(new Willow_Request_Router_Rewrite(
    $routes = Willow_Blackboard::get('config')->routes
));

/**
 * Register a fallback autoloader
 */
Willow_Autoloader::register(new Willow_Autoloader_Fallback());

/**
 * Setup Doctrine attributes
 */
Doctrine_Manager::getInstance()->setAttribute(Doctrine::ATTR_USE_NATIVE_ENUM, true);
Doctrine_Manager::getInstance()->setAttribute(Doctrine::ATTR_PORTABILITY, Doctrine::PORTABILITY_ALL ^ Doctrine::PORTABILITY_EXPR);

/**
 * Register default password hashing handler
 */
Willow_Cryptography_Password::register('Willow_Cryptography_Password_Crypt');

/**
 * Cleanup bootstrap vars
 */
unset($app, $request, $routes);
