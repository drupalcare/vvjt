<?php

declare(strict_types=1);

namespace Drupal\Tests\vvjt\Kernel;

use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\KernelTests\KernelTestBase;
use Drupal\node\Entity\NodeType;
use Drupal\views\Views;
use Drupal\vvjt\Plugin\views\style\Tabs;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

/**
 * Verifies the views_vvjt style plugin loads and provides expected defaults.
 */
#[Group('vvjt')]
#[RunTestsInSeparateProcesses]
final class VvjtStylePluginTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   *
   * @var list<string>
   */
  protected static $modules = [
    'system',
    'user',
    'field',
    'filter',
    'text',
    'node',
    'views',
    'vvj_core',
    'vvjt',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installEntitySchema('user');
    $this->installEntitySchema('node');
    $this->installSchema('node', ['node_access']);
    $this->installConfig(['filter', 'node']);

    // Build the content model the optional example view depends on so that
    // views.view.vvjt_example installs with vvjt config.
    NodeType::create(['type' => 'article', 'name' => 'Article'])->save();
    FieldStorageConfig::create([
      'field_name' => 'body',
      'entity_type' => 'node',
      'type' => 'text_with_summary',
    ])->save();
    FieldConfig::create([
      'field_name' => 'body',
      'entity_type' => 'node',
      'bundle' => 'article',
      'label' => 'Body',
    ])->save();

    $this->installConfig(['vvjt']);
  }

  /**
   * The style plugin is registered with the expected class and theme.
   */
  public function testStylePluginIsRegistered(): void {
    /** @var \Drupal\views\Plugin\ViewsPluginManager $manager */
    $manager = $this->container->get('plugin.manager.views.style');
    $definition = $manager->getDefinition('views_vvjt');
    $this->assertIsArray($definition);

    $this->assertSame(Tabs::class, $definition['class']);
    $this->assertSame('views_view_vvjt', $definition['theme']);
  }

  /**
   * The example view loads and initializes the style plugin.
   */
  public function testExampleViewLoadsWithStyle(): void {
    $view = Views::getView('vvjt_example');
    $this->assertNotNull($view, 'vvjt_example view ships as optional config.');

    $view->setDisplay('default');
    $view->initStyle();

    $this->assertInstanceOf(Tabs::class, $view->style_plugin);
    $this->assertSame('views_vvjt', $view->style_plugin->getPluginId());
  }

  /**
   * The style plugin defines the expected option defaults.
   */
  public function testStyleDefinesExpectedOptionDefaults(): void {
    $view = Views::getView('vvjt_example');
    $this->assertNotNull($view);
    $view->setDisplay('default');
    $view->initStyle();

    $options = $view->style_plugin->options;

    $this->assertArrayHasKey('animation', $options);
    $this->assertArrayHasKey('tabs_position', $options);
    $this->assertArrayHasKey('max_width', $options);
    $this->assertArrayHasKey('unique_id', $options);
    $this->assertArrayHasKey('enable_deeplink', $options);
    $this->assertArrayHasKey('deeplink_identifier', $options);
  }

  /**
   * Rendering produces cache metadata and attaches the pattern library.
   */
  public function testRenderProducesCacheableOutput(): void {
    $view = Views::getView('vvjt_example');
    $this->assertNotNull($view);
    $view->setDisplay('default');
    $view->execute();

    $build = $view->style_plugin->render();
    $this->assertIsArray($build);

    $this->assertArrayHasKey('#cache', $build);
    $cache = $build['#cache'];
    $this->assertIsArray($cache);
    $this->assertArrayHasKey('tags', $cache);
    $this->assertArrayHasKey('contexts', $cache);
    $this->assertArrayHasKey('max-age', $cache);

    $this->assertArrayHasKey('#attached', $build);
    $attached = $build['#attached'];
    $this->assertIsArray($attached);
    $this->assertArrayHasKey('library', $attached);
    $library = $attached['library'];
    $this->assertIsArray($library);
    $this->assertContains('vvjt/vvjt', $library);
  }

}
