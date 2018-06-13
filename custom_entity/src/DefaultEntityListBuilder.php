<?php

namespace Drupal\custom_entity;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Language\LanguageManagerInterface;

/**
 * Defines a class to build a listing of Default entity entities.
 *
 * @ingroup custom_entity
 */
class DefaultEntityListBuilder extends EntityListBuilder
{
    /**
     * @var LanguageManagerInterface
     */
    private $languageManager;

    /**
     * @param EntityTypeInterface $entity_type
     * @param EntityStorageInterface $storage
     * @param LanguageManagerInterface $date_formatter
     */
    public function __construct(EntityTypeInterface $entity_type, EntityStorageInterface $storage, LanguageManagerInterface $languageManager) {
        parent::__construct($entity_type, $storage);

        $this->languageManager = $languageManager;
    }

    /**
     * {@inheritdoc}
     */
    public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
        return new static(
            $entity_type,
            $container->get('entity.manager')->getStorage($entity_type->id()),
            $container->get('language_manager')
        );
    }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Default entity ID');
    $header['name'] = $this->t('Name');
    $header['description'] = $this->t('Description');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\custom_entity\Entity\DefaultEntity */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.default_entity.edit_form',
      ['default_entity' => $entity->id()]
    );
    $row['description'] = $entity->getDescription();
    return $row + parent::buildRow($entity);
  }

    /**
     * Filter entities with current language.
     *
     * {@inheritdoc}
     */
    protected function getEntityIds() {
        $langCode = $this->languageManager->getCurrentLanguage()->getId();
        $query = $this->getStorage()->getQuery()
            ->sort($this->entityType->getKey('id'))
            ->condition('langcode', $langCode, '=');

        // Only add the pager if a limit is specified.
        if ($this->limit) {
            $query->pager($this->limit);
        }
        return $query->execute();
    }
}
