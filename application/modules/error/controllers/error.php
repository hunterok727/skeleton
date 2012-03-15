<?php
/**
 * Error controller
 * Send error headers and show simple page
 *
 * @author   Anton Shevchuk
 * @created  11.07.11 15:32
 */
namespace Bluz;
return
/**
 * @param  integer $code
 * @param  string $message
 * @return closure
 */
function ($code, $message = '') use ($app, $view) {
    /**
     * @var \Bluz\Application $app
     * @var \Bluz\View $view
     */
    switch ($code) {
        case 403:
            if (!headers_sent()) header("HTTP/1.0 403 Forbidden");
            $app->getView()->title = "Error 403";
            $view->description = "Access denied";
            break;
        case 404:
            if (!headers_sent()) header("HTTP/1.0 404 Not Found");
            $app->getView()->title = "Error 404";
            $view->description = "The page you requested was not found.";
            break;
        default:
            if (!headers_sent()) header("HTTP/1.0 400 Bad Request");
            $app->getView()->title = "Error 400";
            $view->description = "An unexpected error occurred with your request. Please try again later.";
            break;
    }
    $app->addError($message);
    $view->message = $message;
    $app->useLayout('small.phtml');
};