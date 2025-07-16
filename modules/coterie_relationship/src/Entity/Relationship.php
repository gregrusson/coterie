<?php

namespace Drupal\coterie_relationship\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines the Relationship entity.
 *
 * @ContentEntityType(
 *   id = "relationship",
 *   label = @Translation("Coterie Relationship"),
 *   base_table = "coterie_relationship",
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *     "label" = "type"
 *   },
 *   handlers = {
 *     "access" = "Drupal\Core\Entity\EntityAccessControlHandler",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "form" = {
 *       "default" = "Drupal\Core\Entity\ContentEntityForm",
 *     },
 *     "list_builder" = "Drupal\Core\Entity\EntityListBuilder"
 *   },
 *   admin_permission = "administer relationships",
 *   field_ui_base_route = "entity.relationship.admin_form",
 * )
 */
class Relationship extends ContentEntityBase
{

    /**
     * Defines fields for the Relationship entity.
     */
    public static function baseFieldDefinitions(EntityTypeInterface $entity_type)
    {
        $fields = parent::baseFieldDefinitions($entity_type);

        $fields['type'] = BaseFieldDefinition::create('string')
                                             ->setLabel(t('Type'))
                                             ->setDescription(t('Type of relationship (e.g., follower, friend, blocked).'))
                                             ->setRequired(TRUE)
                                             ->setSettings(['max_length' => 64]);

        $fields['source_user'] = BaseFieldDefinition::create('entity_reference')
                                                    ->setLabel(t('Source User'))
                                                    ->setDescription(t('The user initiating the relationship.'))
                                                    ->setSetting('target_type', 'user')
                                                    ->setRequired(TRUE);

        $fields['target_user'] = BaseFieldDefinition::create('entity_reference')
                                                    ->setLabel(t('Target User'))
                                                    ->setDescription(t('The user receiving the relationship.'))
                                                    ->setSetting('target_type', 'user')
                                                    ->setRequired(TRUE);

        $fields['created'] = BaseFieldDefinition::create('created')
                                                ->setLabel(t('Created'))
                                                ->setDescription(t('The time the relationship was created.'));

        return $fields;
    }
}
