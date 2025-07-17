<?php

namespace Drupal\coterie_relationship\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\user\Entity\User;

/**
 * Handles follow actions.
 */
class RelationshipController extends ControllerBase {

    public function follow($user) {
        $current_user = $this->currentUser();
        $target_user = User::load($user);

        if (!$target_user || $current_user->id() == $target_user->id()) {
            $this->messenger()->addError('You cannot follow this user.');
            return new RedirectResponse('/user/' . $user);
        }

        /** @var \Drupal\coterie_relationship\RelationshipManager $manager */
        $manager = \Drupal::service('coterie_relationship.manager');

        if ($manager->isFollower($current_user->id(), $target_user->id())) {
            $this->messenger()->addWarning('You are already following this user.');
        }
        else {
            $manager->createRelationship('follower', $current_user->id(), $target_user->id());
            $this->messenger()->addStatus('You are now following this user.');

            \Drupal::logger('coterie_relationship')->notice('User @uid followed user @target.', [
                '@uid' => $current_user->id(),
                '@target' => $target_user->id(),
            ]);
        }

        return new RedirectResponse('/user/' . $user);
    }

    public function unfollow($user) {
        $current_user = $this->currentUser();
        $target_user = User::load($user);

        if (!$target_user || $current_user->id() == $target_user->id()) {
            $this->messenger()->addError('You cannot unfollow this user.');
            return new RedirectResponse('/user/' . $user);
        }

        $manager = \Drupal::service('coterie_relationship.manager');

        if (!$manager->isFollower($current_user->id(), $target_user->id())) {
            $this->messenger()->addWarning('You are not following this user.');
        }
        else {
            $manager->removeRelationship('follower', $current_user->id(), $target_user->id());
            $this->messenger()->addStatus('You have unfollowed this user.');

            $this->logger('coterie_relationship')->notice('User @uid unfollowed user @target.', [
                '@uid' => $current_user->id(),
                '@target' => $target_user->id(),
            ]);
        }

        return new RedirectResponse('/user/' . $user);
    }

    public function followersPage($user) {
        $target_user = User::load($user);
        if (!$target_user) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
        }

        $manager = \Drupal::service('coterie_relationship.manager');
        $follower_ids = $manager->getFollowers($target_user->id());

        $follower_users = \Drupal::entityTypeManager()->getStorage('user')->loadMultiple($follower_ids);

        $items = [];
        foreach ($follower_users as $account) {
            $items[] = $account->toLink($account->getDisplayName())->toRenderable();
        }

        return [
            '#theme' => 'item_list',
            '#title' => $this->t('Followers of @name', ['@name' => $target_user->getDisplayName()]),
            '#items' => $items,
        ];
    }

    public function followingPage($user) {
        $source_user = User::load($user);
        if (!$source_user) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
        }

        $manager = \Drupal::service('coterie_relationship.manager');
        $storage = \Drupal::entityTypeManager()->getStorage('relationship');
        $relationships = $storage->loadByProperties([
                                                        'type' => 'follower',
                                                        'source_user' => $source_user->id(),
                                                    ]);

        $target_ids = [];
        foreach ($relationships as $rel) {
            $target_ids[] = $rel->get('target_user')->target_id;
        }

        $target_users = \Drupal::entityTypeManager()->getStorage('user')->loadMultiple($target_ids);

        $items = [];
        foreach ($target_users as $account) {
            $items[] = $account->toLink($account->getDisplayName())->toRenderable();
        }

        return [
            '#theme' => 'item_list',
            '#title' => $this->t('@name is following', ['@name' => $source_user->getDisplayName()]),
            '#items' => $items,
        ];
    }

}
