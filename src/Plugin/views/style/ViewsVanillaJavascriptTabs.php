<?php

declare(strict_types=1);

namespace Drupal\vvjt\Plugin\views\style;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\style\StylePluginBase;
use Drupal\vvjt\VvjtConstants;

/**
 * Style plugin to render items in Tabs using vanilla JavaScript.
 *
 * @ingroup views_style_plugins
 *
 * @ViewsStyle(
 *   id = "views_vvjt",
 *   title = @Translation("Views Vanilla JavaScript Tabs"),
 *   help = @Translation("Render items in Tabs using vanilla JavaScript."),
 *   theme = "views_view_vvjt",
 *   display_types = { "normal" }
 * )
 */
class ViewsVanillaJavascriptTabs extends StylePluginBase {

  /**
   * Does the style plugin use a row plugin.
   *
   * @var bool
   */
  protected $usesRowPlugin = TRUE;

  /**
   * {@inheritdoc}
   */
  protected $usesRowClass = TRUE;

  /**
   * Cached unique ID for this view display.
   *
   * @var int|null
   */
  protected ?int $cachedUniqueId = NULL;

  /**
   * {@inheritdoc}
   */
  protected function defineOptions(): array {
    $options = parent::defineOptions();
    $options['animation'] = ['default' => 'a-bottom'];
    $options['unique_id'] = ['default' => $this->generateUniqueId()];
    $options['tabs_position'] = ['default' => 'top'];
    $options['max_width'] = ['default' => 300];
    $options['max_height'] = ['default' => 0];
    $options['available_breakpoints'] = ['default' => '992'];
    $options['enable_css'] = ['default' => TRUE];
    $options['disable_background'] = ['default' => FALSE];
    $options['background_buttons'] = ['default' => '#ECECEC'];
    $options['background_panes'] = ['default' => '#F7F7F7'];
    $options['wrap_tabs'] = ['default' => FALSE];
    $options['enable_deeplink'] = ['default' => FALSE];
    $options['deeplink_identifier'] = ['default' => ''];
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state): void {
    parent::buildOptionsForm($form, $form_state);

    $this->setDefaultElementWeights($form);
    $this->buildLayoutSection($form);
    $this->buildDeepLinkingSection($form);
    $this->buildAnimationSection($form);
    $this->buildStylingSection($form);
    $this->buildResponsiveSection($form);
    $this->buildAdvancedOptionsSection($form);
    $this->buildTokenDocumentation($form);
    $this->attachFormAssets($form);
  }

  /**
   * Set weights for default Drupal form elements to ensure proper order.
   *
   * @param array $form
   *   The form array (passed by reference).
   */
  protected function setDefaultElementWeights(array &$form): void {
    $default_elements = [
      'grouping' => -100,
      'row_class' => -90,
      'default_row_class' => -85,
      'uses_fields' => -80,
      'class' => -75,
      'wrapper_class' => -70,
    ];

    foreach ($default_elements as $element_key => $weight) {
      if (isset($form[$element_key])) {
        $form[$element_key]['#weight'] = $weight;
      }
    }
  }

  /**
   * Build layout configuration section.
   *
   * @param array $form
   *   The form array (passed by reference).
   */
  protected function buildLayoutSection(array &$form): void {
    $form['layout_section'] = [
      '#type' => 'details',
      '#title' => $this->t('Layout Settings'),
      '#open' => TRUE,
      '#weight' => -40,
    ];

    $form['layout_section']['tabs_position'] = [
      '#type' => 'select',
      '#title' => $this->t('Tabs Position'),
      '#options' => [
        'top' => $this->t('Top'),
        'right' => $this->t('Right'),
        'bottom' => $this->t('Bottom'),
        'left' => $this->t('Left'),
      ],
      '#default_value' => $this->options['tabs_position'],
      '#description' => $this->t('Select the position for the tab buttons.'),
    ];

    $form['layout_section']['wrap_tabs'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Wrap Tabs When Exceeded'),
      '#default_value' => $this->options['wrap_tabs'],
      '#description' => $this->t('Check this box to wrap tab buttons if they exceed the available space on any screen size. The default is auto-scroll.'),
    ];

    $form['layout_section']['max_width'] = [
      '#type' => 'number',
      '#title' => $this->t('Max Width (px) for Tabs'),
      '#default_value' => $this->options['max_width'],
      '#description' => $this->t('Configure the maximum width for each tab button in a horizontal layout or for the tabs container in a vertical layout.<br><br>

        <strong>Vertical Tabs (Left/Right) (Max width for buttons container):</strong><br>
        - <strong>Auto (0 px):</strong> The tab buttons adjust their width dynamically based on content size and available space, ensuring flexibility across different screen sizes. Note that the max width will collapse on smaller screens. The default max width is set to 300px in the CSS for vertical tabs.<br>
        - <strong>Max Width (in pixels):</strong> Set a maximum width in pixels to allow tab buttons to expand based on content while preventing them from exceeding a specified width. This is ideal for maintaining consistency without sacrificing adaptability.<br><br>

        <strong>Horizontal Tabs (Top/Bottom) (Max width for each button):</strong><br>
        - <strong>Auto (0 px):</strong> The tabs automatically resize to fit their content, providing a responsive layout that adapts to the available space. The max width remains consistent across different screen sizes.<br>
        - <strong>Max Width (in pixels):</strong> Define a maximum width in pixels for tab buttons to ensure they can grow according to content but do not exceed the specified size, maintaining a clean and organized appearance.'),
      '#step' => 1,
      '#min' => 0,
      '#required' => TRUE,
    ];

    $form['layout_section']['max_height'] = [
      '#type' => 'number',
      '#title' => $this->t('Max Height (px) for Vertical Tabs Buttons Container (Left/Right)'),
      '#default_value' => $this->options['max_height'],
      '#description' => $this->t('Defines the maximum height for the buttons container in a vertical layout (Left/Right). Enter zero for auto. This setting does not affect the pane height.'),
      '#step' => 1,
      '#min' => 0,
      '#required' => TRUE,
    ];
  }

  /**
   * Build deep linking configuration section.
   *
   * @param array $form
   *   The form array (passed by reference).
   */
  protected function buildDeepLinkingSection(array &$form): void {
    $form['deeplink_section'] = [
      '#type' => 'details',
      '#title' => $this->t('Deep Linking Settings'),
      '#open' => TRUE,
      '#weight' => -35,
      '#attributes' => [
        'data-vvjt-deeplink-section' => 'true',
      ],
    ];

    $form['deeplink_section']['enable_deeplink'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable Deep Linking'),
      '#description' => $this->t('Enable deep linking to create shareable URLs for specific tabs. When enabled, tab buttons become clickable anchor links.'),
      '#default_value' => $this->options['enable_deeplink'],
      '#attributes' => [
        'data-vvjt-deeplink-toggle' => 'true',
      ],
    ];

    $form['deeplink_section']['deeplink_identifier'] = [
      '#type' => 'textfield',
      '#title' => $this->t('URL Identifier'),
      '#description' => $this->t('Short identifier used in tab links. Example: "products" creates links like #tabs-products-1. Will be automatically cleaned: converted to lowercase, spaces become hyphens, special characters removed.'),
      '#default_value' => $this->options['deeplink_identifier'],
      '#maxlength' => VvjtConstants::DEEPLINK_IDENTIFIER_MAX_LENGTH,
      '#size' => 20,
      '#placeholder' => 'products',
      '#wrapper_attributes' => [
        'class' => ['deeplink-identifier-wrapper'],
        'data-vvjt-deeplink-field' => 'true',
      ],
      '#element_validate' => [[$this, 'validateDeeplinkIdentifier']],
    ];
  }

  /**
   * Validates and sanitizes the deep link identifier field.
   *
   * @param array $element
   *   The form element.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   */
  public function validateDeeplinkIdentifier(array $element, FormStateInterface $form_state): void {
    // Get deep link values.
    $deeplink_values = $form_state->getValue(['style_options', 'deeplink_section']) ?? [];
    $enable_deeplink = !empty($deeplink_values['enable_deeplink']);
    $identifier = (string) ($deeplink_values['deeplink_identifier'] ?? '');

    // 1. If deep linking is disabled, clear identifier and exit early.
    if (!$enable_deeplink) {
      $form_state->setValue(['style_options', 'deeplink_section', 'deeplink_identifier'], '');
      return;
    }

    // 2. Deep linking is enabled, so enforce identifier rules.

    // Required when deep linking is enabled.
    if ($identifier === '') {
      $form_state->setError($element, $this->t('URL Identifier is required when Deep Linking is enabled.'));
      return;
    }

    // Transliterate and clean similar to URL aliases.
    $transliteration = \Drupal::transliteration();
    $clean = $transliteration->transliterate($identifier, 'en');

    // Convert to lowercase.
    $clean = strtolower($clean);

    // Replace spaces and underscores with hyphens.
    $clean = preg_replace('/[\s_]+/', '-', $clean);

    // Remove all characters except letters, numbers, and hyphens.
    $clean = preg_replace('/[^a-z0-9-]/', '', $clean);

    // Remove consecutive hyphens.
    $clean = preg_replace('/-+/', '-', $clean);

    // Remove leading/trailing hyphens.
    $clean = trim($clean, '-');

    // Ensure it starts with a letter.
    $clean = preg_replace('/^[0-9-]+/', '', $clean);

    // If empty after cleaning, show error.
    if ($clean === '') {
      $form_state->setError($element, $this->t('URL Identifier must contain at least one letter.'));
      return;
    }

    // Check reserved words.
    if (in_array($clean, VvjtConstants::DEEPLINK_RESERVED_WORDS, TRUE)) {
      $form_state->setError(
        $element,
        $this->t('Please choose a more specific identifier. "@identifier" is a reserved word.', ['@identifier' => $clean])
      );
      return;
    }

    // Set the cleaned value back to form state.
    $form_state->setValue(['style_options', 'deeplink_section', 'deeplink_identifier'], $clean);
  }

  /**
   * Build animation configuration section.
   *
   * @param array $form
   *   The form array (passed by reference).
   */
  protected function buildAnimationSection(array &$form): void {
    $form['animation_section'] = [
      '#type' => 'details',
      '#title' => $this->t('Animation Settings'),
      '#open' => TRUE,
      '#weight' => -30,
    ];

    $form['animation_section']['animation'] = [
      '#type' => 'select',
      '#title' => $this->t('Animation Type'),
      '#options' => [
        'none' => $this->t('None'),
        'a-top' => $this->t('Slide from Top'),
        'a-bottom' => $this->t('Slide from Bottom'),
        'a-left' => $this->t('Slide from Left'),
        'a-right' => $this->t('Slide from Right'),
        'a-zoom' => $this->t('Zoom'),
        'a-opacity' => $this->t('Opacity'),
      ],
      '#default_value' => $this->options['animation'],
      '#description' => $this->t('Choose the animation type for the tab pane.'),
    ];
  }

  /**
   * Build styling configuration section.
   *
   * @param array $form
   *   The form array (passed by reference).
   */
  protected function buildStylingSection(array &$form): void {
    $form['styling_section'] = [
      '#type' => 'details',
      '#title' => $this->t('Styling Settings'),
      '#open' => TRUE,
      '#weight' => -20,
    ];

    $form['styling_section']['background_buttons'] = [
      '#type' => 'color',
      '#title' => $this->t('Tabs Buttons Background Color'),
      '#default_value' => $this->options['background_buttons'],
      '#description' => $this->t('Select the background color to be applied to the tabs buttons. This color will determine the appearance of the buttons in the tab interface.'),
      '#empty_value' => '',
      '#states' => [
        'disabled' => [
          ':input[name="style_options[styling_section][disable_background]"]' => ['checked' => TRUE],
        ],
      ],
    ];

    $form['styling_section']['background_panes'] = [
      '#type' => 'color',
      '#title' => $this->t('Tabs Panes Background Color'),
      '#default_value' => $this->options['background_panes'],
      '#description' => $this->t('Select the background color to be applied to the content panes within the tabs. This color will be used as the background for the content displayed in each pane.'),
      '#empty_value' => '',
      '#states' => [
        'disabled' => [
          ':input[name="style_options[styling_section][disable_background]"]' => ['checked' => TRUE],
        ],
      ],
    ];

    $form['styling_section']['disable_background'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Disable Background Colors for Buttons and Panes'),
      '#default_value' => $this->options['disable_background'],
      '#description' => $this->t('Check this box to completely disable the background colors for both the tabs buttons and the content panes.'),
    ];
  }

  /**
   * Build responsive configuration section.
   *
   * @param array $form
   *   The form array (passed by reference).
   */
  protected function buildResponsiveSection(array &$form): void {
    $form['responsive_section'] = [
      '#type' => 'details',
      '#title' => $this->t('Responsive Settings'),
      '#open' => FALSE,
      '#weight' => -15,
    ];

    $form['responsive_section']['available_breakpoints'] = [
      '#type' => 'select',
      '#title' => $this->t('Responsive Breakpoint for Vertical Tabs'),
      '#options' => [
        '576' => $this->t('576 px'),
        '768' => $this->t('768 px'),
        '992' => $this->t('992 px'),
        '1200' => $this->t('1200 px'),
        '1400' => $this->t('1400 px'),
      ],
      '#default_value' => $this->options['available_breakpoints'],
      '#description' => $this->t('Choose the breakpoint at which the vertical tabs (Left/Right) will collapse and become horizontal.'),
    ];
  }

  /**
   * Build advanced options section.
   *
   * @param array $form
   *   The form array (passed by reference).
   */
  protected function buildAdvancedOptionsSection(array &$form): void {
    $form['advanced_section'] = [
      '#type' => 'details',
      '#title' => $this->t('Advanced Options'),
      '#open' => FALSE,
      '#weight' => -10,
    ];

    $form['advanced_section']['enable_css'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable CSS Library'),
      '#default_value' => $this->options['enable_css'],
      '#description' => $this->t('Check this box to include the CSS library for styling the tabs.'),
    ];
  }

  /**
   * Build token documentation section.
   *
   * @param array $form
   *   The form array (passed by reference).
   */
  protected function buildTokenDocumentation(array &$form): void {
    $form['token_section'] = [
      '#type' => 'details',
      '#title' => $this->t('Token Documentation'),
      '#open' => FALSE,
      '#weight' => 100,
    ];

    $form['token_section']['description'] = [
      '#markup' => $this->t('<p>When using <em>Global: Text area</em> or <em>Global: Unfiltered text</em> in the Views header, footer, or empty text areas, the default Twig-style tokens (e.g., <code>{{ title }}</code>) will not work with the VVJT style plugin.</p>
        <p>Instead, use the custom VVJT token format to access field values from the <strong>first row</strong> of the View result:</p>
        <ul>
          <li><code>[vvjt:field_name]</code> — The rendered output of the field (e.g., linked title, image, formatted text).</li>
          <li><code>[vvjt:field_name:plain]</code> — A plain-text version of the field, with all HTML stripped.</li>
        </ul>
        <p>Examples:</p>
        <ul>
          <li><code>{{ title }}</code> → <code>[vvjt:title]</code></li>
          <li><code>{{ field_image }}</code> → <code>[vvjt:field_image]</code></li>
          <li><code>{{ body }}</code> → <code>[vvjt:body:plain]</code></li>
        </ul>
        <p>These tokens offer safe and flexible field output for dynamic headings, summaries, and fallback messages in VVJT-enabled Views.</p>'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function submitOptionsForm(&$form, FormStateInterface $form_state): void {
    $values = $form_state->getValue('style_options');
    $flattened_values = $this->flattenFormValues($values);
    $form_state->setValue('style_options', $flattened_values);

    parent::submitOptionsForm($form, $form_state);
  }

  /**
   * Flatten nested form values to match original structure.
   *
   * @param array $values
   *   Nested form values.
   *
   * @return array
   *   Flattened values array.
   */
  protected function flattenFormValues(array $values): array {
    $flattened = [];

    if (isset($values['layout_section'])) {
      $flattened['tabs_position'] = $values['layout_section']['tabs_position'] ?? 'top';
      $flattened['wrap_tabs'] = $values['layout_section']['wrap_tabs'] ?? FALSE;
      $flattened['max_width'] = $values['layout_section']['max_width'] ?? 300;
      $flattened['max_height'] = $values['layout_section']['max_height'] ?? 0;
    }

    if (isset($values['deeplink_section'])) {
      $flattened['enable_deeplink'] = $values['deeplink_section']['enable_deeplink'] ?? FALSE;
      $flattened['deeplink_identifier'] = $values['deeplink_section']['deeplink_identifier'] ?? '';
    }

    if (isset($values['animation_section'])) {
      $flattened['animation'] = $values['animation_section']['animation'] ?? 'a-bottom';
    }

    if (isset($values['styling_section'])) {
      $flattened['background_buttons'] = $values['styling_section']['background_buttons'] ?? '#ECECEC';
      $flattened['background_panes'] = $values['styling_section']['background_panes'] ?? '#F7F7F7';
      $flattened['disable_background'] = $values['styling_section']['disable_background'] ?? FALSE;
    }

    if (isset($values['responsive_section'])) {
      $flattened['available_breakpoints'] = $values['responsive_section']['available_breakpoints'] ?? '992';
    }

    if (isset($values['advanced_section'])) {
      $flattened['enable_css'] = $values['advanced_section']['enable_css'] ?? TRUE;
    }

    $flattened['unique_id'] = $this->options['unique_id'] ?? $this->generateUniqueId();

    return $flattened;
  }

  /**
   * Attach form assets.
   *
   * @param array $form
   *   The form array (passed by reference).
   */
  protected function attachFormAssets(array &$form): void {
    $form['#attached']['library'][] = 'core/drupal.ajax';
    $form['#attached']['library'][] = 'vvjt/vvjt-admin';
  }

  /**
   * Generates a unique numeric ID for the view display.
   *
   * @return int
   *   A unique ID between 10000000 and 99999999.
   *
   * @throws \Exception
   *   If an appropriate source of randomness cannot be found.
   */
  protected function generateUniqueId(): int {
    if ($this->cachedUniqueId !== NULL) {
      return $this->cachedUniqueId;
    }

    $this->cachedUniqueId = random_int(10000000, 99999999);

    if ($this->cachedUniqueId < 10000000) {
      $this->cachedUniqueId += 10000000;
    }
    if ($this->cachedUniqueId > 99999999) {
      $this->cachedUniqueId = $this->cachedUniqueId % 90000000 + 10000000;
    }

    return $this->cachedUniqueId;
  }

  /**
   * {@inheritdoc}
   */
  public function render(): array {
    $rows = [];

    if (!empty($this->view->result)) {
      foreach ($this->view->result as $row) {
        $rendered_row = $this->view->rowPlugin->render($row);
        if ($rendered_row !== NULL) {
          $rows[] = $rendered_row;
        }
      }
    }

    $libraries = $this->buildLibraryList();

    $build = [
      '#theme' => $this->themeFunctions(),
      '#view' => $this->view,
      '#options' => $this->options,
      '#rows' => $rows,
      '#unique_id' => $this->options['unique_id'] ?? $this->generateUniqueId(),
      '#attached' => [
        'library' => $libraries,
      ],
    ];

    return $build;
  }

  /**
   * Build the list of libraries to attach.
   *
   * @return array
   *   An array of library names to attach.
   */
  protected function buildLibraryList(): array {
    $libraries = ['vvjt/vvjt'];

    if (!empty($this->options['enable_css'])) {
      $libraries[] = 'vvjt/vvjt-style';
    }

    if ($this->options['tabs_position'] === 'left' || $this->options['tabs_position'] === 'right') {
      $libraries[] = 'vvjt/vvjt-vertical';
      $libraries[] = 'vvjt/vvjt__' . ($this->options['available_breakpoints'] ?? '992');
    }
    else {
      $libraries[] = 'vvjt/vvjt-horizontal';
    }

    return $libraries;
  }

  /**
   * {@inheritdoc}
   */
  public function validate(): array {
    $errors = parent::validate();

    if (!$this->usesFields()) {
      $errors[] = $this->t('Views Tabs requires Fields as row style');
    }

    return $errors;
  }

}
