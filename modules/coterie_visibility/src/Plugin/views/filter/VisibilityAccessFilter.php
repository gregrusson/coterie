<?php

namespace Drupal\coterie_visibility\Plugin\views\filter;

use Drupal\views\Plugin\views\filter\FilterPluginBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\node\NodeInterface;

/**
 * Filters nodes by visibility rules for the current user.
 *
 * @ViewsFilter("coterie_visibility_access_filter")
 */
class VisibilityAccessFilter extends FilterPluginBase {

    /**
     * {@inheritdoc}
     */
    public function query() {
        $this->ensureMyTable();
        $this->realField = 'nid';

        $current_user = \Drupal::currentUser();
        $node_storage = \Drupal::entityTypeManager()->getStorage('node');

        // Load node IDs for the current view context.
        $query = $node_storage->getQuery()
                              ->accessCheck(TRUE)
                              ->condition('status', 1);

        $nids = $query->execute();

        if (empty($nids)) {
            $this->query->addWhereExpression(0, "1 = 0"); // No results.
            return;
        }

        $viewable_nids = [];
        $nodes = $node_storage->loadMultiple($nids);

        foreach ($nodes as $node) {
            if ($this->userCanViewNode($node, $current_user)) {
                $viewable_nids[] = $node->id();
            }
        }

//        \Drupal::logger('coterie_visibility')->debug('Loaded node IDs: @nids', [
//            '@nids' => implode(', ', $viewable_nids),
//        ]);

        if (empty($viewable_nids)) {
            $viewable_nids = [0]; // Force no results
        }

        $this->query->addWhereExpression($this->options['group'], "{$this->tableAlias}.{$this->realField} IN (:nids[])", [':nids[]' => $viewable_nids]);
    }

    /**
     * Custom logic: check if user can view a node based on visibility field.
     */
    protected function userCanViewNode(NodeInterface $node, AccountInterface $account): bool {
        if (!$node->hasField('field_coterie_visibility') || $node->get('field_coterie_visibility')->isEmpty()) {
            return TRUE; // Default to visible if unset
        }

        $visibility = $node->get('field_coterie_visibility')->value ?? 'public';
        $owner_id = $node->getOwnerId();
        $viewer_id = $account->id();

/*        \Drupal::logger('coterie_visibility')->debug(
            'Checking visibility: node @nid, visibility=@visibility, owner_id=@owner, viewer_id=@viewer, viewer_authenticated=@auth',
            [
                '@nid' => $node->id(),
                '@visibility' => $visibility,
                '@owner' => $owner_id,
                '@viewer' => $viewer_id,
                '@auth' => $account->isAuthenticated() ? 'yes' : 'no',
            ]
        );*/

        switch ($visibility) {
            case 'public':
                return TRUE;

            case 'members':
                return $account->isAuthenticated();

            case 'private':
                return $account->isAuthenticated() && $viewer_id === $owner_id;

            case 'followers':
                if (!$account->isAuthenticated()) {
                    return FALSE;
                }

                // Call your custom relationship service.
                /** @var \Drupal\coterie_relationship\RelationshipManager $manager */
                $manager = \Drupal::service('coterie_relationship.manager');
                return $manager->isFollower($viewer_id, $owner_id);
        }

        return FALSE;
    }

    /**
     * {@inheritdoc}
     * @param false $short
     */
    public function adminLabel($short = FALSE) {
        return $this->t('Coterie Visibility Access Filter');
    }
}
