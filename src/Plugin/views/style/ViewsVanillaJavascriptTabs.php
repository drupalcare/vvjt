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
    $options['animation'] = ['default' => 'vvjt__animate_bottom'];
    $options['unique_id'] = ['default' => $this->generateUniqueId()];
    $options['tabs_position'] = ['default' => 'top'];
    $options['max_width'] = ['default' => 'tabs_width_300'];
    $options['available_breakpoints'] = ['default' => 'vvjt__992'];
    $options['enable_css'] = ['default' => TRUE];
    // Add the wrap_tabs option.
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
        'vvjt__animate_top' => $this->t('Top'),
        'vvjt__animate_bottom' => $this->t('Bottom'),
        'vvjt__animate_left' => $this->t('Left'),
        'vvjt__animate_right' => $this->t('Right'),
        'vvjt__animate_zoom' => $this->t('Zoom'),
        'vvjt__animate_opacity' => $this->t('Opacity'),
      ],
      '#default_value' => $this->options['animation'],
      '#description' => $this->t('Choose the animation type for the tab pane.'),
    ];

    $form['max_width'] = [
      '#type' => 'select',
      '#title' => $this->t('Tabs Max Width'),
      '#options' => [
        'tabs_width_auto' => $this->t('Auto'),
        'tabs_width_100' => $this->t('100 px'),
        'tabs_width_200' => $this->t('200 px'),
        'tabs_width_300' => $this->t('300 px'),
        'tabs_width_400' => $this->t('400 px'),
        'tabs_width_500' => $this->t('500 px'),
        'tabs_width_600' => $this->t('600 px'),
      ],
      '#default_value' => $this->options['max_width'],
      '#description' => $this->t('
        Configure the maximum width of the tab buttons based on the selected tab position.<br><br>

        <strong>Vertical Tabs (Left/Right):</strong><br>
        - <strong>Auto:</strong> The tab buttons adjust their width dynamically based on content size and available space, ensuring flexibility across different screen sizes.<br>
        - <strong>Max Widths (100 px - 600 px):</strong> Set a maximum width to allow tab buttons to expand based on content while preventing them from exceeding a specified width. This is ideal for maintaining consistency without sacrificing adaptability.<br><br>

        <strong>Horizontal Tabs (Top/Bottom):</strong><br>
        - <strong>Auto:</strong> The tabs automatically resize to fit their content, providing a responsive layout that adapts to the available space.<br>
        - <strong>Max Widths (100 px - 600 px):</strong> Define a maximum width for tab buttons to ensure they can grow according to content but do not exceed the specified size, which helps in maintaining a clean and organized appearance.
      '),
    ];

    $form['available_breakpoints'] = [
      '#type' => 'select',
      '#title' => $this->t('Available Breakpoints for Vertical Tabs (Left/Right)'),
      '#options' => [
        'vvjt__576' => $this->t('576px / 36rem'),
        'vvjt__768' => $this->t('768px / 48rem'),
        'vvjt__992' => $this->t('992px / 62rem'),
        'vvjt__1200' => $this->t('1200px / 75rem'),
        'vvjt__1400' => $this->t('1400px / 87.5rem'),
      ],
      '#default_value' => $this->options['available_breakpoints'],
      '#description' => $this->t('Choose the breakpoint at which the vertical tabs will collapse and become horizontal.'),
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
      $libraries[] = 'vvjt/' . $this->options['available_breakpoints'];
    }
    else {
      $libraries[] = 'vvjt/vvjt-horizontal';
    }

    return [
      '#theme' => $this->themeFunctions(),
      '#view' => $this->view,
      '#options' => $this->options,
      '#rows' => $rows,
      '#unique_id' => $this->options['unique_id'],
      '#attached' => [
        'library' => $libraries,
      ],
    ];
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
