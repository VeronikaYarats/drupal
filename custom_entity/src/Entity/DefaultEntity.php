<?php

namespace Drupal\custom_entity\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\RevisionableContentEntityBase;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;

/**
 * Defines the Default entity entity.
 *
 * @ingroup custom_entity
 *
 * @ContentEntityType(
 *   id = "default_entity",
 *   label = @Translation("Default entity"),
 *   handlers = {
 *     "storage" = "Drupal\custom_entity\DefaultEntityStorage",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\custom_entity\DefaultEntityListBuilder",
 *     "views_data" = "Drupal\custom_entity\Entity\DefaultEntityViewsData",
 *     "translation" = "Drupal\custom_entity\DefaultEntityTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\custom_entity\Form\DefaultEntityForm",
 *       "add" = "Drupal\custom_entity\Form\DefaultEntityForm",
 *       "edit" = "Drupal\custom_entity\Form\DefaultEntityForm",
 *       "delete" = "Drupal\custom_entity\Form\DefaultEntityDeleteForm",
 *     },
 *     "access" = "Drupal\custom_entity\DefaultEntityAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\custom_entity\DefaultEntityHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "default_entity",
 *   data_table = "default_entity_field_data",
 *   revision_table = "default_entity_revision",
 *   revision_data_table = "default_entity_field_revision",
 *   translatable = TRUE,
 *   admin_permission = "administer default entity entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "vid",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "status" = "status"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/custom_entities/default_entity/{default_entity}",
 *     "add-form" = "/admin/structure/custom_entities/default_entity/add",
 *     "edit-form" = "/admin/structure/custom_entities/default_entity/{default_entity}/edit",
 *     "delete-form" = "/admin/structure/custom_entities/default_entity/{default_entity}/delete",
 *     "version-history" = "/admin/structure/custom_entities/default_entity/{default_entity}/revisions",
 *     "revision" = "/admin/structure/custom_entities/default_entity/{default_entity}/revisions/{default_entity_revision}/view",
 *     "revision_revert" = "/admin/structure/custom_entities/default_entity/{default_entity}/revisions/{default_entity_revision}/revert",
 *     "revision_delete" = "/admin/structure/custom_entities/default_entity/{default_entity}/revisions/{default_entity_revision}/delete",
 *     "translation_revert" = "/admin/structure/custom_entities/default_entity/{default_entity}/revisions/{default_entity_revision}/revert/{langcode}",
 *     "collection" = "/admin/structure/custom_entities/default_entity",
 *   },
 *   field_ui_base_route = "default_entity.settings"
 * )
 */
class DefaultEntity extends RevisionableContentEntityBase implements DefaultEntityInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'user_id' => \Drupal::currentUser()->id(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function urlRouteParameters($rel) {
    $uri_route_parameters = parent::urlRouteParameters($rel);

    if ($rel === 'revision_revert' && $this instanceof RevisionableInterface) {
      $uri_route_parameters[$this->getEntityTypeId() . '_revision'] = $this->getRevisionId();
    }
    elseif ($rel === 'revision_delete' && $this instanceof RevisionableInterface) {
      $uri_route_parameters[$this->getEntityTypeId() . '_revision'] = $this->getRevisionId();
    }

    return $uri_route_parameters;
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);

    foreach (array_keys($this->getTranslationLanguages()) as $langcode) {
      $translation = $this->getTranslation($langcode);

      // If no owner has been set explicitly, make the anonymous user the owner.
      if (!$translation->getOwner()) {
        $translation->setOwnerId(0);
      }
    }

    // If no revision author has been set explicitly, make the default_entity owner the
    // revision author.
    if (!$this->getRevisionUser()) {
      $this->setRevisionUserId($this->getOwnerId());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

    /**
     * {@inheritdoc}
     */
    public function getDescription() {
      return $this->get('description')->value;
    }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isPublished() {
    return (bool) $this->getEntityKey('status');
  }

  /**
   * {@inheritdoc}
   */
  public function setPublished($published) {
    $this->set('status', $published ? TRUE : FALSE);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Default entity entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Default entity entity.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Publishing status'))
      ->setDescription(t('A boolean indicating whether the Default entity is published.'))
      ->setRevisionable(TRUE)
      ->setDefaultValue(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => -3,
      ]);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    $fields['revision_translation_affected'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Revision translation affected'))
      ->setDescription(t('Indicates if the last edit of a translation belongs to current revision.'))
      ->setReadOnly(TRUE)
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE);

    $fields['description'] = BaseFieldDefinition::create('string')
        ->setLabel('Description')
        ->setDescription('Describes smth')
        ->setRevisionable(TRUE)
        ->setTranslatable(TRUE)
        ->setDefaultValue('')
        ->setDisplayOptions('view', [
            'label' => 'above',
            'type' => 'string',
            'weight' => -4,
        ])
        ->setDisplayOptions('form', [
            'type' => 'string_textfield',
            'weight' => -4,
        ])
        ->setDisplayConfigurable('form', TRUE)
        ->setDisplayConfigurable('view', TRUE)
        ->setRequired(TRUE);

  $fields['article_reference'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Article'))
      ->setDescription(t('Refers to article'))
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setSetting('target_type', 'node')
      ->setDefaultValue('')
      ->setDisplayOptions('form', [
          'type' => 'dynamic_entity_reference_default',
          'weight' => 0,
      ]);
      return $fields;
  }

}
