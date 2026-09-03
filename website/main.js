/* ==========================================================================
   NEXUS LEARNING — MAIN JAVASCRIPT
   Vanilla ES6 only. No frameworks, no PHP, no backend calls.
   ========================================================================== */

document.addEventListener('DOMContentLoaded', () => {

  /* ------------------------------------------------------------------------
     0. CONFIG — update this with the real WhatsApp business number
     Format: country code + number, no +, no spaces (e.g. 919876543210)
     ------------------------------------------------------------------------ */
  const WHATSAPP_NUMBER = '919846544828';

  /* ------------------------------------------------------------------------
     1. AOS — Animate On Scroll
     ------------------------------------------------------------------------ */
  if (window.AOS) {
    AOS.init({
      duration: 700,
      easing: 'ease-out-cubic',
      once: true,
      offset: 60,
    });
  }

  /* ------------------------------------------------------------------------
     2. STICKY NAVBAR + SCROLLSPY
     ------------------------------------------------------------------------ */
  const navbar = document.getElementById('mainNavbar');
  const navLinks = document.querySelectorAll('.nav-link-custom');
  const sections = document.querySelectorAll('main section[id]');

  const handleScroll = () => {
    if (window.scrollY > 20) {
      navbar.classList.add('is-scrolled');
    } else {
      navbar.classList.remove('is-scrolled');
    }

    // Back to top button visibility
    const backToTop = document.querySelector('.back-to-top');
    if (backToTop) {
      backToTop.classList.toggle('is-visible', window.scrollY > 500);
    }

    // Scrollspy: highlight active nav link
    let currentId = '';
    sections.forEach((section) => {
      const top = section.offsetTop - 140;
      if (window.scrollY >= top) {
        currentId = section.getAttribute('id');
      }
    });

    navLinks.forEach((link) => {
      link.classList.remove('active');
      if (link.getAttribute('href') === `#${currentId}`) {
        link.classList.add('active');
      }
    });
  };

  window.addEventListener('scroll', handleScroll, { passive: true });
  handleScroll();

  // Collapse mobile menu after clicking a link
  const mobileMenu = document.getElementById('navMain');
  navLinks.forEach((link) => {
    link.addEventListener('click', () => {
      if (mobileMenu && mobileMenu.classList.contains('show')) {
        const bsCollapse = bootstrap.Collapse.getOrCreateInstance(mobileMenu);
        bsCollapse.hide();
      }
    });
  });

  /* ------------------------------------------------------------------------
     3. TYPED.JS — Hero rotating text
     ------------------------------------------------------------------------ */
  const typedEl = document.getElementById('typed-text');
  if (typedEl && window.Typed) {
    new Typed('#typed-text', {
      strings: [
        'School Tuition',
        'Online Classes',
        'Competitive Exams',
        'Programming',
        'Languages',
        'Skill Development',
      ],
      typeSpeed: 55,
      backSpeed: 30,
      backDelay: 1500,
      startDelay: 400,
      loop: true,
      smartBackspace: true,
    });
  }

  /* ------------------------------------------------------------------------
     4. GSAP — Hero entrance animation
     ------------------------------------------------------------------------ */
  if (window.gsap) {
    gsap.from('.hero-eyebrow', { y: 20, opacity: 0, duration: 0.7, ease: 'power3.out', delay: 0.1 });
    gsap.from('.hero-title-line', { y: 30, opacity: 0, duration: 0.8, ease: 'power3.out', delay: 0.25, stagger: 0.08 });
    gsap.from('.hero .typed-wrap', { y: 20, opacity: 0, duration: 0.7, ease: 'power3.out', delay: 0.5 });
    gsap.from('.hero p.lead', { y: 20, opacity: 0, duration: 0.7, ease: 'power3.out', delay: 0.6 });
    gsap.from('.hero-btns .btn', { y: 20, opacity: 0, duration: 0.6, ease: 'power3.out', delay: 0.7, stagger: 0.1 });
    gsap.from('.hero-trust', { opacity: 0, duration: 0.8, delay: 0.9 });
    gsap.from('.hero-visual', { x: 40, opacity: 0, duration: 0.9, ease: 'power3.out', delay: 0.35 });
    gsap.from('.hero-stat-card', { y: 20, opacity: 0, duration: 0.7, ease: 'power3.out', delay: 1.1 });
    gsap.to('.floating-icon', {
      opacity: 1,
      duration: 0.6,
      delay: 1,
      stagger: 0.15,
    });
  }

  /* ------------------------------------------------------------------------
     5. ANIMATED COUNTERS (Statistics band)
     ------------------------------------------------------------------------ */
  const counters = document.querySelectorAll('[data-counter]');

  const animateCounter = (el) => {
    const target = parseFloat(el.getAttribute('data-counter'));
    const suffix = el.getAttribute('data-suffix') || '';
    const duration = 1800;
    const startTime = performance.now();

    const tick = (now) => {
      const progress = Math.min((now - startTime) / duration, 1);
      const eased = 1 - Math.pow(1 - progress, 3);
      const value = Math.floor(eased * target);
      el.textContent = value + suffix;
      if (progress < 1) {
        requestAnimationFrame(tick);
      } else {
        el.textContent = target + suffix;
      }
    };
    requestAnimationFrame(tick);
  };

  const counterObserver = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        animateCounter(entry.target);
        counterObserver.unobserve(entry.target);
      }
    });
  }, { threshold: 0.5 });

  counters.forEach((counter) => counterObserver.observe(counter));

  /* ------------------------------------------------------------------------
     6. SWIPER — Testimonials slider
     ------------------------------------------------------------------------ */
  if (window.Swiper) {
    new Swiper('.testimonials-swiper', {
      slidesPerView: 1,
      spaceBetween: 24,
      loop: true,
      autoplay: { delay: 4500, disableOnInteraction: false },
      pagination: { el: '.testimonials-swiper .swiper-pagination', clickable: true },
      breakpoints: {
        768: { slidesPerView: 2 },
        1200: { slidesPerView: 3 },
      },
    });
  }

  /* ------------------------------------------------------------------------
     7. COURSE CATEGORY FILTER TABS
     ------------------------------------------------------------------------ */
  const courseTabs = document.querySelectorAll('.course-tab');
  const courseCards = document.querySelectorAll('.course-card[data-cat]');

  const filterCourses = (filter) => {
    courseCards.forEach((card) => {
      const cats = card.getAttribute('data-cat').split(' ');
      const show = filter === 'all' || cats.includes(filter);
      card.classList.toggle('is-visible', show);
    });
  };

  courseTabs.forEach((tab) => {
    tab.addEventListener('click', () => {
      courseTabs.forEach((t) => t.classList.remove('active'));
      tab.classList.add('active');
      filterCourses(tab.getAttribute('data-filter'));
    });
  });

  // Initialize with "all" visible
  filterCourses('all');

  /* ------------------------------------------------------------------------
     8. FAQ ACCORDION
     ------------------------------------------------------------------------ */
  const faqItems = document.querySelectorAll('.faq-item');

  faqItems.forEach((item) => {
    const question = item.querySelector('.faq-question');
    const answer = item.querySelector('.faq-answer');

    question.addEventListener('click', () => {
      const isActive = item.classList.contains('active');

      faqItems.forEach((otherItem) => {
        otherItem.classList.remove('active');
        otherItem.querySelector('.faq-answer').style.maxHeight = null;
        otherItem.querySelector('.faq-question').setAttribute('aria-expanded', 'false');
      });

      if (!isActive) {
        item.classList.add('active');
        answer.style.maxHeight = answer.scrollHeight + 'px';
        question.setAttribute('aria-expanded', 'true');
      }
    });
  });

  /* ------------------------------------------------------------------------
     9. REQUEST CLASS FORM — Send via WhatsApp
     ------------------------------------------------------------------------ */
  const requestForm = document.getElementById('requestClassForm');

  if (requestForm) {
    requestForm.addEventListener('submit', (e) => {
      e.preventDefault();

      if (!requestForm.checkValidity()) {
        requestForm.classList.add('was-validated');
        return;
      }

      const getVal = (id) => {
        const field = document.getElementById(id);
        return field ? field.value.trim() : '';
      };

      const modeField = requestForm.querySelector('input[name="mode"]:checked');

      const data = {
        studentName: getVal('studentName'),
        parentName: getVal('parentName'),
        phone: getVal('phone'),
        whatsapp: getVal('whatsappNumber'),
        email: getVal('email'),
        location: getVal('location'),
        mode: modeField ? modeField.value : '',
        classLevel: getVal('classLevel'),
        subject: getVal('subject'),
        courseName: getVal('courseName'),
        timing: getVal('timing'),
        message: getVal('message'),
      };

      const messageLines = [
        'Hello Nexus Learning,',
        '',
        'I would like to request a class.',
        '',
        `Student Name: ${data.studentName}`,
        `Parent Name: ${data.parentName}`,
        `Phone: ${data.phone}`,
        `WhatsApp: ${data.whatsapp}`,
        `Email: ${data.email}`,
        `Location: ${data.location}`,
        `Mode: ${data.mode}`,
        `Class: ${data.classLevel}`,
        `Subject: ${data.subject}`,
        `Course: ${data.courseName}`,
        `Preferred Time: ${data.timing}`,
        `Message: ${data.message}`,
        '',
        'I would like to know more about teacher availability.',
      ].join('\n');

      const encodedMessage = encodeURIComponent(messageLines);
      const whatsappUrl = `https://wa.me/${WHATSAPP_NUMBER}?text=${encodedMessage}`;

      window.open(whatsappUrl, '_blank', 'noopener,noreferrer');

      // Reset form after successful send
      requestForm.reset();
      requestForm.classList.remove('was-validated');

      const successAlert = document.getElementById('formSuccessAlert');
      if (successAlert) {
        successAlert.classList.remove('d-none');
        setTimeout(() => successAlert.classList.add('d-none'), 6000);
      }
    });
  }

  /* ------------------------------------------------------------------------
     10. FLOATING WHATSAPP BUTTON — dynamic quick-chat link
     ------------------------------------------------------------------------ */
  const whatsappFloat = document.getElementById('whatsappFloatBtn');
  if (whatsappFloat) {
    const quickMessage = encodeURIComponent('Hello Nexus Learning, I would like to know more about your classes.');
    whatsappFloat.setAttribute('href', `https://wa.me/${WHATSAPP_NUMBER}?text=${quickMessage}`);
  }

  /* ------------------------------------------------------------------------
     11. BACK TO TOP BUTTON
     ------------------------------------------------------------------------ */
  const backToTopBtn = document.querySelector('.back-to-top');
  if (backToTopBtn) {
    backToTopBtn.addEventListener('click', (e) => {
      e.preventDefault();
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  /* ------------------------------------------------------------------------
     12. GRADIENT ANIMATION on primary buttons (subtle continuous shift)
     ------------------------------------------------------------------------ */
  const gradientButtons = document.querySelectorAll('.btn-primary');
  gradientButtons.forEach((btn) => {
    btn.style.backgroundSize = '200% auto';
  });

  /* ------------------------------------------------------------------------
     13. CURRENT YEAR IN FOOTER
     ------------------------------------------------------------------------ */
  const yearEls = document.querySelectorAll('[data-current-year]');
  yearEls.forEach((el) => { el.textContent = new Date().getFullYear(); });

});
