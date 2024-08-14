/**
 * @file
 * VVJ Hero.
 *
 * Filename:     vvjh.js
 * Website:      https://www.flashwebcenter.com
 * Developer:    Alaa Haddad https://www.alaahaddad.com.
 */
(function (Drupal, drupalSettings, once) {
  Drupal.behaviors.VVJHero = {
    attach: function (context, settings) {
      // Ensure this behavior is applied only once per .vvjh element
      const heroes = once('vvjHero', '.vvjh', context);

      function toggleHeroActiveClass() {
        const currentWidth = window.innerWidth;

        heroes.forEach(function(hero) {
          const breakpoint = hero.getAttribute('data-breakpoints');

          // Find all hero content elements within the .vvjh element
          const heroContents = hero.querySelectorAll('.hero-wrapper');

          // Check if the current width is greater than or equal to the breakpoint
          if (currentWidth >= breakpoint) {
            // Add the 'active' class to each hero content to trigger the animation
            heroContents.forEach(function(item) {
              item.classList.add('active');
            });
          } else {
            // Remove the 'active' class if the width is below the breakpoint
            heroContents.forEach(function(item) {
              item.classList.remove('active');
            });
          }
        });
      }

      // Initial check on load
      toggleHeroActiveClass();

      // Re-check on window resize
      window.addEventListener('resize', toggleHeroActiveClass);
    }
  };
})(Drupal, drupalSettings, once);

