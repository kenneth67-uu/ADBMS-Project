/* ============================================================
   DonateHub / UMDC — script.js  (Refactored)
   Pure front-end only. No backend. No saving.

   BUTTON CLASSES (no inline onclick needed):
     .signup-btn   → opens Sign Up modal  (index.html)
     .donate-btn   → donate flow (all pages)
     .logout-btn   → log out (dynamically added)
     .pw-toggle-btn → password show/hide
     .quick-btn    → preset donation amounts
     .type-btn     → money / goods toggle

   All modals are closed by:
     • clicking the dark backdrop
     • pressing Escape
     • clicking any .modal-close-x button
   ============================================================ */

/* ── Session state ────────────────────────────────────────── */
var userSignedUp = false;
var userName     = '';

/* ============================================================
   INIT — runs once the DOM is ready
   ============================================================ */
document.addEventListener('DOMContentLoaded', function () {

  /* Page fade-in */
  document.body.style.opacity    = '0';
  document.body.style.transition = 'opacity 0.28s ease';
  setTimeout(function () { document.body.style.opacity = '1'; }, 20);

  initHamburger();
  initButtonDelegation();
  readCampaignStatus();
});

/* ── Navbar shadow on scroll ───────────────────────────────── */
window.addEventListener('scroll', function () {
  var nav = document.querySelector('.navbar');
  if (!nav) return;
  nav.style.boxShadow = window.scrollY > 8
    ? '0 4px 20px rgba(0,0,0,0.14)'
    : '0 1px 8px rgba(0,0,0,0.09)';
});

/* ── Hamburger menu ────────────────────────────────────────── */
function initHamburger() {
  var btn   = document.getElementById('hamburger');
  var links = document.getElementById('navLinks');
  if (!btn || !links) return;

  btn.addEventListener('click', function () {
    links.classList.toggle('open');
    btn.classList.toggle('open');
  });

  links.querySelectorAll('a').forEach(function (a) {
    a.addEventListener('click', function () {
      links.classList.remove('open');
      btn.classList.remove('open');
    });
  });
}

/* ============================================================
   BUTTON DELEGATION
   Uses event delegation on document so buttons added later
   (e.g. logout-btn) are automatically handled.
   ============================================================ */
function initButtonDelegation() {

  document.addEventListener('click', function (e) {
    var t = e.target;

    /* ── Backdrop click — close any modal ── */
    if (t.classList.contains('modal-bg')) {
      t.classList.remove('show');
      return;
    }

    /* ── Close-X button inside modals ── */
    if (t.classList.contains('modal-close-x')) {
      closeAllModals();
      return;
    }

    /* ── Password toggle (modal + PHP pages) ── */
    if (t.classList.contains('modal-pw-toggle') || t.classList.contains('pw-toggle')) {
      var wrap = t.closest('.modal-pw-wrap') || t.closest('.pw-wrap');
      if (wrap) {
        var inp = wrap.querySelector('input');
        if (inp) {
          inp.type       = (inp.type === 'password') ? 'text' : 'password';
          t.textContent  = (inp.type === 'password') ? 'Show' : 'Hide';
        }
      }
      return;
    }

    /* ── Sign-up buttons (.signup-btn) ── */
    if (t.classList.contains('signup-btn')) {
      resetSignupModal();
      openModal('signupModal');
      return;
    }

    /* ── Donate buttons (.donate-btn) ── */
    if (t.classList.contains('donate-btn')) {
      handleDonate();
      return;
    }

    /* ── Log out button (.logout-btn, added dynamically) ── */
    if (t.classList.contains('logout-btn')) {
      doLogout();
      return;
    }

    /* ── "Already signed up? Go to Donate" link-button ── */
    if (t.classList.contains('modal-link-btn')) {
      closeAllModals();
      openDonateModal();
      return;
    }

    /* ── Quick-amount buttons (.quick-btn) ── */
    if (t.classList.contains('quick-btn')) {
      var val = parseInt(t.dataset.val, 10);
      pickModalAmount(val, t);
      return;
    }

    /* ── Donation type buttons (.type-btn) ── */
    if (t.classList.contains('type-btn')) {
      var type = t.dataset.type;
      setModalType(type, t);
      return;
    }

    /* ── "Not Now" / cancel modal button (.btn-modal-no) ── */
    if (t.classList.contains('btn-modal-no')) {
      closeAllModals();
      return;
    }
  });

  /* Escape key */
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeAllModals();
  });

  /* Clear red border on input */
  document.addEventListener('input', function (e) {
    if (e.target && e.target.style) {
      e.target.style.borderColor = '';
    }
  });

  /* Sign-up form submit */
  var signupForm = document.getElementById('signupForm');
  if (signupForm) {
    signupForm.addEventListener('submit', submitSignup);
  }

  /* Donate form submit */
  var donateForm = document.getElementById('donateForm');
  if (donateForm) {
    donateForm.addEventListener('submit', submitDonate);
  }

  /* Campaign form submit (campaign.html) */
  var campaignForm = document.getElementById('campaignForm');
  if (campaignForm) {
    campaignForm.addEventListener('submit', checkCampaignForm);
  }

  /* Campaign cancel button */
  var cancelBtn = document.querySelector('.btn-cancel[data-action="cancel-campaign"]');
  if (cancelBtn) {
    cancelBtn.addEventListener('click', cancelCampaign);
  }
}

/* ============================================================
   OPEN / CLOSE MODALS
   ============================================================ */
function openModal(id) {
  var m = document.getElementById(id);
  if (!m) return;
  m.classList.add('show');
  var first = m.querySelector('input');
  if (first) setTimeout(function () { first.focus(); }, 60);
}

function closeAllModals() {
  document.querySelectorAll('.modal-bg').forEach(function (m) {
    m.classList.remove('show');
  });
}

/* Legacy helper used by about/projects/goal/campaign pages */
function closeModal() { closeAllModals(); }

/* ============================================================
   SIGN UP FLOW
   ============================================================ */
function resetSignupModal() {
  var form = document.getElementById('signupForm');
  var err  = document.getElementById('signupError');
  if (form) form.reset();
  if (err)  { err.style.display = 'none'; err.textContent = ''; }
}

function submitSignup(e) {
  e.preventDefault();

  var nameEl  = document.getElementById('su_name');
  var emailEl = document.getElementById('su_email');
  var passEl  = document.getElementById('su_password');
  var errEl   = document.getElementById('signupError');

  var name     = nameEl  ? nameEl.value.trim()  : '';
  var email    = emailEl ? emailEl.value.trim()  : '';
  var password = passEl  ? passEl.value          : '';

  if (!name || !email || !password) {
    showModalError(errEl, 'Please fill in all fields.');
    return;
  }
  if (!email.includes('@')) {
    showModalError(errEl, 'Please enter a valid email address.');
    return;
  }
  if (password.length < 6) {
    showModalError(errEl, 'Password must be at least 6 characters.');
    return;
  }

  userSignedUp = true;
  userName     = name;
  updateNavAfterSignup(name);

  closeAllModals();
  openDonateModal();
}

/* Update navbar username + swap SIGN UP → Log Out */
function updateNavAfterSignup(name) {
  var usernameEl = document.getElementById('navUsername');
  var signinBtn  = document.getElementById('navSignin');

  if (usernameEl) {
    usernameEl.textContent = 'Hi, ' + name;
    usernameEl.style.display = 'inline';
  }
  if (signinBtn) {
    signinBtn.textContent = 'Log Out';
    /* Remove old classes, add logout class for delegation */
    signinBtn.className = signinBtn.className
      .replace('btn-nav-outline', '')
      .replace('signup-btn', '')
      .trim();
    signinBtn.classList.add('logout-btn', 'btn-nav-outline');
    /* Remove any inline onclick that might exist */
    signinBtn.removeAttribute('onclick');
  }
}

function doLogout() {
  userSignedUp = false;
  userName     = '';

  var usernameEl = document.getElementById('navUsername');
  var signinBtn  = document.getElementById('navSignin');

  if (usernameEl) { usernameEl.style.display = 'none'; usernameEl.textContent = ''; }
  if (signinBtn)  {
    signinBtn.textContent = 'SIGN UP';
    signinBtn.classList.remove('logout-btn');
    signinBtn.classList.add('signup-btn', 'btn-nav-outline');
  }
}

/* ============================================================
   DONATE FLOW
   ============================================================ */
function handleDonate() {
  if (userSignedUp) {
    openDonateModal();
  } else {
    /* Show the sign-up modal — index.html has it,
       other pages have loginModal as fallback          */
    var signupModal = document.getElementById('signupModal');
    var loginModal  = document.getElementById('loginModal');

    if (signupModal) {
      resetSignupModal();
      openModal('signupModal');
    } else if (loginModal) {
      openModal('loginModal');
    }
  }
}

function openDonateModal() {
  var form      = document.getElementById('donateForm');
  var successEl = document.getElementById('donateSuccess');
  var errEl     = document.getElementById('donateError');
  var greeting  = document.getElementById('donateGreeting');

  if (form)      { form.reset(); form.style.display = 'block'; }
  if (successEl) { successEl.style.display = 'none'; successEl.textContent = ''; }
  if (errEl)     { errEl.style.display     = 'none'; errEl.textContent     = ''; }

  /* Reset type → Money */
  var firstTypeBtn = document.querySelector('#donateModal .type-btn');
  if (firstTypeBtn) setModalType('money', firstTypeBtn);

  /* Clear quick-amount highlights */
  document.querySelectorAll('#donateModal .quick-btn').forEach(function (b) {
    b.classList.remove('picked');
  });

  if (greeting) {
    greeting.textContent = userName
      ? 'Hi, ' + userName + '! Your generosity makes a real difference.'
      : 'Your generosity makes a real difference.';
  }

  openModal('donateModal');
}

function submitDonate(e) {
  e.preventDefault();

  var amountEl  = document.getElementById('d_amount');
  var typeEl    = document.getElementById('d_type');
  var goodsEl   = document.getElementById('d_goods');
  var successEl = document.getElementById('donateSuccess');
  var errEl     = document.getElementById('donateError');
  var form      = document.getElementById('donateForm');

  var amount = amountEl ? amountEl.value.trim() : '';
  var type   = typeEl   ? typeEl.value           : 'money';
  var goods  = goodsEl  ? goodsEl.value.trim()   : '';

  if (errEl) errEl.style.display = 'none';

  if (type === 'money' && (!amount || isNaN(amount) || Number(amount) <= 0)) {
    showModalError(errEl, 'Please enter a valid donation amount.');
    return;
  }
  if (type === 'goods' && !goods) {
    showModalError(errEl, 'Please describe the goods you are donating.');
    return;
  }

  var msg = type === 'money'
    ? 'Thank you for your ₱' + Number(amount).toLocaleString() + ' donation! You are making a real difference in Batangas. 💚'
    : 'Thank you for donating goods! Your contribution helps Batangueños thrive. 💚';

  if (form)      form.style.display      = 'none';
  if (successEl) { successEl.textContent = msg; successEl.style.display = 'block'; }
}

/* ── Donation type toggle ──────────────────────────────────── */
function setModalType(type, btn) {
  var hidden     = document.getElementById('d_type');
  var goodsField = document.getElementById('goodsField');

  if (hidden) hidden.value = type;

  document.querySelectorAll('#donateModal .type-btn').forEach(function (b) {
    b.classList.remove('active');
  });
  if (btn) btn.classList.add('active');

  if (goodsField) {
    goodsField.style.display = (type === 'goods') ? 'block' : 'none';
  }
}

/* ── Quick-amount button ───────────────────────────────────── */
function pickModalAmount(val, btn) {
  var inp = document.getElementById('d_amount');
  if (inp) inp.value = val;

  document.querySelectorAll('#donateModal .quick-btn').forEach(function (b) {
    b.classList.remove('picked');
  });
  if (btn) btn.classList.add('picked');
}

/* ============================================================
   UTILITY
   ============================================================ */
function showModalError(box, msg) {
  if (!box) return;
  box.textContent   = msg;
  box.style.display = 'block';
}

/* Password toggle for standalone PHP pages (login.php etc.) */
function togglePw(inputId) {
  var inp = document.getElementById(inputId);
  if (!inp) return;
  var btn = inp.parentElement.querySelector('.pw-toggle');
  inp.type = (inp.type === 'password') ? 'text' : 'password';
  if (btn) btn.textContent = (inp.type === 'password') ? 'Show' : 'Hide';
}

/* ============================================================
   CAMPAIGN PAGE
   ============================================================ */
function checkCampaignForm(e) {
  var form = document.getElementById('campaignForm');
  var ok   = validateForm(form);
  if (!ok) {
    e.preventDefault();
    var box = document.getElementById('formAlert');
    if (box) {
      box.innerHTML = '<div class="alert alert-error">Please fill in all required fields.</div>';
      box.scrollIntoView({ behavior: 'smooth' });
    }
  }
  return ok;
}

function cancelCampaign() {
  if (confirm('Cancel? Your inputs will be cleared.')) {
    var f = document.getElementById('campaignForm');
    if (f) f.reset();
    var a = document.getElementById('formAlert');
    if (a) a.innerHTML = '';
  }
}

function readCampaignStatus() {
  var alertBox = document.getElementById('formAlert');
  if (!alertBox) return;

  var params = new URLSearchParams(window.location.search);
  var status = params.get('status');

  if (status === 'success') {
    alertBox.innerHTML = '<div class="alert alert-success">Your campaign has been submitted for review! We will get back to you soon.</div>';
    alertBox.scrollIntoView({ behavior: 'smooth' });
    history.replaceState(null, '', window.location.pathname);
  } else if (status === 'error') {
    alertBox.innerHTML = '<div class="alert alert-error">Please fill in all required fields correctly.</div>';
    alertBox.scrollIntoView({ behavior: 'smooth' });
    history.replaceState(null, '', window.location.pathname);
  }
}

function validateForm(formEl) {
  var ok = true;
  formEl.querySelectorAll('[required]').forEach(function (el) {
    el.style.borderColor = '';
    if (!el.value.trim()) {
      el.style.borderColor = '#e74c3c';
      ok = false;
    }
  });
  return ok;
}

/* ── Donate-type toggle for donate.php ────────────────────── */
function setDonateType(type, btn) {
  var hidden = document.getElementById('donationType');
  if (hidden) hidden.value = type;
  document.querySelectorAll('.type-btn').forEach(function (b) { b.classList.remove('active'); });
  if (btn) btn.classList.add('active');
  var mf = document.getElementById('moneyFields');
  var gf = document.getElementById('goodsFields');
  if (mf) mf.style.display = (type === 'money') ? 'block' : 'none';
  if (gf) gf.style.display = (type === 'goods')  ? 'block' : 'none';
}

function pickAmount(val) {
  var inp = document.getElementById('amountInput');
  if (inp) inp.value = val;
  document.querySelectorAll('.quick-btn').forEach(function (b) {
    b.classList.toggle('picked', parseInt(b.dataset.val, 10) === val);
  });
}