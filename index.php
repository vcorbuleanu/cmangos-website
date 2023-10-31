<?php
    session_start();
    error_reporting(1);
    
    require_once('application/config.php');
    require_once('framework/functions.php');

    date_default_timezone_set(WEBSITE_TIMEZONE);

    function autoloader($class) {
        // Check if the class is of type: controller
        if (str_ends_with($class, '_controller')) {
            $class = str_replace('_controller', '', $class);
            if (file_exists('application/controllers/' . $class . '.php'))
                return require('application/controllers/' . $class . '.php');
        }

        // Check if the class is of type: model
        if (str_ends_with($class, '_model')) {
            $class = str_replace('_model', '', $class);
            if (file_exists('application/models/' . $class . '.php'))
                return require('application/models/' . $class . '.php');
        }

        // Check if the class is of type: core
        $core_classes = array('controller', 'model', 'input', 'loader', 'router');
        if (in_array($class, $core_classes) && file_exists('framework/core/' . $class . '.php'))
            return require('framework/core/' . $class . '.php');

        // Check if the class is of type: helper
        $helper_classes = array('database', 'jwt', 'srp6', 'otp', 'mailer');
        if (in_array($class, $helper_classes) && file_exists('framework/helpers/' . $class . '.php'))
            return require('framework/helpers/' . $class . '.php');
    }

    spl_autoload_register('autoloader');

    $router = new router();

    $router->register(['GET'], '/', DEFAULT_CONTROLLER . '_controller@' . DEFAULT_METHOD);
    $router->register(['GET'], '/news/latest', DEFAULT_CONTROLLER . '_controller@' . DEFAULT_METHOD);
    $router->register(['GET'], '/news/archived', 'news_controller@archived');
    $router->register(['GET'], '/realms/status', 'realms_controller@status');
    $router->register(['GET'], '/realms/players', 'realms_controller@players');
    $router->register(['GET'], '/realms/statistics', 'realms_controller@statistics');

    $router->register(['GET'], '/account/logout', 'account_controller@logout');
    $router->register(['GET'], '/account/manage', 'account_controller@manage');
    $router->register(['GET'], '/account/activate/:token', 'account_controller@activate');
    $router->register(['GET'], '/account/validate/:token', 'account_controller@validate');
    $router->register(['GET'], '/account/logs', 'account_controller@logs');

    $router->register(['GET', 'POST'], '/account/create', 'account_controller@create');
    $router->register(['GET', 'POST'], '/account/login', 'account_controller@login');
    $router->register(['GET', 'POST'], '/account/recover', 'account_controller@recover');
    $router->register(['GET', 'POST'], '/account/reset/:token', 'account_controller@reset');
    $router->register(['GET', 'POST'], '/account/avatar', 'account_controller@avatar');
    $router->register(['GET', 'POST'], '/account/signature', 'account_controller@signature');
    $router->register(['GET', 'POST'], '/account/nickname', 'account_controller@nickname');
    $router->register(['GET', 'POST'], '/account/password', 'account_controller@password');
    $router->register(['GET', 'POST'], '/account/email', 'account_controller@email');
    $router->register(['GET', 'POST'], '/account/mfa', 'account_controller@mfa');
    
    $router->register(['GET'], '/404', '_404_controller@index');

    $router->process();
?>