<?php

namespace Core;

use JsonSerializable;

/** Model Base */
abstract class Entity implements JsonSerializable {
  protected array $data;

  /**
   * Create new instance of Entity
   * 
   * @param array $data Data
   */
  protected function __construct(array $data) {
    $this->data = $data;
  }

  /**
   * Serialize to JSON Object
   * 
   * @return array JSON Object
   */
  public abstract function jsonSerialize();
}