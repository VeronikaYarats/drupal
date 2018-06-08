<?php

namespace Drupal\custom_entity;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\custom_entity\Entity\DefaultEntityInterface;

/**
 * Defines the storage handler class for Default entity entities.
 *
 * This extends the base storage class, adding required special handling for
 * Default entity entities.
 *
 * @ingroup custom_entity
 */
class DefaultEntityStorage extends SqlContentEntityStorage implements DefaultEntityStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(DefaultEntityInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {default_entity_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {default_entity_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(DefaultEntityInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {default_entity_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('default_entity_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
