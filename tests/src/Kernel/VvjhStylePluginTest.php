<?php

declare(strict_types=1);

namespace Drupal\Tests\vvjh\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\vvj_core\Kernel\VvjArticleImageExampleKernelTrait;
use Drupal\views\Views;
use Drupal\vvjh\Plugin\views\style\Hero;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

/**
 * Verifies the views_vvjh style plugin loads + provides expected defaults.
 */
#[Group('vvjh')]
#[RunTestsInSeparateProcesses]
final class VvjhStylePluginTest extends KernelTestBase {

  use VvjArticleImageExampleKernelTrait;

  /**
   * {@inheritdoc}
   *
   * @var list<string>
   */
  protected static $modules = [
    'system',
    'user',
    'field',
    'file',
    'filter',
    'text',
    'image',
    'node',
    'views',
    'vvj_core',
    'vvjh',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->vvjKernelInstallArticleImageExampleEnvironment('vvjh');
  }

  /**
   * The views_vvjh style plugin registers with the expected class + theme.
   */
  public function testStylePluginIsRegistered(): void {
    /** @var \Drupal\views\Plugin\ViewsPluginManager $manager */
    $manager = $this->container->get('plugin.manager.views.style');
    $definition = $manager->getDefinition('views_vvjh');
    $this->assertIsArray($definition);

    $this->assertSame(Hero::class, $definition['class']);
    $this->assertSame('views_view_vvjh', $definition['theme']);
  }

  /**
   * The shipped example view loads with the Hero style plugin attached.
   */
  public function testExampleViewLoadsWithStyle(): void {
    $view = Views::getView('vvjh_example');
    $this->assertNotNull($view, 'vvjh_example view ships as optional config.');

    $view->setDisplay('default');
    $view->initStyle();

    $this->assertInstanceOf(Hero::class, $view->style_plugin);
    $this->assertSame('views_vvjh', $view->style_plugin->getPluginId());
  }

  /**
   * The style plugin defines the expected pattern option defaults.
   */
  public function testStyleDefinesExpectedOptionDefaults(): void {
    $view = Views::getView('vvjh_example');
    $this->assertNotNull($view);
    $view->setDisplay('default');
    $view->initStyle();

    $options = $view->style_plugin->options;

    $this->assertArrayHasKey('overlay_position', $options);
    $this->assertArrayHasKey('hero_style', $options);
    $this->assertArrayHasKey('hero_speed', $options);
    $this->assertArrayHasKey('unique_id', $options);
    $this->assertArrayHasKey('enable_css', $options);
  }

  /**
   * The rendered build exposes cache metadata and attaches the library.
   */
  public function testRenderProducesCacheableOutput(): void {
    $view = Views::getView('vvjh_example');
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
    $this->assertContains('vvjh/vvjh', $library);
  }

}
