<?php

namespace Drupal\custom_config_entity\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class DefaultEntityForm.
 */
class DefaultEntityForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
      $form = parent::form($form, $form_state);

      $default_entity = $this->entity;
      $form['label'] = [
          '#type' => 'textfield',
          '#title' => $this->t('Label'),
          '#maxlength' => 255,
          '#default_value' => $default_entity->label(),
          '#description' => $this->t("Label for the Default entity."),
          '#required' => TRUE,
      ];

      $form['id'] = [
          '#type' => 'machine_name',
          '#default_value' => $default_entity->id(),
          '#machine_name' => [
              'exists' => '\Drupal\custom_config_entity\Entity\DefaultEntity::load',
          ],
          '#disabled' => !$default_entity->isNew(),
      ];

      $form['title'] = [
          '#type' => 'textfield',
          '#title' => $this->t('Title'),
          '#maxlength' => 255,
          '#default_value' => $default_entity->title,
          '#description' => $this->t("Title for the Default entity."),
          '#required' => TRUE,
      ];

      $form['description'] = [
          '#type' => 'textarea',
          '#title' => $this->t('Description'),
          '#maxlength' => 255,
          '#default_value' => $default_entity->description,
          '#description' => $this->t("Description for the Default entity."),
          '#required' => FALSE,
      ];

      return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $default_entity = $this->entity;
    $status = $default_entity->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Default entity.', [
          '%label' => $default_entity->label()
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Default entity.', [
          '%label' => $default_entity->label()
        ]));
    }
    $form_state->setRedirectUrl($default_entity->toUrl('collection'));
  }

}
