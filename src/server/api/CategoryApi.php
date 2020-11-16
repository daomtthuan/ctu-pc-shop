<?php

namespace Api;

use Core\Api;
use Core\Request;
use Core\Response;
use Provider\CategoryProvider;

/** Category api */
class CategoryApi extends Api {
  public static function mapUrl() {
    return '/api/category';
  }

  public static function get() {
    if (!Request::getInstance()->hasParam('idCategoryGroup')) {
      Response::getInstance()->sendStatus(400);
    }

    $categories = CategoryProvider::find([
      'idCategoryGroup' => Request::getInstance()->getParam('idCategoryGroup'),
      'state' => 1
    ]);

    Response::getInstance()->sendJson($categories);
  }
};