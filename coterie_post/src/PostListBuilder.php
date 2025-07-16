<?php

namespace Drupal\coterie_post;

use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

class PostListBuilder extends EntityListBuilder {
    public function buildHeader() {
        $header['id'] = $this->t('ID');
        $header['title'] = $this->t('Title');
        $header['author'] = $this->t('Author');
        return $header + parent::buildHeader();
    }

    public function buildRow(EntityInterface $entity) {
        /* @var \Drupal\coterie_post\Entity\Post $entity */
        $row['id'] = $entity->id();
        $row['title'] = $entity->toLink();
        $row['author'] = $entity->getOwner()->toLink();
        return $row + parent::buildRow($entity);
    }
}
