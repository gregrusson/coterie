<?php

namespace Drupal\coterie_post\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines the Post entity.
 *
 * @ContentEntityType(
 *   id = "coterie_post",
 *   label = @Translation("Post"),
 *   base_table = "coterie_post",
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *     "label" = "title"
 *   },
 *   handlers = {
 *     "list_builder" = "Drupal\coterie_post\PostListBuilder",
 *     "form" = {
 *       "default" = "Drupal\coterie_post\Form\PostForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm"
 *     }
 *   },
 *   links = {
 *     "canonical" = "/post/{coterie_post}",
 *     "add-form" = "/admin/content/coterie-post/add",
 *     "edit-form" = "/admin/content/coterie-post/{coterie_post}/edit",
 *     "delete-form" = "/admin/content/coterie-post/{coterie_post}/delete",
 *     "collection" = "/admin/content/coterie-post"
 *   }
 * )
 */
class Post extends ContentEntityBase implements PostInterface {

    public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
        $fields = parent::baseFieldDefinitions($entity_type);

        $fields['title'] = BaseFieldDefinition::create('string')
                                              ->setLabel(t('Title'))
                                              ->setRequired(TRUE)
                                              ->setSettings(['max_length' => 255]);

        $fields['body'] = BaseFieldDefinition::create('text_long')
                                             ->setLabel(t('Body'));

        $fields['author_id'] = BaseFieldDefinition::create('entity_reference')
                                                  ->setLabel(t('Author'))
                                                  ->setSetting('target_type', 'user')
                                                  ->setDefaultValueCallback(static::class . '::getCurrentUserId');

        $fields['created'] = BaseFieldDefinition::create('created')
                                                ->setLabel(t('Created'));

        $fields['changed'] = BaseFieldDefinition::create('changed')
                                                ->setLabel(t('Changed'));

        return $fields;
    }

    public static function getCurrentUserId() {
        return [\Drupal::currentUser()->id()];
    }
}
