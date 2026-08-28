document.addEventListener('DOMContentLoaded', function () {

  // ---- sticky header: scroll'da küçülme + gölge ----
  (function () {
    var header = document.querySelector('.site-header');
    if (!header) return;
    var ticking = false;
    function update() {
      header.classList.toggle('is-scrolled', window.scrollY > 8);
      ticking = false;
    }
    window.addEventListener('scroll', function () {
      if (!ticking) { requestAnimationFrame(update); ticking = true; }
    }, { passive: true });
    update();
  })();

  // ---- scroll-reveal: .reveal sınıflı herhangi bir eleman görünür olunca .is-visible
  // ekler (main.css .reveal/.reveal.is-visible) — tüm sayfalarda tekrar kullanılabilir
  // tek bir gözlemci, her eleman bir kez göründükten sonra gözlemden çıkarılır. ----
  (function () {
    var els = document.querySelectorAll('.reveal');
    if (!els.length) return;
    var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (reduceMotion || !('IntersectionObserver' in window)) {
      els.forEach(function (el) { el.classList.add('is-visible'); });
      return;
    }
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        entry.target.classList.add('is-visible');
        io.unobserve(entry.target);
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
    els.forEach(function (el) { io.observe(el); });
  })();

  // ---- istatistik sayaç animasyonu: "27+", "40+", "500+" gibi rakamla başlayan
  // .stat .n etiketleri görünür olunca 0'dan hedefe sayarak dolar. "ISO 9001" gibi
  // rakamla BAŞLAMAYAN etiketler regex'e takılmadığı için olduğu gibi kalır. ----
  (function () {
    var nums = document.querySelectorAll('.stat .n');
    if (!nums.length || !('IntersectionObserver' in window)) return;
    var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function animate(el) {
      var match = el.textContent.trim().match(/^(\d+)(.*)$/);
      if (!match || reduceMotion) return;
      var target = parseInt(match[1], 10);
      var suffix = match[2];
      var duration = 1200;
      var start = null;
      function step(ts) {
        if (!start) start = ts;
        var progress = Math.min((ts - start) / duration, 1);
        var eased = 1 - Math.pow(1 - progress, 3);
        el.textContent = Math.round(eased * target) + suffix;
        if (progress < 1) requestAnimationFrame(step);
      }
      el.textContent = '0' + suffix;
      requestAnimationFrame(step);
    }

    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        animate(entry.target);
        io.unobserve(entry.target);
      });
    }, { threshold: 0.5 });
    nums.forEach(function (el) { io.observe(el); });
  })();

  // ---- mobil hamburger menü ----
  (function () {
    var headerInner = document.querySelector('.site-header-inner');
    var toggle = document.querySelector('.nav-toggle');
    var menu = document.querySelector('.nav-menu');
    if (!headerInner || !toggle || !menu) return;

    function setOpen(open) {
      headerInner.classList.toggle('nav-open', open);
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    }

    toggle.addEventListener('click', function () {
      setOpen(!headerInner.classList.contains('nav-open'));
    });
    menu.addEventListener('click', function (e) {
      if (e.target.closest('a')) setOpen(false);
    });
    document.addEventListener('click', function (e) {
      if (!headerInner.classList.contains('nav-open')) return;
      if (!headerInner.contains(e.target)) setOpen(false);
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') setOpen(false);
    });
    window.addEventListener('resize', function () {
      if (window.innerWidth > 900) setOpen(false);
    });
  })();

  // ---- header "Ürünler" alt kategori flyout'ları (masaüstü): sağda yer yoksa sola aç ----
  // Menünün/flyout'un GÖRÜNMESİ saf CSS ":hover"/"focus-within" ile yönetiliyor (main.css) —
  // bu sadece ekran kenarına yakın kategorilerde flyout'un hangi tarafa açılacağını (sağ/sol)
  // önceden hesaplayıp .flyout-left sınıfını ekliyor/kaldırıyor.
  (function () {
    var FLYOUT_WIDTH = 260; // main.css .megamenu-flyout genişliğiyle eşleşmeli
    // RTL'de (bkz. rtl.css) flyout'un VARSAYILAN yönü tersine döner (sağdan sola akan
    // menüde flyout normalde SOLA açılır; ".flyout-left" sınıfı RTL'de CSS tarafında
    // "SAĞA aç" anlamına gelecek şekilde yeniden kullanılır) — bu yüzden taşma kontrolü
    // de yön bazında dallanır: LTR'de sağ kenar, RTL'de sol kenar kontrol edilir.
    var isRTL = document.documentElement.dir === 'rtl';
    var items = document.querySelectorAll('.megamenu-item.has-children');
    items.forEach(function (item) {
      item.addEventListener('mouseenter', function () {
        var rect = item.getBoundingClientRect();
        var overflows = isRTL
          ? (rect.left - FLYOUT_WIDTH < 0)
          : (rect.right + FLYOUT_WIDTH > window.innerWidth);
        item.classList.toggle('flyout-left', overflows);
      });
    });
  })();

  // ---- header "Ürünler" alt kategorileri (mobil): her kategorinin kendi ok butonuyla
  // hemen altında aç/kapat (hover olmadığı için flyout burada akordeona dönüşür) ----
  (function () {
    document.querySelectorAll('.megamenu-item-toggle').forEach(function (btn) {
      var item = btn.closest('.megamenu-item');
      if (!item) return;
      btn.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var open = !item.classList.contains('open');
        item.classList.toggle('open', open);
        btn.setAttribute('aria-expanded', open ? 'true' : 'false');
      });
    });
  })();

  // ---- ürün listesi kategori sidebar'ı: alt kategorisi olan üst kategorinin ok
  // butonuyla alt listesini aç/kapat (satırın geri kalanı hâlâ kategori sayfasına
  // gider — bkz. cm_category_sidebar(), inc/template-tags.php) ----
  (function () {
    document.querySelectorAll('.cat-sidebar-toggle').forEach(function (btn) {
      var item = btn.closest('.cat-sidebar-item');
      if (!item) return;
      btn.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var open = !item.classList.contains('open');
        item.classList.toggle('open', open);
        btn.setAttribute('aria-expanded', open ? 'true' : 'false');
      });
    });
  })();

  // ---- header "Ürünler": mobilde ok butonuyla kategori/alt kategori listesini aç/kapat ----
  // (masaüstünde .megamenu CSS ":hover"/"focus-within" ile açılır, JS gerekmez)
  (function () {
    var toggle = document.querySelector('.megamenu-toggle');
    var li = toggle ? toggle.closest('.has-megamenu') : null;
    if (!toggle || !li) return;

    function setOpen(open) {
      li.classList.toggle('open', open);
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    }

    toggle.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      setOpen(!li.classList.contains('open'));
    });
    window.addEventListener('resize', function () {
      if (window.innerWidth > 900) setOpen(false);
    });
  })();

  // ---- dil seçici (bayraklı açılır liste) ----
  (function () {
    var dropdown = document.querySelector('.lang-dropdown');
    var toggle = dropdown && dropdown.querySelector('.lang-dropdown-toggle');
    if (!dropdown || !toggle) return;

    function setOpen(open) {
      dropdown.classList.toggle('open', open);
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    }

    toggle.addEventListener('click', function (e) {
      e.stopPropagation();
      setOpen(!dropdown.classList.contains('open'));
    });
    document.addEventListener('click', function (e) {
      if (dropdown.classList.contains('open') && !dropdown.contains(e.target)) setOpen(false);
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') setOpen(false);
    });
  })();

  // ---- anasayfa hero slider ----
  (function () {
    var slides = document.querySelectorAll('.hero-slide');
    var dots = document.querySelectorAll('.hero-dot');
    if (!slides.length) return;

    var i = 0, timer;
    var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function show(n) {
      i = (n + slides.length) % slides.length;
      slides.forEach(function (s, idx) { s.classList.toggle('active', idx === i); });
      // Her slaytın kendi dot grubu var (data-hero-dot ile eşleşir); dizideki sıraya göre değil.
      dots.forEach(function (d) { d.classList.toggle('active', parseInt(d.dataset.heroDot, 10) === i); });
    }
    function start() { if (reduceMotion || slides.length < 2) return; timer = setInterval(function () { show(i + 1); }, 5500); }
    function reset() { clearInterval(timer); start(); }

    var next = document.querySelector('.hero-arrow.next');
    var prev = document.querySelector('.hero-arrow.prev');
    if (next) next.addEventListener('click', function () { show(i + 1); reset(); });
    if (prev) prev.addEventListener('click', function () { show(i - 1); reset(); });
    dots.forEach(function (d) {
      var target = parseInt(d.dataset.heroDot, 10);
      d.addEventListener('click', function () { show(target); reset(); });
    });

    var viewport = document.querySelector('.hero-viewport');
    if (viewport) {
      viewport.addEventListener('mouseenter', function () { clearInterval(timer); });
      viewport.addEventListener('mouseleave', start);
    }
    start();
  })();

  // ---- referans şeridi: sonsuz kayma + elle sürükleme ----
  // Not: kasıtlı olarak "prefers-reduced-motion" kontrolü YOK — bu şerit her zaman
  // kendi kendine akmalı (istemci talebi).
  (function () {
    var wrap = document.querySelector('.marquee-wrap');
    var track = document.querySelector('.marquee-track');
    if (!wrap || !track) return;

    // Az sayıda referans olduğunda (içerik genişliği ekran genişliğine yakın/az) tarayıcının
    // doğal kaydırma sınırı bir "birim" genişliğe hiç ulaşamayabilir; bu da hem otomatik
    // kaymayı hem elle sürüklemeyi tamamen kilitler. Bunu önlemek için tek seti (birim)
    // ölçüp, kaydırılabilir alan görünür alanı rahatça aşana kadar JS ile kopyalıyoruz.
    var originalNodes = Array.prototype.slice.call(track.children);
    var unitWidth = 0;
    var half = 0;

    function measureUnit() {
      // Orijinal düğümler her zaman ilk unitCount kadarı; toplam genişliği o sayıya bölerek buluyoruz.
      var totalOriginal = 0;
      originalNodes.forEach(function (n) { totalOriginal += n.getBoundingClientRect().width; });
      var gap = parseFloat(getComputedStyle(track).columnGap || getComputedStyle(track).gap || 0) || 0;
      return totalOriginal + gap * originalNodes.length;
    }

    function ensureEnoughWidth() {
      unitWidth = measureUnit();
      if (unitWidth <= 0) return;
      var guard = 0;
      while (track.scrollWidth < wrap.clientWidth + unitWidth * 2 && guard < 30) {
        originalNodes.forEach(function (n) { track.appendChild(n.cloneNode(true)); });
        guard++;
      }
      half = unitWidth;
    }

    ensureEnoughWidth();
    window.addEventListener('load', ensureEnoughWidth);
    window.addEventListener('resize', ensureEnoughWidth);

    var isDown = false, isHover = false, startX = 0, startScroll = 0, speed = 0.7;

    function wrapScroll() {
      if (half <= 0) return;
      if (wrap.scrollLeft >= half) wrap.scrollLeft -= half;
      else if (wrap.scrollLeft < 0) wrap.scrollLeft += half;
    }

    setInterval(function () {
      if (isDown || isHover) return;
      wrap.scrollLeft += speed;
      wrapScroll();
    }, 30);

    wrap.addEventListener('mouseenter', function () { isHover = true; });
    wrap.addEventListener('mouseleave', function () { isHover = false; });

    wrap.addEventListener('pointerdown', function (e) {
      isDown = true;
      wrap.classList.add('dragging');
      startX = e.clientX;
      startScroll = wrap.scrollLeft;
      try { wrap.setPointerCapture(e.pointerId); } catch (err) {}
      e.preventDefault(); // metin seçimi / native sürükleme başlatılmasını engeller
    });
    wrap.addEventListener('pointermove', function (e) {
      if (!isDown) return;
      wrap.scrollLeft = startScroll - (e.clientX - startX);
      wrapScroll();
    });
    function endDrag() { isDown = false; wrap.classList.remove('dragging'); }
    wrap.addEventListener('pointerup', endDrag);
    wrap.addEventListener('pointercancel', endDrag);
    wrap.addEventListener('pointerleave', function () { endDrag(); isHover = false; });

    wrap.scrollLeft = 1;
  })();

  // ---- makine detay sayfası galerisi: küçük görsel + ileri/geri ok ----
  (function () {
    var main = document.getElementById('cmGalleryMain');
    var thumbs = Array.prototype.slice.call(document.querySelectorAll('[data-cm-thumb]'));
    if (!main || !thumbs.length) return;

    var urls = thumbs.map(function (t) { return t.getAttribute('data-cm-thumb'); });
    var index = 0;

    function show(n) {
      index = (n + urls.length) % urls.length;
      var img = main.querySelector('img');
      if (!img) {
        img = document.createElement('img');
        main.insertBefore(img, main.firstChild);
      }
      img.src = urls[index];
      thumbs.forEach(function (t, i) { t.classList.toggle('sel', i === index); });
    }

    thumbs.forEach(function (thumb, i) {
      thumb.addEventListener('click', function () { show(i); });
    });

    var prev = main.querySelector('.gallery-arrow.prev');
    var next = main.querySelector('.gallery-arrow.next');
    if (prev) prev.addEventListener('click', function () { show(index - 1); });
    if (next) next.addEventListener('click', function () { show(index + 1); });
  })();

  // ---- makine detay sayfası sekmeler (açıklama/özellikler/video/dokümanlar) ----
  (function () {
    var tabs = document.querySelectorAll('.tab-strip [data-cm-tab]');
    var panels = document.querySelectorAll('.tab-panel[data-cm-panel]');
    if (!tabs.length || !panels.length) return;

    tabs.forEach(function (tab) {
      tab.addEventListener('click', function () {
        var key = tab.getAttribute('data-cm-tab');
        tabs.forEach(function (t) { t.classList.remove('active'); t.setAttribute('aria-selected', 'false'); });
        panels.forEach(function (p) { p.classList.remove('active'); });
        tab.classList.add('active');
        tab.setAttribute('aria-selected', 'true');
        var panel = document.querySelector('.tab-panel[data-cm-panel="' + key + '"]');
        if (panel) panel.classList.add('active');
      });
    });
  })();

  // ---- ürün listesi: 2/3 sütun görünüm seçici (bkz. cm_grid_toggle(), inc/template-tags.php) ----
  (function () {
    var buttons = document.querySelectorAll('.grid-toggle-btn');
    if (!buttons.length) return;
    var KEY = 'cmProductGridCols';

    function apply(cols) {
      document.querySelectorAll('.category-main-col').forEach(function (col) {
        col.classList.toggle('grid-3', cols === '3');
      });
      buttons.forEach(function (btn) {
        var active = btn.getAttribute('data-cols') === cols;
        btn.classList.toggle('active', active);
        btn.setAttribute('aria-pressed', active ? 'true' : 'false');
      });
    }

    var saved = null;
    try { saved = localStorage.getItem(KEY); } catch (err) {}
    if (saved === '3') apply('3');

    buttons.forEach(function (btn) {
      btn.addEventListener('click', function () {
        var cols = btn.getAttribute('data-cols');
        apply(cols);
        try { localStorage.setItem(KEY, cols); } catch (err) {}
      });
    });
  })();

});
