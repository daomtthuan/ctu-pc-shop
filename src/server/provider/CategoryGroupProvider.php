<?php

namespace Provider;

use Core\Database;
use Entity\CategoryGroup;

/** CategoryGroup provider */
class CategoryGroupProvider {
  /**
   * Find category group by filter
   * 
   * @param array|null $filter Finding filter
   * 
   * @return CategoryGroup[] CategoryGroups
   */
  public static function find(array $filter = null) {
    $categoryGroups = [];
    foreach (Database::getInstance()->find('CategoryGroup', $filter) as $data) {
      $categoryGroups[] = new CategoryGroup($data);
    }
    return $categoryGroups;
  }
}
