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
  protected function defineOptions() {
    $options = parent::defineOptions();
    $options['animation'] = ['default' => 'vvjt__animate_bottom'];
    $options['unique_id'] = ['default' => $this->generateUniqueId()];
    $options['vertical_tabs'] = ['default' => FALSE];
    $options['max_width'] = ['default' => 'left_width_300'];
    $options['available_breakpoints'] = ['default' => 'vvjt__992'];
    // New option for enabling CSS.
    $options['enable_css'] = ['default' => TRUE];
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);

    // Add enable_css checkbox to the form.
    $form['enable_css'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable CSS Library'),
      '#default_value' => $this->options['enable_css'],
      '#description' => $this->t('Check this box to include the CSS library for styling the tabs.'),
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
      '#description' => $this->t('Choose the animation type.'),
    ];

    // Add the vertical_tabs checkbox.
    $form['vertical_tabs'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable Vertical Tabs'),
      '#description' => $this->t('Check this box to enable vertical tabs and configure additional settings.'),
      '#default_value' => $this->options['vertical_tabs'],
    ];

    // Add a message above the dependent fields.
    $form['dependent_fields_message'] = [
      '#markup' => '<strong>' . $this->t('The following fields are for Vertical Tabs:') . '</strong>',
    ];

    $form['max_width'] = [
      '#type' => 'select',
      '#title' => $this->t('Max Width'),
      '#options' => [
        'left_width_300' => $this->t('300 px'),
        'left_width_400' => $this->t('400 px'),
        'left_width_500' => $this->t('500 px'),
        'left_width_600' => $this->t('600 px'),
      ],
      '#default_value' => $this->options['max_width'],
      '#description' => $this->t('Choose the maximum width for the left buttons when using vertical tabs.'),
    ];

    $form['available_breakpoints'] = [
      '#type' => 'select',
      '#title' => $this->t('Available Breakpoints'),
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
   */
  protected function generateUniqueId() {
    // 8 digit unique ID
    return random_int(10000000, 99999999);
  }

  /**
   * Renders the view with the 3D carousel style.
   *
   * @return array
   *   A render array for the 3D carousel.
   */
  public function render() {
    $rows = [];
    foreach ($this->view->result as $row) {
      $rows[] = $this->view->rowPlugin->render($row);
    }

    $libraries = [
      'vvjt/vvjt',
    ];

    // Conditionally include the CSS library based on the option.
    if ($this->options['enable_css']) {
      // Updated to attach the vvjs-style library.
      $libraries[] = 'vvjt/vvjt-style';
    }

    // Conditionally include the vertical tabs style.
    if ($this->options['vertical_tabs']) {
      // Corrected library path for breakpoints.
      $libraries[] = 'vvjt/' . $this->options['available_breakpoints'];
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
