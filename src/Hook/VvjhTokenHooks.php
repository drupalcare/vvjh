<?php

declare(strict_types=1);

namespace Drupal\vvjh\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\vvj_core\Service\TokenResolver;
use Drupal\vvjh\Plugin\views\style\Hero;

/**
 * Token hooks — exposes per-row field tokens for VVJH-enabled Views.
 *
 * Token forms:
 *   [vvjh:FIELD]        — rendered HTML
 *   [vvjh:FIELD:plain]  — plain text.
 */
final class VvjhTokenHooks {

  use StringTranslationTrait;

  /**
   * Constructor.
   *
   * @param \Drupal\vvj_core\Service\TokenResolver|null $tokenResolver
   *   Shared token resolver. Nullable to keep the container compilable
   *   during the v1 → v2 upgrade window when vvj_core is on disk but
   *   not yet enabled. vvjh_update_10001 installs it; subsequent
   *   container rebuilds inject the real service.
   */
  public function __construct(
    private readonly ?TokenResolver $tokenResolver,
  ) {}

  /**
   * Implements hook_token_info().
   *
   * @return array<string, array<string, array<string, mixed>>>
   *   Token type + token definitions for the `vvjh` namespace.
   */
  #[Hook('token_info')]
  public function tokenInfo(): array {
    return [
      'types' => [
        'vvjh' => [
          'name' => $this->t('VVJH Hero'),
          'description' => $this->t('Tokens for accessing field values from the first row of a VVJH-enabled View.'),
          'needs-data' => 'view',
        ],
      ],
      'tokens' => [
        'vvjh' => [
          '*' => [
            'name' => $this->t('Field token'),
            'description' => $this->t('Use [vvjh:FIELD_NAME] for HTML output or [vvjh:FIELD_NAME:plain] for plain text.'),
          ],
        ],
      ],
    ];
  }

  /**
   * Implements hook_tokens().
   *
   * @param string $type
   *   Token type — only `vvjh` is handled.
   * @param array<string, string> $tokens
   *   Token name → original placeholder map.
   * @param array<string, mixed> $data
   *   Token data; expects `view` key with a ViewExecutable.
   * @param array<string, mixed> $options
   *   Token resolution options.
   * @param \Drupal\Core\Render\BubbleableMetadata $bubbleable_metadata
   *   For propagating cache metadata.
   *
   * @return array<string, string|\Drupal\Core\Render\Markup>
   *   Map of original placeholder → resolved value.
   */
  #[Hook('tokens')]
  public function tokens(
    string $type,
    array $tokens,
    array $data,
    array $options,
    BubbleableMetadata $bubbleable_metadata,
  ): array {
    if ($type !== 'vvjh' || $this->tokenResolver === NULL) {
      return [];
    }
    return $this->tokenResolver->resolve(
      'vvjh',
      $tokens,
      $data,
      $bubbleable_metadata,
      Hero::class,
    );
  }

}
