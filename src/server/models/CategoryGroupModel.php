<?php

namespace Models;

use Core\Database;
use Entities\CategoryGroup;

class CategoryGroupModel {
  /**
   * Find by filter
   * 
   * @param array|null $filter Finding filter
   * 
   * @return CategoryGroup[] CategoryGroups
   */
  public static function find(array $filter = null) {
    $categoryGroups = [];
    foreach (Database::find('CategoryGroup', $filter) as $data) {
      $categoryGroups[] = new CategoryGroup($data);
    }
    return $categoryGroups;
  }
}
