<?php

namespace Drupal\coterie_post\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

class PostForm extends ContentEntityForm {
    public function buildForm(array $form, FormStateInterface $form_state) {
        $form = parent::buildForm($form, $form_state);
        // Custom alterations if needed.
        return $form;
    }

    public function save(array $form, FormStateInterface $form_state) {
        $entity = $this->entity;
        $status = parent::save($form, $form_state);
        if ($status === SAVED_NEW) {
            drupal_set_message(t('Created new post %label.', ['%label' => $entity->label()]));
        }
        else {
            drupal_set_message(t('Saved post %label.', ['%label' => $entity->label()]));
        }
        $form_state->setRedirect('entity.coterie_post.canonical', ['coterie_post' => $entity->id()]);
    }
}
