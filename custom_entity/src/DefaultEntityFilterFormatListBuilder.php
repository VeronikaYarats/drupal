<?php

namespace Drupal\custom_entity;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;
use Drupal\filter\FilterFormatListBuilder;

class DefaultEntityFilterFormatListBuilder extends FilterFormatListBuilder
{
    /**
     * {@inheritdoc}
     */
    public function buildHeader() {
        $header['id'] = $this->t('Default entity ID');
        $header['name'] = $this->t('Name');
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
        return $row;
    }
}
