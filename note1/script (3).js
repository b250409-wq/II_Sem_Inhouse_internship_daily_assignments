/* =========================================================================
   NOTES APP — MAIN JAVASCRIPT
   Handles: authentication (session-based), note CRUD via PHP/MySQL API,
   live search, dark mode, scroll animations, animated counters, and
   form validation for all forms.
   ========================================================================= */

'use strict';

document.addEventListener('DOMContentLoaded', () => {

  /* =======================================================================
     0. CONSTANTS & STATE
     ======================================================================= */
  const API_BASE = 'api/';
  const THEME_KEY = 'notesApp.theme'; // theme is a local UI preference, kept client-side

  // In-memory cache of notes for the logged-in user, kept in sync with the DB.
  let notes = [];
  // Tracks which note is queued up for deletion while the confirm modal is open.
  let noteIdPendingDelete = null;
  // Current authenticated user, or null if logged out.
  let currentUser = null;

  /* =======================================================================
     1. API HELPER
     ======================================================================= */

  /**
   * Calls a JSON API endpoint and returns the parsed response body.
   * Always resolves (never throws) — check `.success` on the result.
   */
  async function apiCall(endpoint, options = {}) {
    try {
      const res = await fetch(API_BASE + endpoint, {
        method: options.method || 'GET',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'same-origin', // send the PHP session cookie
        body: options.body ? JSON.stringify(options.body) : undefined
      });
      const data = await res.json().catch(() => ({}));
      if (!res.ok && !('success' in data)) {
        return { success: false, error: 'Something went wrong. Please try again.' };
      }
      return data;
    } catch (err) {
      console.error('Notes App: API request failed.', err);
      return { success: false, error: 'Could not reach the server. Please check your connection.' };
    }
  }

  /* =======================================================================
     2. UTILITIES
     ======================================================================= */

  /** Escapes HTML special characters to prevent injection when rendering user text. */
  function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str == null ? '' : String(str);
    return div.innerHTML;
  }

  /** Formats a MySQL datetime string into a short, readable date/time string. */
  function formatTimestamp(ts) {
    if (!ts) return '';
    try {
      // MySQL returns "YYYY-MM-DD HH:MM:SS"; make it parseable cross-browser.
      const d = new Date(String(ts).replace(' ', 'T'));
      if (isNaN(d.getTime())) return '';
      return d.toLocaleDateString(undefined, { month: 'short', day: 'numeric' }) +
        ' · ' + d.toLocaleTimeString(undefined, { hour: '2-digit', minute: '2-digit' });
    } catch (err) {
      return '';
    }
  }

  /* =======================================================================
     3. NOTES RENDERING
     ======================================================================= */

  const notesGrid = document.getElementById('notesGrid');
  const emptyState = document.getElementById('emptyState');
  const notesCountLabel = document.getElementById('notesCountLabel');
  const clearAllBtn = document.getElementById('clearAllBtn');
  const searchInput = document.getElementById('searchNotes');
  const notesLoginPrompt = document.getElementById('notesLoginPrompt');
  const notesAppBody = document.getElementById('notesAppBody');

  /**
   * Renders the given list of notes into the notes grid.
   * Accepts a filtered list (for search) or the full notes array.
   */
  function renderNotes(list) {
    notesGrid.innerHTML = '';

    if (!list || list.length === 0) {
      emptyState.classList.remove('d-none');
      const isSearching = searchInput.value.trim().length > 0;
      emptyState.querySelector('p').textContent = isSearching
        ? 'No notes match your search.'
        : 'No notes yet. Add your first one above.';
      notesGrid.classList.add('d-none');
    } else {
      emptyState.classList.add('d-none');
      notesGrid.classList.remove('d-none');

      list.forEach(note => {
        const col = document.createElement('div');
        col.className = 'col-sm-6 col-lg-4';
        col.innerHTML = `
          <div class="note-item-card" data-id="${escapeHtml(note.id)}">
            <h4>${escapeHtml(note.title)}</h4>
            <p>${escapeHtml(note.body)}</p>
            <span class="note-meta">${formatTimestamp(note.updated_at || note.created_at)}${note.updated_at ? ' (edited)' : ''}</span>
            <div class="note-item-actions">
              <button type="button" class="edit-btn" aria-label="Edit note titled ${escapeHtml(note.title)}" title="Edit note">
                <i class="bi bi-pencil-square"></i>
              </button>
              <button type="button" class="delete-btn" aria-label="Delete note titled ${escapeHtml(note.title)}" title="Delete note">
                <i class="bi bi-trash3"></i>
              </button>
            </div>
          </div>
        `;
        notesGrid.appendChild(col);
      });
    }

    notesCountLabel.textContent = notes.length === 1 ? '1 note' : `${notes.length} notes`;
    clearAllBtn.classList.toggle('d-none', notes.length === 0);
  }

  /* =======================================================================
     4. ALERT HELPERS (Notes demo + Contact form share the same pattern)
     ======================================================================= */

  function showNoteAlert(message, type = 'success') {
    const alertBox = document.getElementById('noteAlert');
    alertBox.textContent = message;
    alertBox.className = `alert mt-3 alert-${type === 'success' ? 'success-custom' : 'danger-custom'}`;
    alertBox.classList.remove('d-none');
    window.clearTimeout(showNoteAlert._timer);
    showNoteAlert._timer = window.setTimeout(() => alertBox.classList.add('d-none'), 4000);
  }

  function showContactAlert(message, type = 'success') {
    const alertBox = document.getElementById('contactAlert');
    alertBox.textContent = message;
    alertBox.className = `alert alert-${type === 'success' ? 'success-custom' : 'danger-custom'}`;
    alertBox.classList.remove('d-none');
  }

  /* =======================================================================
     5. LOAD NOTES FROM SERVER
     ======================================================================= */

  async function loadNotes() {
    const result = await apiCall('notes_list.php');
    if (result.success) {
      notes = result.notes || [];
      applySearchFilter();
    } else {
      notes = [];
      renderNotes(notes);
      if (result.error) showNoteAlert(result.error, 'danger');
    }
  }

  /* =======================================================================
     6. ADD NOTE
     ======================================================================= */

  const noteForm = document.getElementById('noteForm');
  const noteTitleInput = document.getElementById('noteTitle');
  const noteBodyInput = document.getElementById('noteBody');

  noteForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    const title = noteTitleInput.value.trim();
    const body = noteBodyInput.value.trim();
    let hasError = false;

    if (!title) {
      noteTitleInput.classList.add('is-invalid');
      hasError = true;
    } else {
      noteTitleInput.classList.remove('is-invalid');
    }

    if (!body) {
      noteBodyInput.classList.add('is-invalid');
      hasError = true;
    } else {
      noteBodyInput.classList.remove('is-invalid');
    }

    if (hasError) {
      showNoteAlert('Please fill in both the title and note content before adding.', 'danger');
      return;
    }

    const addBtn = document.getElementById('addNoteBtn');
    addBtn.disabled = true;

    const result = await apiCall('notes_add.php', { method: 'POST', body: { title, body } });

    addBtn.disabled = false;

    if (result.success) {
      notes.unshift(result.note);
      applySearchFilter();
      showNoteAlert('Note added successfully.', 'success');
      noteForm.reset();
      noteTitleInput.focus();
    } else {
      showNoteAlert(result.error || 'Could not add your note. Please try again.', 'danger');
    }
  });

  [noteTitleInput, noteBodyInput].forEach(input => {
    input.addEventListener('input', () => input.classList.remove('is-invalid'));
  });

  /* =======================================================================
     7. EDIT NOTE
     ======================================================================= */

  const editNoteModalEl = document.getElementById('editNoteModal');
  const editNoteModal = new bootstrap.Modal(editNoteModalEl);
  const editNoteForm = document.getElementById('editNoteForm');
  const editNoteIdInput = document.getElementById('editNoteId');
  const editNoteTitleInput = document.getElementById('editNoteTitle');
  const editNoteBodyInput = document.getElementById('editNoteBody');

  function openEditModal(noteId) {
    const note = notes.find(n => String(n.id) === String(noteId));
    if (!note) {
      showNoteAlert('That note could not be found — it may have already been deleted.', 'danger');
      return;
    }
    editNoteIdInput.value = note.id;
    editNoteTitleInput.value = note.title;
    editNoteBodyInput.value = note.body;
    editNoteTitleInput.classList.remove('is-invalid');
    editNoteBodyInput.classList.remove('is-invalid');
    editNoteModal.show();
  }

  editNoteForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    const id = editNoteIdInput.value;
    const title = editNoteTitleInput.value.trim();
    const body = editNoteBodyInput.value.trim();
    let hasError = false;

    if (!title) {
      editNoteTitleInput.classList.add('is-invalid');
      hasError = true;
    } else {
      editNoteTitleInput.classList.remove('is-invalid');
    }

    if (!body) {
      editNoteBodyInput.classList.add('is-invalid');
      hasError = true;
    } else {
      editNoteBodyInput.classList.remove('is-invalid');
    }

    if (hasError) return;

    const result = await apiCall('notes_update.php', { method: 'POST', body: { id, title, body } });

    if (result.success) {
      const noteIndex = notes.findIndex(n => String(n.id) === String(id));
      if (noteIndex !== -1) notes[noteIndex] = result.note;
      applySearchFilter();
      editNoteModal.hide();
      showNoteAlert('Note updated successfully.', 'success');
    } else {
      showNoteAlert(result.error || 'That note could not be updated.', 'danger');
      editNoteModal.hide();
    }
  });

  /* =======================================================================
     8. DELETE NOTE (with confirmation modal)
     ======================================================================= */

  const deleteConfirmModalEl = document.getElementById('deleteConfirmModal');
  const deleteConfirmModal = new bootstrap.Modal(deleteConfirmModalEl);
  const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

  function openDeleteModal(noteId) {
    noteIdPendingDelete = noteId;
    deleteConfirmModal.show();
  }

  confirmDeleteBtn.addEventListener('click', async () => {
    if (!noteIdPendingDelete) return;

    const result = await apiCall('notes_delete.php', { method: 'POST', body: { id: noteIdPendingDelete } });

    if (result.success) {
      notes = notes.filter(n => String(n.id) !== String(noteIdPendingDelete));
      applySearchFilter();
      showNoteAlert('Note deleted.', 'success');
    } else {
      showNoteAlert(result.error || 'That note could not be found.', 'danger');
    }

    noteIdPendingDelete = null;
    deleteConfirmModal.hide();
  });

  deleteConfirmModalEl.addEventListener('hidden.bs.modal', () => {
    noteIdPendingDelete = null;
  });

  /* =======================================================================
     9. EVENT DELEGATION FOR EDIT / DELETE BUTTONS ON NOTE CARDS
     ======================================================================= */

  notesGrid.addEventListener('click', (e) => {
    const editBtn = e.target.closest('.edit-btn');
    const deleteBtn = e.target.closest('.delete-btn');
    const card = e.target.closest('.note-item-card');
    if (!card) return;
    const noteId = card.getAttribute('data-id');

    if (editBtn) openEditModal(noteId);
    if (deleteBtn) openDeleteModal(noteId);
  });

  /* =======================================================================
     10. SEARCH NOTES (client-side filter over the loaded notes cache)
     ======================================================================= */

  function applySearchFilter() {
    const query = searchInput.value.trim().toLowerCase();
    if (!query) {
      renderNotes(notes);
      return;
    }
    const filtered = notes.filter(n =>
      n.title.toLowerCase().includes(query) || n.body.toLowerCase().includes(query)
    );
    renderNotes(filtered);
  }

  searchInput.addEventListener('input', applySearchFilter);

  /* =======================================================================
     11. CLEAR ALL NOTES
     ======================================================================= */

  clearAllBtn.addEventListener('click', async () => {
    if (notes.length === 0) return;
    const confirmed = window.confirm('Delete all notes? This cannot be undone.');
    if (!confirmed) return;

    const result = await apiCall('notes_clear.php', { method: 'POST' });

    if (result.success) {
      notes = [];
      renderNotes(notes);
      showNoteAlert('All notes cleared.', 'success');
    } else {
      showNoteAlert(result.error || 'Could not clear your notes.', 'danger');
    }
  });

  /* =======================================================================
     12. DARK MODE TOGGLE
     ======================================================================= */

  const darkModeToggle = document.getElementById('darkModeToggle');
  const darkModeIcon = document.getElementById('darkModeIcon');

  function applyTheme(theme) {
    document.documentElement.setAttribute('data-theme', theme);
    darkModeIcon.className = theme === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-stars-fill';
    try {
      window.localStorage.setItem(THEME_KEY, theme);
    } catch (err) {
      console.warn('Notes App: could not persist theme preference.', err);
    }
  }

  (function initTheme() {
    let savedTheme = null;
    try {
      savedTheme = window.localStorage.getItem(THEME_KEY);
    } catch (err) {
      savedTheme = null;
    }
    if (savedTheme === 'dark' || savedTheme === 'light') {
      applyTheme(savedTheme);
    } else {
      const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
      applyTheme(prefersDark ? 'dark' : 'light');
    }
  })();

  darkModeToggle.addEventListener('click', () => {
    const current = document.documentElement.getAttribute('data-theme');
    applyTheme(current === 'dark' ? 'light' : 'dark');
  });

  /* =======================================================================
     13. ANIMATED COUNTERS (Statistics section)
     ======================================================================= */

  function animateCounter(el) {
    const target = parseInt(el.getAttribute('data-target'), 10) || 0;
    const suffix = el.getAttribute('data-suffix') || '';
    const duration = 1500;
    const startTime = performance.now();

    function tick(now) {
      const progress = Math.min((now - startTime) / duration, 1);
      const eased = 1 - Math.pow(1 - progress, 3);
      const current = Math.floor(eased * target);
      el.textContent = current.toLocaleString() + suffix;
      if (progress < 1) {
        window.requestAnimationFrame(tick);
      } else {
        el.textContent = target.toLocaleString() + suffix;
      }
    }
    window.requestAnimationFrame(tick);
  }

  /* =======================================================================
     14. SCROLL-TRIGGERED ANIMATIONS (fade-up / fade-in + counters)
     ======================================================================= */

  const animatedEls = document.querySelectorAll('[data-animate]');
  const statNumbers = document.querySelectorAll('.stat-number');
  let countersStarted = false;

  if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.15 });

    animatedEls.forEach(el => observer.observe(el));

    const statsSection = document.querySelector('.stats-section');
    if (statsSection) {
      const statsObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting && !countersStarted) {
            countersStarted = true;
            statNumbers.forEach(animateCounter);
            statsObserver.unobserve(entry.target);
          }
        });
      }, { threshold: 0.3 });
      statsObserver.observe(statsSection);
    }
  } else {
    animatedEls.forEach(el => el.classList.add('is-visible'));
    statNumbers.forEach(animateCounter);
  }

  /* =======================================================================
     15. BACK TO TOP BUTTON
     ======================================================================= */

  const backToTopBtn = document.getElementById('backToTopBtn');

  window.addEventListener('scroll', () => {
    backToTopBtn.classList.toggle('show', window.scrollY > 400);
  });

  backToTopBtn.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });

  /* =======================================================================
     16. GENERIC FORM VALIDATION HELPER
     ======================================================================= */

  function validateField(input, isValidFn) {
    const valid = isValidFn(input.value.trim());
    input.classList.toggle('is-invalid', !valid);
    input.classList.toggle('is-valid', valid);
    return valid;
  }

  const isNotEmpty = (val) => val.length > 0;
  const isValidEmail = (val) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val);

  /* =======================================================================
     17. CONTACT FORM (saved to the database via api/contact.php)
     ======================================================================= */

  const contactForm = document.getElementById('contactForm');
  const contactName = document.getElementById('contactName');
  const contactEmail = document.getElementById('contactEmail');
  const contactMessage = document.getElementById('contactMessage');

  contactForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    const nameValid = validateField(contactName, isNotEmpty);
    const emailValid = validateField(contactEmail, isValidEmail);
    const messageValid = validateField(contactMessage, (val) => val.length >= 10);

    if (!nameValid || !emailValid || !messageValid) {
      showContactAlert('Please fix the highlighted fields before sending your message.', 'danger');
      return;
    }

    const submitBtn = contactForm.querySelector('button[type="submit"]');
    submitBtn.disabled = true;

    const result = await apiCall('contact.php', {
      method: 'POST',
      body: {
        name: contactName.value.trim(),
        email: contactEmail.value.trim(),
        message: contactMessage.value.trim()
      }
    });

    submitBtn.disabled = false;

    if (result.success) {
      showContactAlert(result.message || 'Thanks! Your message has been received.', 'success');
      contactForm.reset();
      [contactName, contactEmail, contactMessage].forEach(el => el.classList.remove('is-valid', 'is-invalid'));
    } else {
      showContactAlert(result.error || 'Could not send your message. Please try again.', 'danger');
    }
  });

  [
    [contactName, isNotEmpty],
    [contactEmail, isValidEmail],
    [contactMessage, (val) => val.length >= 10]
  ].forEach(([el, validator]) => {
    el.addEventListener('input', () => {
      if (el.classList.contains('is-invalid') || el.classList.contains('is-valid')) {
        validateField(el, validator);
      }
    });
  });

  /* =======================================================================
     18. AUTHENTICATION — LOGIN / SIGN UP / LOGOUT (real backend)
     ======================================================================= */

  const loggedOutActions = document.getElementById('loggedOutActions');
  const loggedInActions = document.getElementById('loggedInActions');
  const userGreetingName = document.getElementById('userGreetingName');
  const logoutBtn = document.getElementById('logoutBtn');

  /** Updates the navbar and notes panel to reflect the current auth state. */
  function updateAuthUI() {
    if (currentUser) {
      loggedOutActions.classList.add('d-none');
      loggedInActions.classList.remove('d-none');
      userGreetingName.textContent = currentUser.name;
      notesLoginPrompt.classList.add('d-none');
      notesAppBody.classList.remove('d-none');
    } else {
      loggedOutActions.classList.remove('d-none');
      loggedInActions.classList.add('d-none');
      notesLoginPrompt.classList.remove('d-none');
      notesAppBody.classList.add('d-none');
    }
  }

  /** Checks the server session on page load and syncs the UI + notes. */
  async function refreshSession() {
    const result = await apiCall('session.php');
    currentUser = (result.success && result.loggedIn) ? result.user : null;
    updateAuthUI();
    if (currentUser) {
      await loadNotes();
    } else {
      notes = [];
      renderNotes(notes);
    }
  }

  function setupAuthForm(formId, fields, modalId, endpoint, onSuccess) {
    const form = document.getElementById(formId);
    if (!form) return;

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      let allValid = true;

      fields.forEach(({ id, validator }) => {
        const input = document.getElementById(id);
        const valid = validateField(input, validator);
        if (!valid) allValid = false;
      });

      if (!allValid) return;

      const submitBtn = form.querySelector('button[type="submit"]');
      submitBtn.disabled = true;

      const body = {};
      fields.forEach(({ id, key }) => {
        body[key] = document.getElementById(id).value.trim();
      });

      const result = await apiCall(endpoint, { method: 'POST', body });

      submitBtn.disabled = false;

      if (result.success) {
        const modalEl = document.getElementById(modalId);
        const modalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        modalInstance.hide();
        form.reset();
        fields.forEach(({ id }) => document.getElementById(id).classList.remove('is-valid', 'is-invalid'));
        onSuccess(result);
      } else {
        const errorField = document.getElementById(fields[0].id);
        errorField.classList.add('is-invalid');
        const feedbackEl = errorField.parentElement.querySelector('.invalid-feedback');
        if (feedbackEl) feedbackEl.textContent = result.error || 'Something went wrong.';
      }
    });
  }

  setupAuthForm('loginForm', [
    { id: 'loginEmail', key: 'email', validator: isValidEmail },
    { id: 'loginPassword', key: 'password', validator: (val) => val.length >= 6 }
  ], 'loginModal', 'login.php', async (result) => {
    currentUser = result.user;
    updateAuthUI();
    await loadNotes();
    showNoteAlert(`Welcome back, ${result.user.name}!`, 'success');
  });

  setupAuthForm('signupForm', [
    { id: 'signupName', key: 'name', validator: isNotEmpty },
    { id: 'signupEmail', key: 'email', validator: isValidEmail },
    { id: 'signupPassword', key: 'password', validator: (val) => val.length >= 6 }
  ], 'signupModal', 'register.php', async (result) => {
    currentUser = result.user;
    updateAuthUI();
    await loadNotes();
    showNoteAlert(`Welcome, ${result.user.name}! Your account has been created.`, 'success');
  });

  if (logoutBtn) {
    logoutBtn.addEventListener('click', async () => {
      await apiCall('logout.php', { method: 'POST' });
      currentUser = null;
      updateAuthUI();
      notes = [];
      renderNotes(notes);
      showNoteAlert('You have been logged out.', 'success');
    });
  }

  /* =======================================================================
     19. FOOTER YEAR
     ======================================================================= */

  const yearEl = document.getElementById('currentYear');
  if (yearEl) yearEl.textContent = new Date().getFullYear();

  /* =======================================================================
     20. GLOBAL ERROR SAFETY NET
     ======================================================================= */

  window.addEventListener('error', (event) => {
    console.error('Notes App: unexpected error caught.', event.error || event.message);
  });

  /* =======================================================================
     21. INITIAL LOAD — check session, then render notes/empty state
     ======================================================================= */

  refreshSession();

});
