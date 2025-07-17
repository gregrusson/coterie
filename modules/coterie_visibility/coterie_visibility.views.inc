<?php

/**
 * Implements hook_views_data_alter().
 */
function coterie_visibility_views_data_alter(array &$data) {
    // Attach our filter to the 'nid' field on the node table.
    $data['node_field_data']['coterie_visibility_access'] = [
        'title' => t('Coterie Visibility Access Filter'),
        'help' => t('Filters nodes by visibility access.'),
        'filter' => [
            'id' => 'coterie_visibility_access_filter',
        ],
    ];
}
