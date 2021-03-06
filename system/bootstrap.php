<?php
/**
 * Bootstrap handler - perform the Nova Framework's bootstrap stage.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date April 10th, 2016
 */

use Core\Config;
use Core\Language;
use Core\Logger;
use Core\Modules;
use Foundation\AliasLoader;
use Routing\Router;
use Support\Facades\App;
use Support\Facades\Event;
use Support\Facades\Request;
use Support\Facades\Session;

use Patchwork\Utf8\Bootup as Patchwork;

/** Ensure using internally the UTF-8 encoding. */
if (function_exists('mb_internal_encoding')) {
    mb_internal_encoding('utf-8');
}

/** Turn on the custom error handling. */
set_exception_handler('Core\Logger::ExceptionHandler');
set_error_handler('Core\Logger::ErrorHandler');

/** Turn on output buffering. */
ob_start();

/** Load the Configuration. */
require APPDIR .'Config.php';

/** Set the Default Timezone. */
date_default_timezone_set(DEFAULT_TIMEZONE);

/** Initialize the Class Aliases. */
$aliases = Config::get('app.aliases', array());

AliasLoader::getInstance($aliases)->register();

/** Initialize the Logger. */
Logger::init();

/** Start the Session. */
Session::init();

/** Initialize the Language. */
Language::init();

/** Initialize the Patchwork Utf8. */
Patchwork::initAll();

/** Load the Events. */
require APPDIR .'Events.php';

/** Load the Route Filters */
require APPDIR .'Filters.php';

/** Initialize the active Modules. */
Modules::init();

/** Get the Router instance. */
$router = Router::getInstance();

/** Load the Routes. */
require APPDIR .'Routes.php';

/** Load the Routes from the active Modules. */
Modules::loadRoutes();

/** Inform listeners of Nova execution. */
Event::fire('nova.framework.booting');

/** Get the Request instance. */
$request = Request::instance();

/** Execute matched Routes. */
$result = $router->dispatch($request);

/* Finish the Session and send the Response. */
App::finish($result);
