<?php

namespace Drupal\vvjt\Plugin\views\style;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\style\StylePluginBase;

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
   * Set default options.
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
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state): void {
    parent::buildOptionsForm($form, $form_state);

    // Add enable_css checkbox to the form.
    $form['enable_css'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable CSS Library'),
      '#default_value' => $this->options['enable_css'],
      '#description' => $this->t('Check this box to include the CSS library for styling the tabs.'),
    ];

    // Add the wrap_tabs checkbox to the form.
    $form['wrap_tabs'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Wrap Tabs When Exceeded'),
      '#default_value' => $this->options['wrap_tabs'],
      '#description' => $this->t('Check this box to wrap tab buttons if they exceed the available space on any screen size. The default is auto-scroll.'),
    ];

    // Add the tabs position select field.
    $form['tabs_position'] = [
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

    $form['animation'] = [
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

    $form['max_width'] = [
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

    $form['max_height'] = [
      '#type' => 'number',
      '#title' => $this->t('Max Height (px) for Vertical Tabs Buttons Container (Left/Right)'),
      '#default_value' => $this->options['max_height'],
      '#description' => $this->t('Defines the maximum height for the buttons container in a vertical layout (Left/Right). Enter zero for auto. This setting does not affect the pane height.'),
      '#step' => 1,
      '#min' => 0,
      '#required' => TRUE,
    ];

    $form['available_breakpoints'] = [
      '#type' => 'select',
      '#title' => $this->t('Available Breakpoints for Vertical Tabs (Left/Right)'),
      '#options' => [
        '576' => $this->t('576px / 36rem'),
        '768' => $this->t('768px / 48rem'),
        '992' => $this->t('992px / 62rem'),
        '1200' => $this->t('1200px / 75rem'),
        '1400' => $this->t('1400px / 87.5rem'),
      ],
      '#default_value' => $this->options['available_breakpoints'],
      '#description' => $this->t('Choose the breakpoint at which the vertical tabs will collapse and become horizontal.'),
    ];

    // Add the background color field for buttons.
    $form['background_buttons'] = [
      '#type' => 'color',
      '#title' => $this->t('Tabs Buttons Background Color'),
      '#default_value' => $this->options['background_buttons'],
      '#description' => $this->t('Select the background color to be applied to the tabs buttons. This color will determine the appearance of the buttons in the tab interface.'),
      // Allow for empty value.
      '#empty_value' => '',
      '#states' => [
        'disabled' => [
          ':input[name="style_options[disable_background]"]' => ['checked' => TRUE],
        ],
      ],
    ];

    // Add the background color field for panes.
    $form['background_panes'] = [
      '#type' => 'color',
      '#title' => $this->t('Tabs Panes Background Color'),
      '#default_value' => $this->options['background_panes'],
      '#description' => $this->t('Select the background color to be applied to the content panes within the tabs. This color will be used as the background for the content displayed in each pane.'),
      // Allow for empty value.
      '#empty_value' => '',
      '#states' => [
        'disabled' => [
          ':input[name="style_options[disable_background]"]' => ['checked' => TRUE],
        ],
      ],
    ];

    // Add the disable background checkbox.
    $form['disable_background'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Disable Background Colors for Buttons and Panes'),
      '#default_value' => $this->options['disable_background'],
      '#description' => $this->t('Check this box to completely disable the background colors for both the tabs buttons and the content panes.'),
    ];

    $form['vvjt_token_info'] = [
      '#type' => 'details',
      '#title' => $this->t('VVJT Tokens'),
      '#open' => TRUE,
    ];

    $form['vvjt_token_info']['description'] = [
      '#markup' => $this->t('<p>When using <em>Global: Text area</em> or <em>Global: Unfiltered text</em> in the Views header, footer, or empty text areas, the default Twig-style tokens (e.g., <code>{{ title }}</code>) will not work with the VVJT style plugin.</p>
        <p>Instead, use the custom VVJT token format to access field values from the <strong>first row</strong> of the View result:</p>
        <ul>
          <li><code>[vvjt:field_name]</code> – The rendered output of the field (e.g., linked title, image, formatted text).</li>
          <li><code>[vvjt:field_name:plain]</code> – A plain-text version of the field, with all HTML stripped.</li>
        </ul>
        <p>Examples:</p>
        <ul>
          <li><code>{{ title }}</code> ➜ <code>[vvjt:title]</code></li>
          <li><code>{{ field_image }}</code> ➜ <code>[vvjt:field_image]</code></li>
          <li><code>{{ body }}</code> ➜ <code>[vvjt:body:plain]</code></li>
        </ul>
        <p>These tokens offer safe and flexible field output for dynamic headings, summaries, and fallback messages in VVJT-enabled Views.</p>'),
    ];

  }

  /**
   * Generates a unique numeric ID for the view display.
   *
   * @throws \Random\RandomException
   */
  protected function generateUniqueId(): int {
    // 8 digit unique ID
    return random_int(10000000, 99999999);
  }

  /**
   * {@inheritdoc}
   */
  public function preRender($result) {
    parent::preRender($result);

    if (empty($result)) {
      return;
    }

    $this->view->row_index = 0;
    $this->view->style_plugin = $this;

    $first_row = $result[0];
    $replacements = [];

    // Generate [vvjt:field_name] tokens for all fields.
    foreach ($this->view->field as $field_id => $field_handler) {
      $replacements["[vvjt:$field_id]"] = $field_handler->render($first_row);
    }

    // Replace in header/footer/empty regions.
    $sections = ['header', 'footer', 'empty'];

    foreach ($sections as $section) {
      $options = $this->view->display_handler->getOption($section);
      if (!empty($options) && is_array($options)) {
        foreach ($options as &$item) {
          if (isset($item['content']) && is_string($item['content'])) {
            $item['content'] = strtr($item['content'], $replacements);
          }
        }
        $this->view->display_handler->setOption($section, $options);
      }
    }
  }

  /**
   * Renders the view with the 3D carousel style.
   *
   * @return array
   *   A render array for the 3D carousel.
   */
  public function render(): array {
    $rows = [];
    foreach ($this->view->result as $row) {
      $rows[] = $this->view->rowPlugin->render($row);
    }

    $libraries = [
      'vvjt/vvjt',
    ];

    // Conditionally include the CSS library based on the option.
    if ($this->options['enable_css']) {
      // Include the vvjs-style library.
      $libraries[] = 'vvjt/vvjt-style';
    }

    // Conditionally include the vertical tabs style.
    if ($this->options['tabs_position'] === 'left' || $this->options['tabs_position'] === 'right') {
      // Include the appropriate library for breakpoints.
      $libraries[] = 'vvjt/vvjt-vertical';
      $libraries[] = 'vvjt/vvjt__' . $this->options['available_breakpoints'];
    }
    else {
      $libraries[] = 'vvjt/vvjt-horizontal';
    }

    $build = [
      '#theme' => $this->themeFunctions(),
      '#view' => $this->view,
      '#options' => $this->options,
      '#rows' => $rows,
      '#unique_id' => $this->options['unique_id'],
      '#attached' => [
        'library' => $libraries,
      ],
    ];

    $build['#context']['tokens']['view'] = $this->view;
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function validate() {
    $errors = parent::validate();
    if (!$this->usesFields()) {
      $errors[] = $this->t('Views Tabs requires Fields as row style');
    }
    return $errors;
  }

}
