/* Md Naime admin — theme, navigation, repeaters, editor, media ordering. */
(function () {
  'use strict';

  var csrf = document.querySelector('meta[name="csrf-token"]');
  var token = csrf ? csrf.content : '';

  function $(selector, root) { return (root || document).querySelector(selector); }
  function $$(selector, root) { return Array.prototype.slice.call((root || document).querySelectorAll(selector)); }

  /* Theme ------------------------------------------------------------- */
  var root = document.documentElement;
  var themeButton = $('[data-theme-toggle]');

  function setTheme(theme) {
    root.setAttribute('data-theme', theme);
    try { localStorage.setItem('admin-theme', theme); } catch (e) {}
    if (themeButton) {
      themeButton.setAttribute('aria-label', theme === 'dark' ? 'Switch to light theme' : 'Switch to dark theme');
      themeButton.setAttribute('title', theme === 'dark' ? 'Light theme' : 'Dark theme');
      var label = $('[data-theme-label]', themeButton);
      if (label) { label.textContent = theme === 'dark' ? 'Light' : 'Dark'; }
    }
  }

  if (themeButton) {
    themeButton.addEventListener('click', function () {
      setTheme(root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark');
    });
  }

  window.addEventListener('storage', function (event) {
    if (event.key === 'admin-theme' && event.newValue) { setTheme(event.newValue); }
  });

  /* Mobile sidebar ---------------------------------------------------- */
  var sidebar = $('[data-sidebar]');
  var scrim = $('[data-sidebar-scrim]');
  var menuButton = $('[data-menu-btn]');

  function setSidebar(open) {
    if (!sidebar) { return; }
    sidebar.classList.toggle('is-open', open);
    if (scrim) { scrim.classList.toggle('is-open', open); }
    if (menuButton) { menuButton.setAttribute('aria-expanded', open ? 'true' : 'false'); }
  }

  if (menuButton) {
    menuButton.addEventListener('click', function () {
      setSidebar(!sidebar.classList.contains('is-open'));
    });
  }
  if (scrim) { scrim.addEventListener('click', function () { setSidebar(false); }); }
  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') { setSidebar(false); }
  });

  /* Confirm destructive actions --------------------------------------- */
  $$('form[data-confirm]').forEach(function (form) {
    form.addEventListener('submit', function (event) {
      if (!window.confirm(form.getAttribute('data-confirm') || 'Are you sure?')) {
        event.preventDefault();
      }
    });
  });

  /* Tabs -------------------------------------------------------------- */
  $$('[data-tabs]').forEach(function (group) {
    var buttons = $$('[data-tab]', group);
    var storageKey = group.getAttribute('data-tabs');

    function activate(name, focus) {
      buttons.forEach(function (button) {
        var active = button.getAttribute('data-tab') === name;
        button.classList.toggle('is-active', active);
        button.setAttribute('aria-selected', active ? 'true' : 'false');
        if (active && focus) { button.focus(); }
      });
      $$('[data-tab-panel]', group).forEach(function (panel) {
        panel.hidden = panel.getAttribute('data-tab-panel') !== name;
      });
      if (storageKey) {
        try { sessionStorage.setItem('admin-tab-' + storageKey, name); } catch (e) {}
      }
    }

    buttons.forEach(function (button, index) {
      button.addEventListener('click', function () { activate(button.getAttribute('data-tab')); });
      button.addEventListener('keydown', function (event) {
        if (event.key !== 'ArrowRight' && event.key !== 'ArrowLeft') { return; }
        event.preventDefault();
        var next = buttons[(index + (event.key === 'ArrowRight' ? 1 : -1) + buttons.length) % buttons.length];
        activate(next.getAttribute('data-tab'), true);
      });
    });

    var hash = (window.location.hash || '').replace('#', '');
    var matchFromHash = hash ? buttons.find(function (b) {
      return b.getAttribute('data-tab') === hash || b.getAttribute('data-tab') === 'media-' + hash;
    }) : null;

    var initial = storageKey ? (function () {
      try { return sessionStorage.getItem('admin-tab-' + storageKey); } catch (e) { return null; }
    })() : null;

    var targetButton = matchFromHash
      || (initial && buttons.find(function (b) { return b.getAttribute('data-tab') === initial; }))
      || buttons.find(function (b) { return b.classList.contains('is-active'); })
      || buttons[0];

    if (targetButton) { activate(targetButton.getAttribute('data-tab')); }
  });

  /* Repeaters --------------------------------------------------------- */
  $$('[data-repeater]').forEach(function (repeater) {
    var template = $('[data-repeater-template]', repeater);
    var list = $('[data-repeater-list]', repeater);

    function addRow() {
      var fragment;
      if (template) {
        fragment = template.content.cloneNode(true);
      } else {
        // Fallback when the template is missing: clone the last populated row.
        fragment = document.createDocumentFragment();
        var clone = list.lastElementChild && list.lastElementChild.cloneNode(true);
        if (!clone) { return; }
        $$('input, textarea', clone).forEach(function (field) { field.value = ''; });
        fragment.appendChild(clone);
      }

      list.appendChild(fragment);
      var first = list.lastElementChild && list.lastElementChild.querySelector('input, textarea');
      if (first) { first.focus(); }
    }

    repeater.addEventListener('click', function (event) {
      var add = event.target.closest('[data-add-row]');
      if (add && repeater.contains(add)) {
        event.preventDefault();
        addRow();
        return;
      }

      var remove = event.target.closest('[data-remove-row]');
      if (remove && repeater.contains(remove)) {
        event.preventDefault();
        var row = remove.closest('[data-repeater-row]');
        if (row) { row.remove(); }
      }
    });
  });

  /* Slug -------------------------------------------------------------- */
  var slugSource = $('[data-slug-source]');
  var slugTarget = $('[data-slug-target]');
  if (slugSource && slugTarget) {
    var manual = slugTarget.value.trim().length > 0 && slugTarget.dataset.auto !== 'true';
    slugTarget.addEventListener('input', function () { manual = true; });
    slugSource.addEventListener('input', function () {
      if (manual) { return; }
      slugTarget.value = slugSource.value
        .toLowerCase()
        .normalize('NFKD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '')
        .slice(0, 80);
    });
  }

  /* Image previews ---------------------------------------------------- */
  $$('[data-image-input]').forEach(function (input) {
    var preview = $(input.getAttribute('data-image-input'));
    if (!preview) { return; }
    input.addEventListener('change', function () {
      var file = input.files && input.files[0];
      if (!file) { return; }
      preview.innerHTML = '';
      var image = document.createElement('img');
      image.alt = '';
      image.src = URL.createObjectURL(file);
      image.style.width = '100%';
      image.style.height = '100%';
      image.style.objectFit = 'cover';
      image.style.borderRadius = '7px';
      preview.appendChild(image);
      preview.style.padding = '0';
    });
  });

  /* Rich text editor -------------------------------------------------- */
  $$('[data-editor]').forEach(function (editor) {
    var body = $('[data-editor-body]', editor);
    var input = $('[data-editor-input]', editor)
      || (editor.parentElement && $('[data-editor-input]', editor.parentElement));
    if (!body || !input) { return; }

    if (input.value.trim() && !body.innerHTML.trim()) { body.innerHTML = input.value; }

    function sync() { input.value = body.innerHTML.trim(); }
    body.addEventListener('input', sync);
    body.addEventListener('blur', sync);

    $$('[data-cmd]', editor).forEach(function (button) {
      button.addEventListener('mousedown', function (event) {
        event.preventDefault();
        var command = button.getAttribute('data-cmd');
        body.focus();

        if (command === 'createLink') {
          var url = window.prompt('Link URL (https://…)');
          if (url) { document.execCommand('createLink', false, url); }
        } else if (command === 'formatBlock') {
          var block = button.getAttribute('data-value') || 'p';
          document.execCommand('formatBlock', false, '<' + block + '>');
        } else {
          document.execCommand(command, false, null);
        }

        sync();
      });
    });

    var form = editor.closest('form');
    if (form) { form.addEventListener('submit', sync); }
  });

  // Ensure all editors sync before any form submit even if submitted externally
  document.addEventListener('submit', function (event) {
    var form = event.target;
    if (form && form.querySelectorAll) {
      $$('[data-editor]', form).forEach(function (editor) {
        var body = $('[data-editor-body]', editor);
        var input = $('[data-editor-input]', editor)
          || (editor.parentElement && $('[data-editor-input]', editor.parentElement));
        if (body && input) {
          input.value = body.innerHTML.trim();
        }
      });
    }
  }, true);

  /* Media drag ordering ---------------------------------------------- */
  $$('[data-media-grid]').forEach(function (mediaGrid) {
    var reorderUrl = mediaGrid.getAttribute('data-reorder-url');
    var dragging = null;

    mediaGrid.addEventListener('dragstart', function (event) {
      var card = event.target.closest('[data-media-id]');
      if (!card) { return; }
      dragging = card;
      card.classList.add('is-dragging');
      if (event.dataTransfer) {
        event.dataTransfer.effectAllowed = 'move';
        event.dataTransfer.setData('text/plain', card.getAttribute('data-media-id'));
      }
    });

    mediaGrid.addEventListener('dragend', function () {
      if (dragging) { dragging.classList.remove('is-dragging'); }
      $$('.is-over', mediaGrid).forEach(function (el) { el.classList.remove('is-over'); });
      dragging = null;
    });

    mediaGrid.addEventListener('dragover', function (event) {
      var card = event.target.closest('[data-media-id]');
      if (!card || !dragging || card === dragging) { return; }
      event.preventDefault();
      card.classList.add('is-over');

      var rect = card.getBoundingClientRect();
      var after = event.clientX > rect.left + rect.width / 2;
      mediaGrid.insertBefore(dragging, after ? card.nextElementSibling : card);
    });

    mediaGrid.addEventListener('dragleave', function (event) {
      var card = event.target.closest('[data-media-id]');
      if (card) { card.classList.remove('is-over'); }
    });

    mediaGrid.addEventListener('drop', function (event) {
      event.preventDefault();
      $$('.is-over', mediaGrid).forEach(function (el) { el.classList.remove('is-over'); });
      if (!reorderUrl) { return; }

      var ids = $$('[data-media-id]', mediaGrid).map(function (card) {
        return Number(card.getAttribute('data-media-id'));
      });

      fetch(reorderUrl, {
        method: 'PATCH',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': token
        },
        body: JSON.stringify({ ids: ids })
      }).catch(function () { /* order stays local; a reload restores it */ });
    });

    $$('[data-caption-url]', mediaGrid).forEach(function (input) {
      var original = input.value;
      input.addEventListener('change', function () {
        if (input.value === original) { return; }
        fetch(input.getAttribute('data-caption-url'), {
          method: 'PATCH',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': token
          },
          body: JSON.stringify({ caption: input.value })
        }).then(function (response) {
          if (response.ok) {
            original = input.value;
            input.style.borderColor = 'var(--good)';
            setTimeout(function () { input.style.borderColor = ''; }, 900);
          }
        }).catch(function () {});
      });
    });
  });

  /* Dropzones --------------------------------------------------------- */
  $$('[data-dropzone]').forEach(function (zone) {
    var input = $('input[type="file"]', zone);
    if (!input) { return; }

    zone.addEventListener('click', function (event) {
      if (event.target.closest('button, input, label, a')) { return; }
      input.click();
    });

    input.addEventListener('change', function () {
      var label = $('[data-file-summary]', zone);
      if (label && input.files.length) {
        label.textContent = input.files.length + ' file' + (input.files.length === 1 ? '' : 's') + ' selected';
      }
    });

    ['dragenter', 'dragover'].forEach(function (name) {
      zone.addEventListener(name, function (event) {
        event.preventDefault();
        zone.classList.add('is-dragover');
      });
    });

    ['dragleave', 'drop'].forEach(function (name) {
      zone.addEventListener(name, function (event) {
        event.preventDefault();
        zone.classList.remove('is-dragover');
      });
    });

    zone.addEventListener('drop', function (event) {
      if (!event.dataTransfer || !event.dataTransfer.files.length) { return; }
      input.files = event.dataTransfer.files;
      input.dispatchEvent(new Event('change', { bubbles: true }));
    });
  });

  /* Copy fields ------------------------------------------------------- */
  $$('[data-copy]').forEach(function (button) {
    button.addEventListener('click', function () {
      var value = button.getAttribute('data-copy');
      if (!value) { return; }

      if (navigator.clipboard) {
        navigator.clipboard.writeText(value).then(function () {
          var previous = button.textContent;
          button.textContent = 'Copied';
          setTimeout(function () { button.textContent = previous; }, 1400);
        });
      }
    });
  });

  /* Auto-submit toggles --------------------------------------------- */
  $$('form[data-autosubmit] input[type="checkbox"]').forEach(function (input) {
    input.addEventListener('change', function () {
      if (input.form) { input.form.submit(); }
    });
  });

  /* Prevent double submits ------------------------------------------- */
  $$('form[data-lock-submit]').forEach(function (form) {
    form.addEventListener('submit', function () {
      var button = form.querySelector('button[type="submit"]');
      if (button) {
        setTimeout(function () {
          button.disabled = true;
          button.textContent = 'Saving…';
        }, 10);
      }
    });
  });
})();
