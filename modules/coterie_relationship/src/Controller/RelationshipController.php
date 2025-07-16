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
        }

        return new RedirectResponse('/user/' . $user);
    }
}
