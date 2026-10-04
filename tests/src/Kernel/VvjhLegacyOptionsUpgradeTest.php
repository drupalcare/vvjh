<?php

declare(strict_types=1);

namespace Drupal\Tests\vvjh\Kernel;

use Drupal\KernelTests\KernelTestBase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

/**
 * A view saved by v1 is converted to the v2 option shape by vvjh_update_10002.
 *
 * The fixture is written straight to config storage: no schema casting and
 * no save event, so it holds exactly what a v1 database holds. Strict schema
 * checking stays on and validates the CONVERTED view when the update saves
 * it. Measured on a real v1 database (solo.local) 2026-09-26.
 */
#[Group('vvjh')]
#[RunTestsInSeparateProcesses]
final class VvjhLegacyOptionsUpgradeTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   *
   * @var list<string>
   */
  protected static $modules = ['system', 'user', 'filter', 'views', 'vvj_core', 'vvjh'];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    \Drupal::moduleHandler()->loadInclude('vvjh', 'install');
  }

  /**
   * A 1.0 bare-number min_height becomes {value, unit: vw}; types cast.
   */
  public function testBareMinHeightBecomesMappingAndTypesAreCast(): void {
    $this->saveLegacyView('legacy', [
      'min_height' => '40',
      'max_width' => '1200',
      'enable_css' => 1,
    ]);
    // CONTROL: the fixture really is drifted, or the checks below prove
    // nothing.
    $this->assertNotSame([], $this->driftOf('legacy'));

    $this->assertStringContainsString('Converted 1', vvjh_update_10002());

    $options = $this->optionsOf('legacy');
    $this->assertSame(['value' => 40, 'unit' => 'vw'], $options['min_height']);
    $this->assertSame(1200, $options['max_width']);
    $this->assertTrue($options['enable_css']);
    $this->assertSame([], $this->driftOf('legacy'));
    $this->assertStringContainsString('already', vvjh_update_10002());
  }

  /**
   * A min_height already in the {value, unit} shape is left as it is.
   */
  public function testMappingMinHeightIsUntouched(): void {
    $this->saveLegacyView('current', ['min_height' => ['value' => 60, 'unit' => 'vh']]);
    vvjh_update_10002();
    $this->assertSame(['value' => 60, 'unit' => 'vh'], $this->optionsOf('current')['min_height']);
  }

  /**
   * Stores a view the way v1 left it: no schema casting.
   *
   * @param string $id
   *   View ID.
   * @param array<string, mixed> $options
   *   Style options as v1 stored them.
   */
  private function saveLegacyView(string $id, array $options): void {
    // Straight to storage: no casting, exactly as v1 left it. (The
    // trusted-data save() is deprecated in 11.4, removed in 13.)
    $this->container->get('config.storage')->write('views.view.' . $id, [
      'langcode' => 'en',
      'status' => TRUE,
      'dependencies' => [],
      'id' => $id,
      'label' => $id,
      'module' => 'views',
      'description' => '',
      'tag' => '',
      'base_table' => 'users_field_data',
      'base_field' => 'uid',
      'display' => [
        'default' => [
          'id' => 'default',
          'display_title' => 'Default',
          'display_plugin' => 'default',
          'position' => 0,
          'display_options' => [
            'style' => ['type' => 'views_vvjh', 'options' => $options],
          ],
        ],
      ],
    ]);
    $this->container->get('config.factory')->reset('views.view.' . $id);
  }

  /**
   * The stored style options of a view, read fresh.
   *
   * @param string $id
   *   View ID.
   *
   * @return array<mixed>
   *   The options.
   */
  private function optionsOf(string $id): array {
    $this->container->get('config.factory')->reset('views.view.' . $id);
    $options = $this->config('views.view.' . $id)->get('display.default.display_options.style.options');
    $this->assertIsArray($options);
    return $options;
  }

  /**
   * Drift the auditor reports for a view.
   *
   * @param string $id
   *   View ID.
   *
   * @return list<string>
   *   Drift descriptions.
   */
  private function driftOf(string $id): array {
    /** @var \Drupal\vvj_core\Service\VvjUpgradeAuditor $auditor */
    $auditor = $this->container->get('vvj_core.upgrade_auditor');
    return $auditor->detectDrift($id, ['views_vvjh']);
  }

}
