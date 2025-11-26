/**
 * Custom Modern Interactions
 * JavaScript moderne pour des effets dynamiques et interactifs
 *
 * @author Custom Modernization
 * @version 1.0.0
 */

(function() {
  'use strict';

  /* ==========================================
     CONFIGURATION
     ========================================== */

  const CONFIG = {
    animationDuration: 300,
    scrollThreshold: 100,
    parallaxIntensity: 0.5,
    debounceDelay: 150,
    observerOptions: {
      threshold: 0.1,
      rootMargin: '0px 0px -100px 0px'
    }
  };

  /* ==========================================
     UTILITIES
     ========================================== */

  /**
   * Fonction de debounce pour optimiser les performances
   */
  function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
      const later = () => {
        clearTimeout(timeout);
        func(...args);
      };
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
    };
  }

  /**
   * Fonction de throttle pour limiter l'exécution
   */
  function throttle(func, limit) {
    let inThrottle;
    return function(...args) {
      if (!inThrottle) {
        func.apply(this, args);
        inThrottle = true;
        setTimeout(() => inThrottle = false, limit);
      }
    };
  }

  /**
   * Vérifier si un élément est visible dans le viewport
   */
  function isInViewport(element) {
    const rect = element.getBoundingClientRect();
    return (
      rect.top >= 0 &&
      rect.left >= 0 &&
      rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
      rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
  }

  /**
   * Ajouter une classe avec animation
   */
  function addClassWithAnimation(element, className, delay = 0) {
    setTimeout(() => {
      element.classList.add(className);
    }, delay);
  }

  /* ==========================================
     PAGE HEADER - Effets Parallaxe
     ========================================== */

  class PageHeaderEffects {
    constructor() {
      this.header = document.querySelector('.page-header');
      if (!this.header) return;

      this.init();
    }

    init() {
      this.addParallaxEffect();
      this.addMouseMoveEffect();
      this.addScrollReveal();
    }

    /**
     * Effet parallaxe au scroll
     */
    addParallaxEffect() {
      const handleScroll = throttle(() => {
        const scrolled = window.pageYOffset;
        const parallax = scrolled * CONFIG.parallaxIntensity;

        this.header.style.transform = `translateY(${parallax}px)`;
        this.header.style.opacity = 1 - (scrolled / 500);
      }, 10);

      window.addEventListener('scroll', handleScroll, { passive: true });
    }

    /**
     * Effet de mouvement au survol de la souris
     */
    addMouseMoveEffect() {
      this.header.addEventListener('mousemove', (e) => {
        const rect = this.header.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;

        const centerX = rect.width / 2;
        const centerY = rect.height / 2;

        const percentX = (x - centerX) / centerX;
        const percentY = (y - centerY) / centerY;

        this.header.style.setProperty('--mouse-x', `${percentX * 10}px`);
        this.header.style.setProperty('--mouse-y', `${percentY * 10}px`);
      });

      this.header.addEventListener('mouseleave', () => {
        this.header.style.setProperty('--mouse-x', '0px');
        this.header.style.setProperty('--mouse-y', '0px');
      });
    }

    /**
     * Animation de révélation au scroll
     */
    addScrollReveal() {
      this.header.style.opacity = '0';
      this.header.style.transform = 'translateY(-20px)';

      setTimeout(() => {
        this.header.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        this.header.style.opacity = '1';
        this.header.style.transform = 'translateY(0)';
      }, 100);
    }
  }

  /* ==========================================
     H6 - Effets Interactifs
     ========================================== */

  class HeadingEffects {
    constructor() {
      this.headings = document.querySelectorAll('h6');
      if (!this.headings.length) return;

      this.init();
    }

    init() {
      this.headings.forEach((heading, index) => {
        this.addAnimationOnScroll(heading, index);
        this.addCopyToClipboard(heading);
        this.addTypingEffect(heading);
      });
    }

    /**
     * Animation au scroll
     */
    addAnimationOnScroll(heading, index) {
      heading.style.opacity = '0';
      heading.style.transform = 'translateX(-20px)';

      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            setTimeout(() => {
              entry.target.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
              entry.target.style.opacity = '1';
              entry.target.style.transform = 'translateX(0)';
            }, index * 100);

            observer.unobserve(entry.target);
          }
        });
      }, CONFIG.observerOptions);

      observer.observe(heading);
    }

    /**
     * Copier le texte au clic
     */
    addCopyToClipboard(heading) {
      heading.style.cursor = 'pointer';
      heading.title = 'Cliquer pour copier';

      heading.addEventListener('click', () => {
        const text = heading.textContent;
        navigator.clipboard.writeText(text).then(() => {
          this.showCopiedFeedback(heading);
        });
      });
    }

    /**
     * Feedback visuel après copie
     */
    showCopiedFeedback(heading) {
      const originalText = heading.textContent;
      heading.textContent = '✓ Copié !';
      heading.style.color = '#4CAF50';

      setTimeout(() => {
        heading.textContent = originalText;
        heading.style.color = '';
      }, 1500);
    }

    /**
     * Effet de typing (optionnel, pour les titres spéciaux)
     */
    addTypingEffect(heading) {
      if (heading.classList.contains('typing-effect')) {
        const text = heading.textContent;
        heading.textContent = '';
        heading.style.opacity = '1';

        let index = 0;
        const typeChar = () => {
          if (index < text.length) {
            heading.textContent += text.charAt(index);
            index++;
            setTimeout(typeChar, 50);
          }
        };

        setTimeout(typeChar, 500);
      }
    }
  }

  /* ==========================================
     BLOG ENTRIES - Effets de Cards
     ========================================== */

  class BlogEntryEffects {
    constructor() {
      this.entries = document.querySelectorAll('.blog-entry-inner');
      if (!this.entries.length) return;

      this.init();
    }

    init() {
      this.entries.forEach((entry, index) => {
        this.addScrollAnimation(entry, index);
        this.add3DTiltEffect(entry);
        this.addRippleEffect(entry);
        this.addLazyLoadAnimation(entry);
      });
    }

    /**
     * Animation d'apparition au scroll
     */
    addScrollAnimation(entry, index) {
      entry.style.opacity = '0';
      entry.style.transform = 'translateY(30px) scale(0.95)';

      const observer = new IntersectionObserver((entries) => {
        entries.forEach(observedEntry => {
          if (observedEntry.isIntersecting) {
            setTimeout(() => {
              observedEntry.target.style.transition = 'all 0.6s cubic-bezier(0.34, 1.56, 0.64, 1)';
              observedEntry.target.style.opacity = '1';
              observedEntry.target.style.transform = 'translateY(0) scale(1)';
            }, index * 100);

            observer.unobserve(observedEntry.target);
          }
        });
      }, CONFIG.observerOptions);

      observer.observe(entry);
    }

    /**
     * Effet 3D tilt au survol
     */
    add3DTiltEffect(entry) {
      entry.addEventListener('mousemove', (e) => {
        const rect = entry.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;

        const centerX = rect.width / 2;
        const centerY = rect.height / 2;

        const rotateX = ((y - centerY) / centerY) * -10;
        const rotateY = ((x - centerX) / centerX) * 10;

        entry.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale(1.05)`;
      });

      entry.addEventListener('mouseleave', () => {
        entry.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) scale(1)';
      });
    }

    /**
     * Effet ripple au clic
     */
    addRippleEffect(entry) {
      entry.addEventListener('click', (e) => {
        const ripple = document.createElement('span');
        const rect = entry.getBoundingClientRect();

        const size = Math.max(rect.width, rect.height);
        const x = e.clientX - rect.left - size / 2;
        const y = e.clientY - rect.top - size / 2;

        ripple.style.width = ripple.style.height = size + 'px';
        ripple.style.left = x + 'px';
        ripple.style.top = y + 'px';
        ripple.classList.add('ripple-effect');

        entry.style.position = 'relative';
        entry.style.overflow = 'hidden';
        entry.appendChild(ripple);

        setTimeout(() => {
          ripple.remove();
        }, 600);
      });

      // Ajouter les styles du ripple
      this.addRippleStyles();
    }

    /**
     * Styles pour l'effet ripple
     */
    addRippleStyles() {
      if (!document.getElementById('ripple-styles')) {
        const style = document.createElement('style');
        style.id = 'ripple-styles';
        style.textContent = `
          .ripple-effect {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.6);
            transform: scale(0);
            animation: ripple-animation 0.6s ease-out;
            pointer-events: none;
            z-index: 10;
          }

          @keyframes ripple-animation {
            to {
              transform: scale(4);
              opacity: 0;
            }
          }
        `;
        document.head.appendChild(style);
      }
    }

    /**
     * Animation lazy load pour les images
     */
    addLazyLoadAnimation(entry) {
      const images = entry.querySelectorAll('img');

      images.forEach(img => {
        if (!img.complete) {
          img.style.opacity = '0';
          img.addEventListener('load', () => {
            img.style.transition = 'opacity 0.5s ease';
            img.style.opacity = '1';
          });
        }
      });
    }
  }

  /* ==========================================
     SCROLL EFFECTS - Bouton Retour en Haut
     ========================================== */

  class ScrollToTop {
    constructor() {
      this.button = null;
      this.init();
    }

    init() {
      this.createButton();
      this.addScrollListener();
    }

    createButton() {
      this.button = document.createElement('button');
      this.button.innerHTML = '↑';
      this.button.className = 'scroll-to-top';
      this.button.setAttribute('aria-label', 'Retour en haut');
      this.button.style.cssText = `
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #c8d9e6 0%, #a8b9c6 100%);
        color: white;
        border: none;
        border-radius: 50%;
        font-size: 24px;
        cursor: pointer;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
        opacity: 0;
        transform: scale(0);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        z-index: 1000;
      `;

      this.button.addEventListener('click', () => {
        window.scrollTo({
          top: 0,
          behavior: 'smooth'
        });
      });

      this.button.addEventListener('mouseenter', () => {
        this.button.style.transform = 'scale(1.1)';
      });

      this.button.addEventListener('mouseleave', () => {
        this.button.style.transform = 'scale(1)';
      });

      document.body.appendChild(this.button);
    }

    addScrollListener() {
      const handleScroll = throttle(() => {
        if (window.pageYOffset > CONFIG.scrollThreshold) {
          this.button.style.opacity = '1';
          this.button.style.transform = 'scale(1)';
        } else {
          this.button.style.opacity = '0';
          this.button.style.transform = 'scale(0)';
        }
      }, 100);

      window.addEventListener('scroll', handleScroll, { passive: true });
    }
  }

  /* ==========================================
     PERFORMANCE - Smooth Scroll
     ========================================== */

  function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function(e) {
        const href = this.getAttribute('href');
        if (href !== '#' && href !== '#0') {
          const target = document.querySelector(href);
          if (target) {
            e.preventDefault();
            target.scrollIntoView({
              behavior: 'smooth',
              block: 'start'
            });
          }
        }
      });
    });
  }

  /* ==========================================
     LOADING - Indicateur de Progression
     ========================================== */

  class ProgressIndicator {
    constructor() {
      this.init();
    }

    init() {
      const progressBar = document.createElement('div');
      progressBar.className = 'reading-progress';
      progressBar.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 0%;
        height: 3px;
        background: linear-gradient(90deg, #c8d9e6 0%, #4CAF50 100%);
        z-index: 9999;
        transition: width 0.1s ease;
      `;
      document.body.appendChild(progressBar);

      const handleScroll = throttle(() => {
        const windowHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        const scrolled = (window.pageYOffset / windowHeight) * 100;
        progressBar.style.width = scrolled + '%';
      }, 10);

      window.addEventListener('scroll', handleScroll, { passive: true });
    }
  }

  /* ==========================================
     INITIALISATION
     ========================================== */

  function init() {
    // Attendre que le DOM soit chargé
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', initAll);
    } else {
      initAll();
    }
  }

  function initAll() {
    console.log('🎨 Modern Interactions initialized');

    // Initialiser tous les effets
    new PageHeaderEffects();
    new HeadingEffects();
    new BlogEntryEffects();
    new ScrollToTop();
    new ProgressIndicator();

    initSmoothScroll();

    // Ajouter une classe au body pour indiquer que JS est chargé
    document.body.classList.add('modern-js-loaded');

    // Performance: Préchargement des images au hover
    document.querySelectorAll('a[href]').forEach(link => {
      link.addEventListener('mouseenter', function() {
        const url = this.href;
        if (url && !url.startsWith('#')) {
          const prefetch = document.createElement('link');
          prefetch.rel = 'prefetch';
          prefetch.href = url;
          document.head.appendChild(prefetch);
        }
      }, { once: true });
    });
  }

  // Lancer l'initialisation
  init();

  /* ==========================================
     EXPORT (si besoin de réutiliser)
     ========================================== */

  window.ModernInteractions = {
    PageHeaderEffects,
    HeadingEffects,
    BlogEntryEffects,
    ScrollToTop,
    ProgressIndicator,
    utils: {
      debounce,
      throttle,
      isInViewport
    }
  };

})();
