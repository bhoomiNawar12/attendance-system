/**
 * EduAttend — shared UI (navigation, mobile sidebar)
 */
(function () {
  'use strict';

  function initMobileSidebar() {
    var sidebar = document.getElementById('app-sidebar');
    if (!sidebar) return;

    var backdrop = document.getElementById('sidebar-backdrop');
    if (!backdrop) {
      backdrop = document.createElement('div');
      backdrop.id = 'sidebar-backdrop';
      backdrop.setAttribute('aria-hidden', 'true');
      document.body.appendChild(backdrop);
    }

    var menuBtn = document.getElementById('mobile-menu-btn');
    if (!menuBtn) {
      menuBtn = document.createElement('button');
      menuBtn.id = 'mobile-menu-btn';
      menuBtn.type = 'button';
      menuBtn.className =
        'lg:hidden fixed top-3 left-3 z-[60] p-2 rounded-lg bg-primary text-white shadow-md';
      menuBtn.setAttribute('aria-label', 'Open menu');
      menuBtn.innerHTML =
        '<span class="material-symbols-outlined">menu</span>';
      document.body.appendChild(menuBtn);
    }

    function closeSidebar() {
      sidebar.classList.remove('is-open');
      backdrop.classList.remove('is-open');
      document.body.style.overflow = '';
    }

    function openSidebar() {
      sidebar.classList.add('is-open');
      backdrop.classList.add('is-open');
      document.body.style.overflow = 'hidden';
    }

    menuBtn.addEventListener('click', function () {
      if (sidebar.classList.contains('is-open')) {
        closeSidebar();
      } else {
        openSidebar();
      }
    });

    backdrop.addEventListener('click', closeSidebar);

    sidebar.querySelectorAll('nav a').forEach(function (link) {
      link.addEventListener('click', function () {
        if (window.innerWidth < 1024) closeSidebar();
      });
    });

    window.addEventListener('resize', function () {
      if (window.innerWidth >= 1024) closeSidebar();
    });
  }

  function markActiveNav() {
    var path = window.location.pathname.split('/').pop() || 'index.html';
    document.querySelectorAll('nav a[href]').forEach(function (link) {
      var href = link.getAttribute('href');
      if (!href || href === '#') return;
      var target = href.split('/').pop();
      if (target === path) {
        link.setAttribute('aria-current', 'page');
      }
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    initMobileSidebar();
    markActiveNav();
    console.log('EduAttend UI loaded');
  });
})();
