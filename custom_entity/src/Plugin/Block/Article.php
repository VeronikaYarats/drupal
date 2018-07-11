<?php

namespace Drupal\custom_entity\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\node\Entity\Node;
use Drupal\Core\Database\Database;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Database\Driver\mysql\Connection;

/**
 * Provides list articles Block.
 *
 * @Block(
 *   id = "color articles",
 *   admin_label = @Translation("Color articles"),
 *   category = @Translation("Drupal 8 school"),
 * )
 */
class Article extends BlockBase implements ContainerFactoryPluginInterface
{
    /**
     * @var Database
     */
    private $connection;

    /**
     * @var EntityTypeManagerInterface
     */
    private $entityTypeManager;

    /**
     * @param ContainerInterface $container
     * @param array $configuration
     * @param string $plugin_id
     * @param mixed $plugin_definition
     * @return static
     */
    public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
        return new static(
            $configuration,
            $plugin_id,
            $plugin_definition,
            $container->get('database'),
            $container->get('entity_type.manager')
        );
    }

    /**
     * @param array $configuration
     * @param string $plugin_id
     * @param mixed $plugin_definition
     * @param \Drupal\Core\Entity\Query\QueryInterface $query
     * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
     * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
     */
    public function __construct(
        array $configuration,
        $plugin_id,
        $plugin_definition,
        Connection $connection,
        EntityTypeManagerInterface $entityTypeManager
    ) {
        parent::__construct($configuration, $plugin_id, $plugin_definition);
        $this->connection = $connection;
        $this->entityTypeManager = $entityTypeManager;
    }

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

        $query = $this->connection->select('default_entity__article_reference');
        $query->fields('default_entity__article_reference', ['article_reference_target_id']);
        $custom_entities = $query->execute()->fetchAll();
        foreach ($custom_entities as $entity) {
            $article = $this->entityTypeManager->getStorage('node')->load($entity->article_reference_target_id);
            $list['#attached']['drupalSettings']['articles']['colors'][] = $article->get('field_color')->value;
            $list['#items'][]['name'] =  [
                $article->getTitle()
            ];
        }

        return $list;
    }
}

