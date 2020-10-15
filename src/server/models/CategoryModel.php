<?php

namespace Models;

use Core\Database;
use Entities\Category;

class CategoryModel {
  /**
   * Find by filter
   * 
   * @param array|null $filter Finding filter
   * 
   * @return Category[] Categories
   */
  public static function find(array $filter = null) {
    $categories = [];
    foreach (Database::find('Category', $filter) as $data) {
      $categories[] = new Category($data);
    }
    return $categories;
  }
}
