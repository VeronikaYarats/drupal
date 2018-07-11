<?php

namespace Drupal\custom_entity\Plugin\rest\resource;

use Drupal\custom_entity\Entity\DefaultEntity;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Psr\Log\LoggerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Database\Driver\mysql\Connection;
use Symfony\Component\HttpFoundation\Request;
use Drupal\rest\ModifiedResourceResponse;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;


/**
 * Provides a resource to get view modes by entity.
 *
 * @RestResource(
 *   id = "custom_entities_rest_resource",
 *   label = @Translation("Get custom entities rest resource"),
 *   uri_paths = {
 *     "canonical" = "/custom_entities_api/custom_entity",
 *     "https://www.drupal.org/link-relations/create" = "/custom_entities_api/custom_entity"
 *   }
 * )
 */
class RestResource extends ResourceBase
{
  /**
   * @var EntityTypeManagerInterface;
   */
  private $entityManager;

  /**
   * @var LanguageManagerInterface
   */
  private $languageManager;

  /**
   * @var Connection
   */
  private $connection;

  /**
   * @var Request
   */
  private $currentRequest;

  /**
   * {@inheritdoc}
   */
  public static function create(
    ContainerInterface $container,
    array $configuration, $plugin_id,
    $plugin_definition
  ) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->getParameter('serializer.formats'),
      $container->get('logger.factory')->get('rest'),
      $container->get('entity_type.manager'),
      $container->get('current_user'),
      $container->get('language_manager'),
      $container->get('database'),
      $container->get('request_stack')->getCurrentRequest()
    );
  }

  /**
   * @param array $configuration
   * @param string $plugin_id
   * @param mixed $plugin_definition
   * @param array $serializer_formats
   * @param LoggerInterface $logger
   * @param EntityTypeManagerInterface  $entity_manager
   * @param AccountProxyInterface $current_user
   * @param LanguageManagerInterface $languageManager
   * @param Connection $connection
   * @param Request $currentRequest
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    array $serializer_formats,
    LoggerInterface $logger,
    EntityTypeManagerInterface  $entity_manager,
    AccountProxyInterface $current_user,
    LanguageManagerInterface $languageManager,
    Connection $connection,
    Request $currentRequest
  ) {
    parent::__construct(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $serializer_formats,
      $logger);
    $this->entityManager = $entity_manager;
    $this->currentUser = $current_user;
    $this->languageManager = $languageManager;
    $this->connection = $connection;
    $this->currentRequest = $currentRequest;
  }

  /**
   * Responds to entity GET requests.
   * @return \Drupal\rest\ResourceResponse
   */
  public function get()
  {
    $response = [];
    $custom_entities = $this->getDefaultEntities();
    $articles = $this->loadArticles();
    foreach ($custom_entities as $entity_id => $entity) {
      $response[$entity_id] = [
        'id' => $entity_id,
        'name' => $entity->getName(),
        'description' => $entity->getDescription()
      ];
      $article_id = $entity->get('article_reference')->first()->getValue()['target_id'];
      if ($article_id && isset($articles[$article_id])) {
        $response[$entity_id]['article'] = [
          'article_id' => $article_id,
          'name' => $articles[$article_id]->getTitle(),
          'color' => $articles[$article_id]->get('field_color')->value
        ];
      }
    }
    return new ResourceResponse(json_encode($response));
  }

  /**
   * @return array
   */
  private function loadArticles()
  {
    $langCode = $this->languageManager->getCurrentLanguage()->getId();
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'article')
      ->condition('langcode', $langCode);
    $articleIds = $query->execute();
    $articles = $this->entityManager->getStorage('node')
      ->loadMultiple(array_values($articleIds));
    return $articles;
  }

  /**
   * Retrieve custom entities with current language code and date in period duration
   *
   * @return array
   */
  private function getDefaultEntities()
  {
    $langCode = $this->languageManager->getCurrentLanguage()->getId();
    $date = $this->currentRequest->get('date');
    $query = \Drupal::entityQuery('default_entity')
      ->condition('langcode', $langCode);
    if ($date) {
      $date = strtotime($date);
      $query->condition('start_date', $date, '>=')
        ->condition('end_date', $date, '<=');
    }
    $custom_entities_ids = $query->execute();

    return DefaultEntity::loadMultiple($custom_entities_ids);
  }

  /**
   * Responds to entity Post requests.
   *
   * @param $data
   * @throws UnprocessableEntityHttpException
   * @return ModifiedResourceResponse
   */
  public function post($data)
  {
    $data = json_decode($data, true);
    try {
      $custom_entity = $this->entityManager->getStorage('default_entity')->create($data);
      if ($this->isValidDate($data)) {
        $duration = [
          [
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date']
          ]
        ];
        $custom_entity->set('field_duration', $duration);
      }
      $custom_entity->save();
    } catch (\Exception $e) {
      throw new UnprocessableEntityHttpException($this->t("Unable to create new entity"), $e);
    }
    return new ModifiedResourceResponse($custom_entity);
  }

  /**
   * Check if dates are set and have a valid format.
   *
   * @param $data array
   * @return bool
   */
  private function isValidDate($data)
  {
    if (!strtotime($data['start_date']) || !strtotime($data['end_date'])) {
        return false;
      }
    if (strtotime($data['start_date']) > strtotime($data['end_date'])) {
      return false;
    }

    return true;
  }
}
