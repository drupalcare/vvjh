<?php

declare(strict_types=1);

namespace Drupal\vvjh\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Help hook for vvjh.
 */
final class VvjhHelpHook {

  use StringTranslationTrait;

  /**
   * Implements hook_help() for the vvjh help route.
   */
  #[Hook('help')]
  public function help(string $route_name, RouteMatchInterface $route_match): ?string {
    if ($route_name !== 'help.page.vvjh') {
      return NULL;
    }
    $output = '<h2>' . $this->t('About') . '</h2>';
    $output .= '<p>' . $this->t('The Views Vanilla JavaScript Hero module displays the results of a view as a responsive hero. It is a Views format: it shows whatever the view lists and adds no content of its own. For more information, see the <a href=":project">online documentation for the Views Vanilla JavaScript Hero module</a>.', [
      ':project' => 'https://www.drupal.org/project/vvjh',
    ]) . '</p>';
    $output .= '<h2>' . $this->t('Uses') . '</h2>';
    $output .= '<dl>';
    $output .= '<dt>' . $this->t('Displaying a view as a hero') . '</dt>';
    $output .= '<dd>' . $this->t('In the Views administration pages (<em>Structure</em> > <em>Views</em>), edit a view and, under <em>Format</em>, choose <em>Views Vanilla JavaScript Hero</em>. Its settings cover layout, navigation, animation, accessibility and responsive behavior.') . '</dd>';
    $output .= '<dt>' . $this->t('Choosing a Pattern') . '</dt>';
    $output .= '<dd>' . $this->t('With the VVJ Patterns module (part of VVJ Core) enabled, the <em>Patterns</em> section of the format settings offers four ready-made designs: Panels, Aperture, Bento and Stack. Choosing one applies its layout, animation and styling settings to the view.') . '</dd>';
    $output .= '<dt>' . $this->t('Trying the example view') . '</dt>';
    $output .= '<dd>' . $this->t('On a site with the Article content type, the module installs an example view, <em>VVJH Example</em>, at the path /vvjh-example.') . '</dd>';
    $output .= '</dl>';
    return $output;
  }

}
