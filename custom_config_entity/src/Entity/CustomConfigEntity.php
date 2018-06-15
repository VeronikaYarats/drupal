<?php

namespace Drupal\custom_config_entity\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Defines the Custom config entity.
 *
 * @ConfigEntityType(
 *   id = "custom_config_entity",
 *   label = @Translation("Custom config entity"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\custom_config_entity\CustomConfigEntityListBuilder",
 *     "form" = {
 *       "add" = "Drupal\custom_config_entity\Form\CustomConfigEntityForm",
 *       "edit" = "Drupal\custom_config_entity\Form\CustomConfigEntityForm",
 *       "delete" = "Drupal\custom_config_entity\Form\CustomConfigEntityDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\custom_config_entity\CustomConfigEntityHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "custom_config_entity",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *     "title" = "title",
 *     "description" = "description"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/custom_entities/custom_config_entity/{custom_config_entity}",
 *     "add-form" = "/admin/structure/custom_entities/custom_config_entity/add",
 *     "edit-form" = "/admin/structure/custom_entities/custom_config_entity/{custom_config_entity}/edit",
 *     "delete-form" = "/admin/structure/custom_entities/custom_config_entity/{custom_config_entity}/delete",
 *     "collection" = "/admin/structure/custom_entities/custom_config_entity"
 *   }
 * )
 */
class CustomConfigEntity extends ConfigEntityBase implements CustomConfigEntityInterface {

    /**
     * The Custom config entity ID.
     *
     * @var string
     */
    protected $id;

    /**
     * The Custom config entity label.
     *
     * @var string
     */
    protected $label;
    /**
     * The Custom config entity title.
     *
     * @var string
     */
    public $title;

    /**
     * The Custom config entity description.
     *
     * @var string
     */
    public $description;
}
