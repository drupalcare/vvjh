/**
 * @file
 * Shared by the vvjh Pattern views: marks the element once it has hydrated
 * (`data-vvjh-ready`), so a Pattern can start its photo hidden only when
 * JavaScript is there to reveal it. Without JavaScript nothing is hidden.
 *
 * @license SPDX-License-Identifier: GPL-2.0-or-later
 */

((Drupal, once) => {
  const controllers = new WeakMap();

  Drupal.behaviors.vvjPatternVvjh = {
    attach(context) {
      once('vvj-pattern-vvjh', '.vvj-pattern.vvjh', context).forEach((root) => {
        const controller = new AbortController();
        controllers.set(root, controller);
        Drupal.Vvj.whenReady(
          root,
          () => root.setAttribute('data-vvjh-ready', ''),
          controller.signal,
        );
      });
    },
    detach(context, settings, trigger) {
      if (trigger !== 'unload') {
        return;
      }
      once
        .remove('vvj-pattern-vvjh', '.vvj-pattern.vvjh', context)
        .forEach((root) => {
          controllers.get(root)?.abort();
          controllers.delete(root);
        });
    },
  };
})(Drupal, once);
