<?php

namespace Drupal\custom_entity\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Default entity entities.
 *
 * @ingroup custom_entity
 */
interface DefaultEntityInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Default entity name.
   *
   * @return string
   *   Name of the Default entity.
   */
  public function getName();

  /**
   * Sets the Default entity name.
   *
   * @param string $name
   *   The Default entity name.
   *
   * @return \Drupal\custom_entity\Entity\DefaultEntityInterface
   *   The called Default entity entity.
   */
  public function setName($name);

  /**
   * Gets the Default entity creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Default entity.
   */
  public function getCreatedTime();

  /**
   * Sets the Default entity creation timestamp.
   *
   * @param int $timestamp
   *   The Default entity creation timestamp.
   *
   * @return \Drupal\custom_entity\Entity\DefaultEntityInterface
   *   The called Default entity entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Default entity published status indicator.
   *
   * Unpublished Default entity are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Default entity is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Default entity.
   *
   * @param bool $published
   *   TRUE to set this Default entity to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\custom_entity\Entity\DefaultEntityInterface
   *   The called Default entity entity.
   */
  public function setPublished($published);

  /**
   * Gets the Default entity revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Default entity revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\custom_entity\Entity\DefaultEntityInterface
   *   The called Default entity entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Default entity revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Default entity revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\custom_entity\Entity\DefaultEntityInterface
   *   The called Default entity entity.
   */
  public function setRevisionUserId($uid);

}
