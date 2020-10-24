<?php

namespace Core;

use Exception;

/** Routter */
class Router {
  private static Router $instance;
  private array $controllers;

  /** Create new instance of Routter */
  private function __construct() {
    $this->controllers = [];
  }

  /** 
   * Get instance of Router 
   * 
   * @return Router Router
   */
  public static function getInstance() {
    if (!isset(self::$instance)) {
      self::$instance = new Router();
    }
    return self::$instance;
  }


  /**
   * Register Api mapping Url
   * 
   * @param string $url Mapping url
   * @param string $name Api class name
   */
  public function registerApi(string $url, string $name) {
    $this->controllers[$url] = $name;
  }

  /** Redirect Api */
  public function redirectApi() {
    // Check Api mapping
    if (!isset($this->controllers[Request::getInstance()->getUrl()])) {
      $this->redirectError(404, 'Not found ' . Request::getInstance()->getUrl());
    }

    // Check method in Api
    $controller = $this->controllers[Request::getInstance()->getUrl()];
    if (!method_exists($controller, Request::getInstance()->getMethod())) {
      $this->redirectError(405, 'Method ' . Request::getInstance()->getMethod() . ' not allowed');
    }

    // Call method in Api
    try {
      call_user_func("$controller::" . Request::getInstance()->getMethod());
    } catch (Exception $exception) {
      $this->redirectError(500, $exception->getMessage());
    }

    Service::getInstance()->stop();
  }

  /**
   * Redirect error
   * 
   * @param int $statusCode Error Status code
   * @param string|null $message Error message
   */
  public function redirectError(int $statusCode, string $message = null) {
    Http::setStatus($statusCode);
    Http::setContentType('text');
    if (!isset($message)) {
      Http::setData("Error $statusCode");
    } else {
      Http::setData("Error $statusCode: $message");
    }
    Logger::getInstance()->setServiceLog('status', $statusCode);

    Service::getInstance()->stop();
  }
}
