<?php

namespace Drupal\coterie_relationship;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\coterie_relationship\Entity\Relationship;

/**
 * Provides relationship management utilities.
 */
class RelationshipManager {

    protected $entityTypeManager;

    public function __construct(EntityTypeManagerInterface $entityTypeManager) {
        $this->entityTypeManager = $entityTypeManager;
    }

    /**
     * Check if $source_uid is following $target_uid.
     */
    public function isFollower($source_uid, $target_uid): bool {
        $storage = $this->entityTypeManager->getStorage('relationship');
        $results = $storage->loadByProperties([
                                                  'source_user' => $source_uid,
                                                  'target_user' => $target_uid,
                                                  'type' => 'follower',
                                              ]);
        return !empty($results);
    }

    /**
     * Create a relationship between users.
     */
    public function createRelationship(string $type, int $source_uid, int $target_uid): Relationship {
        /** @var \Drupal\coterie_relationship\Entity\Relationship $relationship */
        $relationship = $this->entityTypeManager->getStorage('relationship')->create([
                                                                                         'type' => $type,
                                                                                         'source_user' => $source_uid,
                                                                                         'target_user' => $target_uid,
                                                                                     ]);
        $relationship->save();
        return $relationship;
    }

    /**
     * Remove a relationship between users.
     */
    public function removeRelationship(string $type, int $source_uid, int $target_uid): void {
        $storage = $this->entityTypeManager->getStorage('relationship');
        $relationships = $storage->loadByProperties([
                                                        'type' => $type,
                                                        'source_user' => $source_uid,
                                                        'target_user' => $target_uid,
                                                    ]);

        foreach ($relationships as $relationship) {
            $relationship->delete();
        }
    }

    /**
     * Get all users who follow the given user.
     */
    public function getFollowers(int $target_uid): array {
        $storage = $this->entityTypeManager->getStorage('relationship');
        $relationships = $storage->loadByProperties([
                                                        'target_user' => $target_uid,
                                                        'type' => 'follower',
                                                    ]);

        $followers = [];
        foreach ($relationships as $relationship) {
            $followers[] = $relationship->get('source_user')->target_id;
        }
        return $followers;
    }

}
