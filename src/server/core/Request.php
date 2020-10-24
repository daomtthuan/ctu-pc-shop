<?php

namespace Core;

use Entity\User;
use Provider\UserProvider;

/** Request */
class Request {
  private static Request $instance;
  private array $address;
  private string $url;
  private array $cookies;
  private array $params;
  private string $method;
  private array $data;

  /** Create new instance of Request */
  private function __construct() {
    // Get Address request
    $this->address = [
      'ip' => $_SERVER['REMOTE_ADDR'],
      'mac' => strtok(exec('getmac'), ' ')
    ];

    // Get reques url 
    $this->url = strtok(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/', '?');

    // Get cookies
    $this->cookies = $_COOKIE;

    // Get request params
    $this->params = $_GET;

    // Get reques method 
    $this->method = isset($_SERVER['REQUEST_METHOD']) ? strtolower($_SERVER['REQUEST_METHOD'])  : 'get';

    // Get request data
    if (isset($_SERVER['CONTENT_TYPE'])) {
      if (strpos(strtolower($_SERVER['CONTENT_TYPE']), 'application/json') !== false) {
        $this->data = json_decode(file_get_contents('php://input'), true);
      }
    }
  }

  /** 
   * Get instance of Request 
   * 
   * @return Request Request
   */
  public static function getInstance() {
    if (!isset(self::$instance)) {
      self::$instance = new Request();
    }
    return self::$instance;
  }

  /**
   * Get Address request
   * 
   * @param string|null $key Param Key
   * 
   * @return array|mixed Get All params. Otherwise, value of param has that key
   */
  public function getAddress(string $key = null) {
    if (!isset($key)) {
      return $this->address;
    } else {
      return $this->address[$key];
    }
  }

  /**
   * Get the value of url request
   */
  public function getUrl() {
    return $this->url;
  }

  /**
   * Get Cookie request
   * 
   * @param string|null $key Cookie Key
   * 
   * @return mixed Cookies if $key is null. Otherwise, value of Cookie has that key
   */
  public function getCookie(string $key = null) {
    if (!isset($key)) {
      return $this->cookies;
    } else {
      return $this->cookies[str_replace('.', '_', $key)];
    }
  }

  /**
   * Check request has cookie or not
   * 
   * @param string[] $keys Cookie Keys
   * 
   * @return bool true if has cookie. Otherwise, false.
   */
  public function hasCookie(string ...$keys) {
    if (count($keys) == 0) {
      return count($this->cookies) > 0;
    } else {
      foreach ($keys as $key) {
        if (!isset($this->cookies[str_replace('.', '_', $key)])) {
          return false;
        }
      }
      return true;
    }
  }

  /**
   * Get the value of method
   */
  public function getMethod() {
    return $this->method;
  }

  /**
   * Get Data request
   * 
   * @param string|null $key Data Key
   * 
   * @return mixed Data JSON object if $key is null. Otherwise, value of data has that key
   */
  public function getData(string $key = null) {
    if (!isset($key)) {
      return $this->data;
    } else {
      return $this->data[$key];
    }
  }

  /**
   * Check request has data or not
   * 
   * @param string[] $keys Data Keys
   * 
   * @return bool true if has data. Otherwise, false.
   */
  public function hasData(string ...$keys) {
    if (count($keys) == 0) {
      return count($this->data) > 0;
    } else {
      foreach ($keys as $key) {
        if (!isset($this->data[$key])) {
          return false;
        }
      }
      return true;
    }
  }

  /**
   * Get Param request
   * 
   * @param string|null $key Param Key
   * 
   * @return array|mixed Get All params. Otherwise, value of param has that key
   */
  public function getParam(string $key = null) {
    if (!isset($key)) {
      return $this->params;
    } else {
      return $this->params[$key];
    }
  }

  /**
   * Check request has paramater or not
   * 
   * @param string[] $keys paramater Keys
   * 
   * @return bool true if has paramater. Otherwise, false.
   */
  public function hasParam(string ...$keys) {
    if (count($keys) == 0) {
      return count($this->params) > 0;
    } else {
      foreach ($keys as $key) {
        if (!isset($this->params[$key])) {
          return false;
        }
      }
      return true;
    }
  }

  /**
   * Verify user
   * 
   * @return User Verified user
   */
  public function verifyUser() {
    if (!$this->hasCookie($_ENV['TOKEN_KEY_LOCAL'])) {
      Response::getInstance()->sendStatus(401);
    }

    Session::start($this->getCookie($_ENV['TOKEN_KEY_LOCAL']));
    if (!Session::has('user')) {
      Response::getInstance()->sendStatus(401);
    }

    $users = UserProvider::find([
      'id' => Session::get('user')
    ]);

    if (count($users) != 1) {
      Response::getInstance()->sendStatus(401);
    }

    if (!$users[0]->getState()) {
      Response::getInstance()->sendStatus(406);
    }

    return $users[0];
  }
}
