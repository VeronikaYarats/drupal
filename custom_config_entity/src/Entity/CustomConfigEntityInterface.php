<?php

namespace Drupal\custom_config_entity\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface for defining Custom config entity entities.
 */
interface CustomConfigEntityInterface extends ConfigEntityInterface {

  // Add get/set methods for your configuration properties here.

    /**
     * Get Custom config entity title.
     *
     * @return string
     */
    public function getTitle();

    /**
     * Get Custom config entity description.
     *
     * @return string
     */
    public function getDescription();
}
