<?php

declare(strict_types=1);

namespace Drupal\Tests\vvjh\FunctionalJavascript;

use Drupal\Tests\vvj_core\FunctionalJavascript\VvjPatternJavascriptTestBase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

/**
 * Browser smoke test: the VVJH example view renders a `<vvjh-hero>` element.
 */
#[Group('vvjh')]
#[Group('vvj_javascript')]
#[RunTestsInSeparateProcesses]
final class VvjhJavascriptSmokeTest extends VvjPatternJavascriptTestBase {

  /**
   * {@inheritdoc}
   *
   * @var list<string>
   */
  protected static $modules = [
    'node',
    'block',
    'views',
    'field',
    'file',
    'filter',
    'text',
    'image',
    'vvj_core',
    'vvjh',
  ];

  /**
   * {@inheritdoc}
   */
  protected function vvjExampleViewId(): string {
    return 'vvjh_example';
  }

  /**
   * {@inheritdoc}
   */
  protected function vvjCustomElementTag(): string {
    return 'vvjh-hero';
  }

  /**
   * {@inheritdoc}
   */
  protected function vvjRequiresArticleImageField(): bool {
    return TRUE;
  }

}
