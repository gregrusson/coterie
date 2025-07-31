<?php
namespace Drupal\coterie\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a block to display the quick coterie post form.
 *
 * @Block(
 *   id = "coterie_post_quick_form_block",
 *   admin_label = @Translation("Coterie Post Quick Form")
 * )
 */
class CoteriePostQuickFormBlock extends BlockBase {

    public function build() {
        if (!\Drupal::currentUser()->isAuthenticated()) {
            return [];
        }

        return \Drupal::formBuilder()->getForm('Drupal\coterie\Form\CoteriePostQuickForm');
    }

}
