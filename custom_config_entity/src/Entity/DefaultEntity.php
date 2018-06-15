<?php

namespace Drupal\custom_config_entity\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Defines the Default entity entity.
 *
 * @ConfigEntityType(
 *   id = "default_entity",
 *   label = @Translation("Default entity"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\custom_config_entity\DefaultEntityListBuilder",
 *     "form" = {
 *       "add" = "Drupal\custom_config_entity\Form\DefaultEntityForm",
 *       "edit" = "Drupal\custom_config_entity\Form\DefaultEntityForm",
 *       "delete" = "Drupal\custom_config_entity\Form\DefaultEntityDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\custom_config_entity\DefaultEntityHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "default_entity",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *     "title" = "title",
 *     "description" = "description"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/default_entity/{default_entity}",
 *     "add-form" = "/admin/structure/default_entity/add",
 *     "edit-form" = "/admin/structure/default_entity/{default_entity}/edit",
 *     "delete-form" = "/admin/structure/default_entity/{default_entity}/delete",
 *     "collection" = "/admin/structure/default_entity"
 *   }
 * )
 */
class DefaultEntity extends ConfigEntityBase implements DefaultEntityInterface {

    /**
     * The Default entity ID.
     *
     * @var string
     */
    protected $id;

    /**
     * The Default entity label.
     *
     * @var string
     */
    protected $label;
    /**
     * The Default entity title.
     *
     * @var string
     */
    public $title;

    /**
     * The Default entity description.
     *
     * @var string
     */
    public $description;
}
