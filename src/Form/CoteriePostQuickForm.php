<?php
namespace Drupal\coterie\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;

/**
 * Provides a quick post form for Coterie Posts.
 */
class CoteriePostQuickForm extends FormBase {

    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'coterie_post_quick_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        $form['title'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Title'),
            '#required' => TRUE,
            '#attributes' => ['placeholder' => $this->t("What's on your mind?")],
        ];

        $form['body'] = [
            '#type' => 'textarea',
            '#title' => $this->t(''),
            '#required' => FALSE,
            '#attributes' => ['placeholder' => $this->t("Add more details...")],
        ];

        $form['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Post'),
            '#attributes' => ['class' => ['coterie-post-submit']],
        ];

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $title = $form_state->getValue('title');
        $body = $form_state->getValue('body');

        $node = Node::create([
                                 'type' => 'coterie_post',
                                 'title' => $title,
                                 'body' => [
                                     'value' => $body,
                                     'format' => 'basic_html',
                                 ],
                                 'uid' => $this->currentUser()->id(),
                                 'status' => 1,
                             ]);

        $node->save();

        $this->messenger()->addStatus($this->t('Your post has been published.'));
        $form_state->setRedirect('<current>');
    }
}
