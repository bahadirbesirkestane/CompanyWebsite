document.addEventListener('DOMContentLoaded', function () {

  // ---- çerez onay bandı ----
  // Sadece Özelleştir → Çerez & KVKK → "Çerez Onay Bandını Yayında Göster" AÇIKKEN
  // bu dosya enqueue ediliyor (bkz. functions.php), o yüzden burada ayrıca bir
  // "aktif mi" kontrolü yok — #cm-cerez-banner varsa özellik zaten açık demektir.
  // Tercih 180 gün süreyle bir çerezde (cm_cerez_onay) JSON olarak saklanır;
  // sayfa her yüklendiğinde bu çerez okunup Analitik onaylıysa dondurulmuş
  // (type="text/plain") GA/GTM script'leri gerçek <script>'e çevrilip çalıştırılır
  // (bkz. inc/seo.php → cm_seo_analytics_script_open_tag()).
  (function () {
    var banner = document.getElementById('cm-cerez-banner');
    if (!banner) return;

    var COOKIE_NAME = 'cm_cerez_onay';
    var mainView = banner.querySelector('.cerez-banner-inner');
    var settingsView = banner.querySelector('.cerez-settings');
    var analyticsCheckbox = banner.querySelector('[data-cerez-category="analytics"]');

    function getCookie(name) {
      var m = document.cookie.match('(^|;)\\s*' + name + '\\s*=\\s*([^;]+)');
      return m ? decodeURIComponent(m.pop()) : null;
    }
    function setCookie(name, value, days) {
      var d = new Date();
      d.setTime(d.getTime() + days * 24 * 60 * 60 * 1000);
      document.cookie = name + '=' + encodeURIComponent(value) + ';expires=' + d.toUTCString() + ';path=/;SameSite=Lax';
    }

    // Dondurulmuş (type="text/plain") analitik script'lerini gerçek <script>'e
    // çevirip DOM'a yeniden ekler — hem inline (GTM/gtag config) hem de
    // data-src'li (gtag.js yükleyici) script'ler için çalışır.
    function activateAnalyticsScripts() {
      document.querySelectorAll('script[type="text/plain"][data-cookie-category="analytics"]').forEach(function (old) {
        var s = document.createElement('script');
        var src = old.getAttribute('data-src');
        if (src) s.src = src;
        if (old.async) s.async = true;
        if (old.textContent) s.textContent = old.textContent;
        old.parentNode.replaceChild(s, old);
      });
    }

    function applyConsent(consent) {
      if (consent && consent.analytics) activateAnalyticsScripts();
    }

    function showBanner() { banner.hidden = false; }
    function hideBanner() { banner.hidden = true; }
    function showMainView() { mainView.hidden = false; settingsView.hidden = true; }
    function showSettingsView() { mainView.hidden = true; settingsView.hidden = false; }

    function saveConsent(analyticsAllowed) {
      var consent = { necessary: true, analytics: !!analyticsAllowed, ts: Date.now() };
      setCookie(COOKIE_NAME, JSON.stringify(consent), 180);
      applyConsent(consent);
      hideBanner();
    }

    var raw = getCookie(COOKIE_NAME);
    var savedConsent = null;
    if (raw) {
      try { savedConsent = JSON.parse(raw); } catch (e) { savedConsent = null; }
    }
    if (savedConsent) {
      applyConsent(savedConsent);
    } else {
      showBanner();
    }

    banner.addEventListener('click', function (e) {
      var btn = e.target.closest('[data-cerez-action]');
      if (!btn) return;
      var action = btn.getAttribute('data-cerez-action');
      if (action === 'tumu') saveConsent(true);
      else if (action === 'zorunlu') saveConsent(false);
      else if (action === 'ayarlar') showSettingsView();
      else if (action === 'geri') showMainView();
      else if (action === 'kaydet') saveConsent(analyticsCheckbox && analyticsCheckbox.checked);
    });

    // Footer'daki "Çerez Ayarları" bağlantısı (bkz. footer.php) — banner
    // kapatıldıktan sonra tercihi değiştirmek için her sayfadan erişilebilir.
    document.addEventListener('click', function (e) {
      var reopen = e.target.closest('[data-cerez-reopen]');
      if (!reopen) return;
      e.preventDefault();
      if (analyticsCheckbox) analyticsCheckbox.checked = !!(savedConsent && savedConsent.analytics);
      showSettingsView();
      showBanner();
    });
  })();

});
