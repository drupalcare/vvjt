<?php

declare(strict_types=1);

namespace Drupal\vvjt;

/**
 * Defines constants for the VVJT module.
 *
 * This class contains constants used across the module for
 * token processing and data attribute mapping.
 */
final class VvjtConstants {

  /**
   * Token namespace for VVJT tokens.
   */
  public const TOKEN_NAMESPACE = 'vvjt';

  /**
   * Plain text token suffix.
   */
  public const TOKEN_PLAIN_SUFFIX = ':plain';

  /**
   * Token pattern for validation.
   *
   * Validates token format: alphanumeric, underscores, optional :plain suffix.
   */
  public const TOKEN_PATTERN = '/^[a-zA-Z0-9_]+(:plain)?$/';

  /**
   * Data attribute prefix for HTML attributes.
   */
  public const DATA_ATTRIBUTE_PREFIX = 'data-';

  /**
   * Data attribute mapping for tabs options.
   *
   * Maps internal option keys to HTML data attribute names.
   */
  public const DATA_ATTRIBUTE_MAP = [
    'animation' => 'animation',
    'tabs_position' => 'tabs-position',
    'unique_id' => 'unique-id',
    'available_breakpoints' => 'available-breakpoints',
    'max_width' => 'max-width',
    'max_height' => 'max-height',
  ];

  /**
   * Boolean option mapping for tabs controls.
   *
   * Maps internal boolean option keys to HTML data attribute names.
   */
  public const BOOLEAN_ATTRIBUTE_MAP = [
    'enable_css' => 'enable-css',
    'disable_background' => 'disable-background',
    'wrap_tabs' => 'wrap-tabs',
  ];

  /**
   * Views integration field type constants.
   */

  /**
   * Integer field type for Views mapping.
   */
  public const VIEWS_TYPE_INTEGER = 'integer';

  /**
   * String field type for Views mapping.
   */
  public const VIEWS_TYPE_STRING = 'string';

  /**
   * Boolean field type for Views mapping.
   */
  public const VIEWS_TYPE_BOOLEAN = 'boolean';

  /**
   * Private constructor to prevent instantiation.
   *
   * This class should only be used for its constants.
   */
  private function __construct() {
    // Prevent instantiation.
  }

}
