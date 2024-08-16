<?php

/**
 * @file
 * Contains the hook implementations for the custom Views style plugin.
 */

/**
 * Implements hook_views_data_alter().
 */
function vvjt_views_data_alter(array &$data): void {
  // Define the style plugin schema.
  $data['views_style_plugin']['views_vvjt'] = [
    'type' => 'views_style',
    'label' => t('Views Vanilla JavaScript Tabs'),
    'mapping' => [
      'animation' => [
        'type' => 'string',
        'label' => t('Animation Type'),
      ],
      'vertical_tabs' => [
        'type' => 'boolean',
        'label' => t('Enable Vertical Tabs'),
      ],
      'max_width' => [
        'type' => 'string',
        'label' => t('Max Width'),
      ],
      'available_breakpoints' => [
        'type' => 'string',
        'label' => t('Available Breakpoints'),
      ],
      'unique_id' => [
        'type' => 'string',
        'label' => t('Unique ID for the view display'),
      ],
      'enable_css' => [
        'type' => 'boolean',
        'label' => t('Enable CSS Library'),
      ],
      'background_panes' => [
        'type' => 'string',
        'label' => t('Background Color for Panes'),
      ],
      'background_buttons' => [
        'type' => 'string',
        'label' => t('Background Color for Buttons'),
      ],
      'disable_background' => [
        'type' => 'boolean',
        'label' => t('Disable Background Colors'),
      ],
    ],
  ];
}
