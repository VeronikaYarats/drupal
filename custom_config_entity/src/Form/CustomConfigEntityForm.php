<?php

namespace Drupal\custom_config_entity\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class CustomConfigEntityForm.
 */
class CustomConfigEntityForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
      $form = parent::form($form, $form_state);

      $custom_config_entity = $this->entity;
      $form['label'] = [
          '#type' => 'textfield',
          '#title' => $this->t('Label'),
          '#maxlength' => 255,
          '#default_value' => $custom_config_entity->label(),
          '#description' => $this->t("Label for the Custom config entity."),
          '#required' => TRUE,
      ];

      $form['id'] = [
          '#type' => 'machine_name',
          '#default_value' => $custom_config_entity->id(),
          '#machine_name' => [
              'exists' => '\Drupal\custom_config_entity\Entity\CustomConfigEntity::load',
          ],
          '#disabled' => !$custom_config_entity->isNew(),
      ];

      $form['title'] = [
          '#type' => 'textfield',
          '#title' => $this->t('Title'),
          '#maxlength' => 255,
          '#default_value' => $custom_config_entity->getTitle(),
          '#description' => $this->t("Title for the Custom config entity."),
          '#required' => TRUE,
      ];

      $form['description'] = [
          '#type' => 'textarea',
          '#title' => $this->t('Description'),
          '#maxlength' => 255,
          '#default_value' => $custom_config_entity->getDescription(),
          '#description' => $this->t("Description for the Custom config entity."),
          '#required' => FALSE,
      ];

      return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $custom_config_entity = $this->entity;
    $status = $custom_config_entity->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Custom config entity.', [
          '%label' => $custom_config_entity->label()
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Custom config entity.', [
          '%label' => $custom_config_entity->label()
        ]));
    }
    $form_state->setRedirectUrl($custom_config_entity->toUrl('collection'));
  }

}
