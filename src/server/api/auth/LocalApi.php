<?php

namespace Api\Auth;

use Core\Api;
use Core\Logger;
use Core\Request;
use Core\Response;
use Core\Session;
use Provider\RoleProvider;
use Provider\AccountProvider;

/** Auth local api */
class LocalApi extends Api {
  public static function mapUrl() {
    return '/api/auth/local';
  }

  public static function get() {
    $idAccount = Request::getInstance()->verifyAccount()->getId();

    $roles = RoleProvider::findOwnedByAccount($idAccount, [
      'state' => 1
    ]);
    if (count($roles) == 0) {
      Response::getInstance()->sendStatus(406);
    }

    $account =  [
      'id' => $idAccount,
      'roles' => []
    ];
    foreach ($roles as $role) {
      $account['roles'][] = $role->getName();
    }

    Response::getInstance()->sendJson([
      'account' => $account
    ]);
  }

  public static function post() {
    if (!Request::getInstance()->hasData('username', 'password')) {
      Response::getInstance()->sendStatus(400);
    }

    $accounts = AccountProvider::find([
      'username' => Request::getInstance()->getData('username')
    ]);
    if (count($accounts) != 1) {
      Response::getInstance()->sendStatus(401);
    }

    if (!password_verify(Request::getInstance()->getData('password'), $accounts[0]->getPassword())) {
      Response::getInstance()->sendStatus(401);
    }

    if (!$accounts[0]->getState()) {
      Response::getInstance()->sendStatus(406);
    }

    $token = Session::start();
    Session::set('account', $accounts[0]);
    Logger::getInstance()->setServiceLog('account', $accounts[0]);

    Response::getInstance()->sendJson([
      'token' => $token,
      'fullName' => $accounts[0]->getFullName()
    ]);
  }

  public static function put() {
    $account = Request::getInstance()->verifyAccount();

    if (!Request::getInstance()->hasData('oldPassword', 'password')) {
      Response::getInstance()->sendStatus(400);
    }

    if (!password_verify(Request::getInstance()->getData('oldPassword'), $account->getPassword())) {
      Response::getInstance()->sendStatus(406);
    }

    $account->setPassword(password_hash(Request::getInstance()->getData('password'), PASSWORD_BCRYPT));

    $success = AccountProvider::edit($account);

    Response::getInstance()->sendStatus($success ? 200 : 500);
  }

  public static function delete() {
    Request::getInstance()->verifyAccount();
    Session::stop();

    Response::getInstance()->sendStatus(200);
  }
};
