<?php

namespace Drupal\custom_entity\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\node\Entity\Node;
use Drupal\Core\Database\Database;

/**
 * Provides list articles Block.
 *
 * @Block(
 *   id = "color articles",
 *   admin_label = @Translation("Color articles"),
 *   category = @Translation("Drupal 8 school"),
 * )
 */
class Article extends BlockBase
{
    /**
     * {@inheritdoc}
     */
    public function build()
    {
        $list = [
            '#theme' => 'item_list',
            '#list_type' => 'ul',
            '#attached' => [
              'library' =>  [
                 'custom_entity/article-color'
                 ],
             ],
            '#items' => []
        ];
        $connection = Database::getConnection('default_entity');
        $query = $connection->select('default_entity__article_reference');
        $query->fields('default_entity__article_reference', ['article_reference_target_id']);
        $custom_entities = $query->execute()->fetchAll();
        foreach ($custom_entities as $entity) {
            $article = Node::load($entity->article_reference_target_id);
            $list['#attached']['drupalSettings']['articles']['colors'][] = $article->get('field_color')->value;
            $list['#items'][]['name'] =  [
                $article->getTitle()
            ];
        }

        return $list;
    }
}

