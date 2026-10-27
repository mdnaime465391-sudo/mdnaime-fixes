/* mdnaime.design — public interaction layer.
   Motion uses transform/opacity only and is skipped for reduced-motion users. */
(function () {
  'use strict';

  var $ = function (selector, root) { return (root || document).querySelector(selector); };
  var $$ = function (selector, root) { return Array.prototype.slice.call((root || document).querySelectorAll(selector)); };
  var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var csrfMeta = $('meta[name="csrf-token"]');
  var csrf = csrfMeta ? csrfMeta.content : '';

  function raf(fn) { return window.requestAnimationFrame(fn); }

  /* Preloader --------------------------------------------------------- */
  function initPreloader() {
    var loader = $('.preloader');
    if (!loader) { return; }
    if (reduced) { loader.classList.add('is-done'); return; }

    var count = $('.p-count', loader);
    var bar = $('.p-bar span', loader);
    var start = performance.now();
    var duration = 720;

    function step(now) {
      var progress = Math.min(1, (now - start) / duration);
      var eased = 1 - Math.pow(1 - progress, 3);
      if (count) { count.textContent = String(Math.round(eased * 100)).padStart(3, '0'); }
      if (bar) { bar.style.transform = 'scaleX(' + eased + ')'; }
      if (progress < 1) { raf(step); } else {
        setTimeout(function () { loader.classList.add('is-done'); }, 90);
      }
    }

    raf(step);
    window.setTimeout(function () { loader.classList.add('is-done'); }, 1600);
  }

  /* Split headlines --------------------------------------------------- */
  function splitNode(node) {
    Array.prototype.slice.call(node.childNodes).forEach(function (child) {
      if (child.nodeType === 3) {
        var words = child.nodeValue.split(/\s+/).filter(Boolean);
        if (!words.length) { return; }
        var fragment = document.createDocumentFragment();
        words.forEach(function (word) {
          var outer = document.createElement('span');
          outer.className = 'word';
          var inner = document.createElement('span');
          inner.className = 'word-in';
          inner.textContent = word;
          outer.appendChild(inner);
          fragment.appendChild(outer);
          fragment.appendChild(document.createTextNode(' '));
        });
        child.parentNode.replaceChild(fragment, child);
      } else if (child.nodeType === 1 && !child.classList.contains('word')) {
        splitNode(child);
      }
    });
  }

  function initSplit() {
    if (reduced) { return; }
    $$('.split').forEach(function (node) {
      splitNode(node);
      $$('.word-in', node).forEach(function (word, index) {
        word.style.setProperty('--d', Math.min(index * 0.035, 0.45) + 's');
      });
    });
  }

  /* Reveals ----------------------------------------------------------- */
  function initReveal() {
    var nodes = $$('.reveal, .reveal-img, .split');
    if (!nodes.length) { return; }

    if (!('IntersectionObserver' in window) || reduced) {
      nodes.forEach(function (node) { node.classList.add('is-in'); });
      return;
    }

    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) { return; }
        entry.target.classList.add('is-in');
        observer.unobserve(entry.target);
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -6% 0px' });

    nodes.forEach(function (node) { observer.observe(node); });

    /* Failsafe: if the observer ever misfires (restored scroll position,
       embeds, older engines), still reveal anything the user has scrolled
       into view. Redundant when the observer works. */
    var sweep = function () {
      nodes.forEach(function (node) {
        if (node.classList.contains('is-in')) { return; }
        if (node.getBoundingClientRect().top < window.innerHeight) { node.classList.add('is-in'); }
      });
    };
    window.addEventListener('scroll', sweep, { passive: true });
    window.addEventListener('resize', sweep);
    window.setTimeout(sweep, 1500);
  }

  /* Parallax ---------------------------------------------------------- */
  function initParallax() {
    var nodes = $$('[data-parallax]');
    if (!nodes.length || reduced || window.matchMedia('(max-width: 768px)').matches) { return; }
    var ticking = false;

    function update() {
      nodes.forEach(function (node) {
        var factor = parseFloat(node.getAttribute('data-parallax')) || 0.04;
        var rect = node.getBoundingClientRect();
        if (rect.bottom < -200 || rect.top > window.innerHeight + 200) { return; }
        var offset = (rect.top + rect.height / 2 - window.innerHeight / 2) * factor * -1;
        node.style.transform = 'translate3d(0,' + offset.toFixed(2) + 'px,0)';
      });
      ticking = false;
    }

    window.addEventListener('scroll', function () {
      if (!ticking) { ticking = true; raf(update); }
    }, { passive: true });
    update();
  }

  /* Magnetic buttons -------------------------------------------------- */
  function initMagnetic() {
    if (reduced || !window.matchMedia('(hover: hover) and (pointer: fine)').matches) { return; }
    $$('[data-magnetic]').forEach(function (button) {
      var strength = parseFloat(button.getAttribute('data-magnetic')) || 0.25;
      button.addEventListener('mousemove', function (event) {
        var rect = button.getBoundingClientRect();
        var x = (event.clientX - rect.left - rect.width / 2) * strength;
        var y = (event.clientY - rect.top - rect.height / 2) * strength;
        button.style.transform = 'translate(' + x.toFixed(1) + 'px,' + y.toFixed(1) + 'px)';
      });
      button.addEventListener('mouseleave', function () { button.style.transform = ''; });
    });
  }

  /* Cursor ------------------------------------------------------------ */
  function initCursor() {
    if (reduced || !window.matchMedia('(hover: hover) and (pointer: fine)').matches) { return; }
    var ring = document.createElement('div');
    var dot = document.createElement('div');
    ring.className = 'cursor';
    dot.className = 'cursor-dot';
    ring.setAttribute('aria-hidden', 'true');
    dot.setAttribute('aria-hidden', 'true');
    document.body.appendChild(ring);
    document.body.appendChild(dot);

    var targetX = window.innerWidth / 2;
    var targetY = window.innerHeight / 2;
    var ringX = targetX;
    var ringY = targetY;

    document.addEventListener('mousemove', function (event) {
      targetX = event.clientX;
      targetY = event.clientY;
      dot.style.transform = 'translate(' + targetX + 'px,' + targetY + 'px) translate(-50%,-50%)';
      ring.classList.remove('is-hidden');
      dot.classList.remove('is-hidden');
    });

    document.addEventListener('mouseleave', function () {
      ring.classList.add('is-hidden');
      dot.classList.add('is-hidden');
    });

    document.addEventListener('mouseover', function (event) {
      if (event.target.closest('a, button, [data-magnetic], .chip, .work-card')) {
        ring.classList.add('is-hover');
      }
    });

    document.addEventListener('mouseout', function (event) {
      if (event.target.closest('a, button, [data-magnetic], .chip, .work-card')) {
        ring.classList.remove('is-hover');
      }
    });

    (function loop() {
      ringX += (targetX - ringX) * 0.16;
      ringY += (targetY - ringY) * 0.16;
      ring.style.transform = 'translate(' + ringX.toFixed(1) + 'px,' + ringY.toFixed(1) + 'px) translate(-50%,-50%)';
      raf(loop);
    })();
  }

  /* Marquee ----------------------------------------------------------- */
  function initMarquee() {
    $$('.marquee-track').forEach(function (track) {
      if (track.dataset.cloned === 'true') { return; }
      track.innerHTML += track.innerHTML;
      track.dataset.cloned = 'true';
    });
  }

  /* Navigation / menu ------------------------------------------------- */
  function initNav() {
    var nav = $('.site-nav');
    if (nav) {
      var onScroll = function () { nav.classList.toggle('is-solid', window.scrollY > 8); };
      window.addEventListener('scroll', onScroll, { passive: true });
      onScroll();
    }

    var toggle = $('.menu-toggle');
    var overlay = $('#menu');
    if (!toggle || !overlay) { return; }

    function setMenu(open) {
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
      overlay.setAttribute('aria-hidden', open ? 'false' : 'true');
      overlay.classList.toggle('is-open', open);
      document.body.classList.toggle('menu-open', open);
      if (open) {
        var first = $('a', overlay);
        if (first) { window.setTimeout(function () { first.focus(); }, 260); }
      }
    }

    toggle.addEventListener('click', function () {
      setMenu(overlay.getAttribute('aria-hidden') === 'true');
    });

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape' && overlay.classList.contains('is-open')) {
        setMenu(false);
        toggle.focus();
      }
    });

    $$('a', overlay).forEach(function (link) {
      link.addEventListener('click', function () { setMenu(false); });
    });
  }

  /* Back to top / year ------------------------------------------------ */
  function initUtilities() {
    var top = $('[data-to-top]');
    if (top) {
      top.addEventListener('click', function (event) {
        event.preventDefault();
        window.scrollTo({ top: 0, behavior: reduced ? 'auto' : 'smooth' });
      });
    }

    $$('[data-year]').forEach(function (node) { node.textContent = String(new Date().getFullYear()); });
  }

  /* Page transition curtain ------------------------------------------- */
  function initTransitions() {
    var curtain = $('.curtain');
    if (!curtain || reduced) { return; }

    $$('a.js-nav').forEach(function (link) {
      link.addEventListener('click', function (event) {
        var href = link.getAttribute('href');
        if (!href || href.charAt(0) === '#' || link.target === '_blank' || event.metaKey || event.ctrlKey || event.shiftKey) { return; }
        if (link.host !== window.location.host) { return; }
        if (link.pathname === window.location.pathname && link.hash) { return; }

        event.preventDefault();
        curtain.classList.add('is-exit');
        window.setTimeout(function () { window.location.href = href; }, 380);
      });
    });
  }

  /* Work filter + progressive reveal ---------------------------------- */
  function initWork() {
    var grid = $('[data-work-grid]');
    if (!grid) { return; }

    var cards = $$('.work-card', grid);
    var bar = $('[data-filter-bar]');
    var more = $('[data-load-more]');
    var wrap = $('[data-load-more-wrap]');
    var empty = $('[data-work-empty]');
    var INITIAL = 6;
    var BATCH = 3;
    var filter = 'all';
    var shown = INITIAL;

    function matches(card) {
      if (filter === 'all') { return true; }
      return (card.getAttribute('data-categories') || '').split('|').indexOf(filter) !== -1;
    }

    function paint() {
      var list = cards.filter(matches);
      grid.classList.toggle('is-filtered', filter !== 'all');
      cards.forEach(function (card) { card.hidden = true; });
      list.forEach(function (card, index) { card.hidden = index >= shown; });

      if (empty) { empty.hidden = list.length > 0; }

      if (wrap && more) {
        var canToggle = list.length > INITIAL;
        wrap.hidden = !canToggle;
        if (canToggle) {
          var expanded = shown >= list.length;
          more.textContent = expanded ? 'Show less' : 'Show more projects';
          more.setAttribute('aria-expanded', expanded ? 'true' : 'false');
        }
      }
    }

    if (more) {
      more.addEventListener('click', function () {
        var list = cards.filter(matches);
        if (shown >= list.length) { shown = INITIAL; }
        else { shown = Math.min(shown + BATCH, list.length); }
        paint();
      });
    }

    if (bar) {
      var chips = $$('.chip', bar);
      chips.forEach(function (chip) {
        chip.addEventListener('click', function () {
          filter = chip.getAttribute('data-filter') || 'all';
          shown = INITIAL;
          chips.forEach(function (item) {
            var active = item === chip;
            item.classList.toggle('is-active', active);
            item.setAttribute('aria-pressed', active ? 'true' : 'false');
          });
          paint();
        });
      });

      /* keep the sticky bar flush under the real header height */
      var nav = $('.site-nav');
      var setOffset = function () {
        if (nav) { bar.style.top = nav.offsetHeight + 'px'; }
      };
      setOffset();
      window.addEventListener('resize', setOffset);
      window.addEventListener('load', setOffset);
      if (document.fonts && document.fonts.ready) { document.fonts.ready.then(setOffset); }

      /* drag-to-pan on desktop, native swipe on touch */
      var down = false;
      var startX = 0;
      var startScroll = 0;
      var moved = false;
      var suppressClick = false;

      bar.addEventListener('pointerdown', function (event) {
        if (event.pointerType !== 'mouse') { return; }
        down = true;
        moved = false;
        startX = event.clientX;
        startScroll = bar.scrollLeft;
      });

      window.addEventListener('pointermove', function (event) {
        if (!down) { return; }
        var delta = event.clientX - startX;
        if (!moved && Math.abs(delta) > 6) {
          moved = true;
          bar.classList.add('is-dragging');
          if (bar.setPointerCapture) { try { bar.setPointerCapture(event.pointerId); } catch (e) {} }
        }
        if (moved) { bar.scrollLeft = startScroll - delta; }
      });

      window.addEventListener('pointerup', function () {
        down = false;
        if (moved) {
          suppressClick = true;
          bar.classList.remove('is-dragging');
          window.setTimeout(function () { suppressClick = false; }, 80);
        }
      });

      bar.addEventListener('click', function (event) {
        if (!suppressClick) { return; }
        suppressClick = false;
        event.preventDefault();
        event.stopPropagation();
      }, true);
    }

    paint();
  }

  /* Contact form ------------------------------------------------------ */
  function initInquiryForm() {
    var form = $('[data-inquiry-form]');
    if (!form) { return; }

    var success = $('[data-inquiry-success]');
    var summary = $('[data-success-summary]');
    var banner = $('[data-form-status]');
    var submit = form.querySelector('button[type="submit"]');

    function clearErrors() {
      $$('.field', form).forEach(function (field) { field.classList.remove('has-error'); });
      $$('.err', form).forEach(function (error) { error.textContent = ''; });
      if (banner) { banner.classList.remove('is-on'); banner.textContent = ''; }
    }

    function showErrors(errors) {
      Object.keys(errors || {}).forEach(function (name) {
        var input = form.querySelector('[name="' + name + '"]');
        if (!input) { return; }
        var field = input.closest('.field');
        if (field) { field.classList.add('has-error'); }
        var error = document.getElementById(name + '-err');
        if (error) { error.textContent = errors[name][0]; }
      });
      var first = form.querySelector('.field.has-error .input, .field.has-error .select, .field.has-error .textarea');
      if (first) { first.focus(); }
    }

    form.addEventListener('submit', function (event) {
      event.preventDefault();
      clearErrors();

      if (submit) { submit.disabled = true; submit.dataset.label = submit.textContent; submit.textContent = 'Sending…'; }

      fetch(form.action, {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrf
        },
        body: new FormData(form)
      }).then(function (response) {
        if (response.status === 422) {
          return response.json().then(function (data) {
            showErrors(data.errors);
            if (banner) { banner.textContent = 'Check the highlighted fields and try again.'; banner.classList.add('is-on'); }
          });
        }

        if (response.status === 429) {
          throw new Error('Too many inquiries from this connection. Please try again in a minute, or email directly.');
        }

        if (!response.ok) {
          throw new Error('Something went wrong. Please email mdnaime4657@gmail.com and I will pick it up from there.');
        }

        return response.json().then(function (data) {
          if (summary) { summary.textContent = data.summary || 'Project inquiry'; }
          form.hidden = true;
          if (success) {
            success.hidden = false;
            success.classList.add('is-on');
            success.setAttribute('tabindex', '-1');
            success.focus({ preventScroll: true });
          }
          form.reset();
        });
      }).catch(function (error) {
        if (banner) { banner.textContent = error.message; banner.classList.add('is-on'); }
      }).finally(function () {
        if (submit) { submit.disabled = false; submit.textContent = submit.dataset.label || 'Send inquiry'; }
      });
    });

    var again = $('[data-form-again]');
    if (again) {
      again.addEventListener('click', function () {
        if (success) { success.classList.remove('is-on'); success.hidden = true; }
        form.hidden = false;
        clearErrors();
        var first = form.querySelector('.input');
        if (first) { first.focus(); }
      });
    }
  }

  /* Live preview fallback --------------------------------------------- */
  function initLiveFrames() {
    $$('[data-live-frame]').forEach(function (frame) {
      var iframe = $('iframe', frame);
      if (!iframe) { return; }
      var loaded = false;
      var started = false;
      var timeout = parseInt(frame.getAttribute('data-timeout'), 10) || 7000;

      iframe.addEventListener('load', function () {
        loaded = true;
        frame.classList.remove('is-blocked');
      });

      function startTimer() {
        if (started) { return; }
        started = true;
        window.setTimeout(function () {
          if (!loaded) { frame.classList.add('is-blocked'); }
        }, timeout);
      }

      if ('IntersectionObserver' in window) {
        var observer = new IntersectionObserver(function (entries) {
          entries.forEach(function (entry) {
            if (entry.isIntersecting) {
              startTimer();
              observer.unobserve(frame);
            }
          });
        }, { rootMargin: '200px 0px' });
        observer.observe(frame);
      } else {
        startTimer();
      }
    });
  }

  /* Smooth (eased) wheel scrolling ------------------------------------ */
  /* Ported from the prototype build: interpolates wheel input per frame so
     scrolling feels weighted. Desktop pointers only; touch and reduced
     motion keep native scrolling. */
  function initSmoothScroll() {
    if (reduced) { return; }
    var coarse = window.matchMedia('(hover: none), (pointer: coarse)').matches;
    if (coarse) { return; }

    var doc = document.documentElement;
    doc.style.scrollBehavior = 'auto'; // CSS smooth scrolling would fight the per-frame writes

    var target = window.scrollY;
    var current = target;
    var ticking = false;
    var programmatic = false;
    var maxScroll = function () { return Math.max(0, doc.scrollHeight - window.innerHeight); };

    function isScrollable(node) {
      var el = node;
      while (el && el !== document.body && el.nodeType === 1) {
        var s = window.getComputedStyle(el);
        if (/(auto|scroll)/.test(s.overflowY) && el.scrollHeight > el.clientHeight + 4) { return true; }
        el = el.parentElement;
      }
      return false;
    }

    function loop() {
      current += (target - current) * 0.14;
      if (Math.abs(target - current) < 0.4) { current = target; ticking = false; }
      else { ticking = true; }
      programmatic = true;
      window.scrollTo(0, current);
      if (ticking) { requestAnimationFrame(loop); }
    }

    window.addEventListener('wheel', function (event) {
      if (event.ctrlKey || document.body.classList.contains('menu-open')) { return; }
      if (isScrollable(event.target)) { return; }
      event.preventDefault();
      target = Math.max(0, Math.min(maxScroll(), target + event.deltaY));
      if (!ticking) { ticking = true; requestAnimationFrame(loop); }
    }, { passive: false });

    window.addEventListener('scroll', function () {
      if (programmatic) { programmatic = false; return; }
      target = current = window.scrollY;
      ticking = false;
    }, { passive: true });

    window.addEventListener('resize', function () { target = Math.min(target, maxScroll()); });
  }

  function boot() {
    initPreloader();
    initSplit();
    initReveal();
    initParallax();
    initMagnetic();
    initCursor();
    initMarquee();
    initNav();
    initSmoothScroll();
    initUtilities();
    initTransitions();
    initWork();
    initInquiryForm();
    initLiveFrames();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();
