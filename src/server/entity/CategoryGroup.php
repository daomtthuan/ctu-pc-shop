<?php

namespace Entity;

use Core\Entity;

class CategoryGroup extends Entity {
  private string $name;

  /** 
   * Create new instance of CategoryGroup
   * 
   * @param array $data Data CategoryGroup
   */
  public function __construct(array $data) {
    parent::__construct($data);
    $this->name = (string)$data['name'];
  }

  public function jsonSerialize() {
    return [
      'id' => $this->getId(),
      'name' => $this->getName(),
      'state' => $this->getState()
    ];
  }

  /**
   * Get the value of name
   * 
   * @return string Name
   */
  public function getName() {
    return $this->name;
  }

  /**
   * Set the value of name
   * 
   * @param string name
   */
  public function setName(string $name) {
    $this->name = $name;
  }
}
