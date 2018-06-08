<?php

namespace Drupal\custom_entity\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\custom_entity\Entity\DefaultEntityInterface;

/**
 * Class DefaultEntityController.
 *
 *  Returns responses for Default entity routes.
 */
class DefaultEntityController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a Default entity  revision.
   *
   * @param int $default_entity_revision
   *   The Default entity  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($default_entity_revision) {
    $default_entity = $this->entityManager()->getStorage('default_entity')->loadRevision($default_entity_revision);
    $view_builder = $this->entityManager()->getViewBuilder('default_entity');

    return $view_builder->view($default_entity);
  }

  /**
   * Page title callback for a Default entity  revision.
   *
   * @param int $default_entity_revision
   *   The Default entity  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($default_entity_revision) {
    $default_entity = $this->entityManager()->getStorage('default_entity')->loadRevision($default_entity_revision);
    return $this->t('Revision of %title from %date', ['%title' => $default_entity->label(), '%date' => format_date($default_entity->getRevisionCreationTime())]);
  }

  /**
   * Generates an overview table of older revisions of a Default entity .
   *
   * @param \Drupal\custom_entity\Entity\DefaultEntityInterface $default_entity
   *   A Default entity  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(DefaultEntityInterface $default_entity) {
    $account = $this->currentUser();
    $langcode = $default_entity->language()->getId();
    $langname = $default_entity->language()->getName();
    $languages = $default_entity->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $default_entity_storage = $this->entityManager()->getStorage('default_entity');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $default_entity->label()]) : $this->t('Revisions for %title', ['%title' => $default_entity->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all default entity revisions") || $account->hasPermission('administer default entity entities')));
    $delete_permission = (($account->hasPermission("delete all default entity revisions") || $account->hasPermission('administer default entity entities')));

    $rows = [];

    $vids = $default_entity_storage->revisionIds($default_entity);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\custom_entity\DefaultEntityInterface $revision */
      $revision = $default_entity_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $default_entity->getRevisionId()) {
          $link = $this->l($date, new Url('entity.default_entity.revision', ['default_entity' => $default_entity->id(), 'default_entity_revision' => $vid]));
        }
        else {
          $link = $default_entity->link($date);
        }

        $row = [];
        $column = [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{% trans %}{{ date }} by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}',
            '#context' => [
              'date' => $link,
              'username' => \Drupal::service('renderer')->renderPlain($username),
              'message' => ['#markup' => $revision->getRevisionLogMessage(), '#allowed_tags' => Xss::getHtmlTagList()],
            ],
          ],
        ];
        $row[] = $column;

        if ($latest_revision) {
          $row[] = [
            'data' => [
              '#prefix' => '<em>',
              '#markup' => $this->t('Current revision'),
              '#suffix' => '</em>',
            ],
          ];
          foreach ($row as &$current) {
            $current['class'] = ['revision-current'];
          }
          $latest_revision = FALSE;
        }
        else {
          $links = [];
          if ($revert_permission) {
            $links['revert'] = [
              'title' => $this->t('Revert'),
              'url' => $has_translations ?
              Url::fromRoute('entity.default_entity.translation_revert', ['default_entity' => $default_entity->id(), 'default_entity_revision' => $vid, 'langcode' => $langcode]) :
              Url::fromRoute('entity.default_entity.revision_revert', ['default_entity' => $default_entity->id(), 'default_entity_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.default_entity.revision_delete', ['default_entity' => $default_entity->id(), 'default_entity_revision' => $vid]),
            ];
          }

          $row[] = [
            'data' => [
              '#type' => 'operations',
              '#links' => $links,
            ],
          ];
        }

        $rows[] = $row;
      }
    }

    $build['default_entity_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
