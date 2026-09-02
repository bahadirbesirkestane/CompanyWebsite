jQuery(function ($) {
  var $list = $('#cm-cat-order-list');
  if (!$list.length) return;

  var $status = $('#cm-cat-order-status');
  var saveTimer = null;

  $list.sortable({
    handle: '.cm-cat-order-handle',
    axis: 'y',
    update: function () {
      var ids = $list.find('li').map(function () {
        return $(this).data('id');
      }).get();

      clearTimeout(saveTimer);
      $status.text('Kaydediliyor…');
      saveTimer = setTimeout(function () {
        $.post(cmCategoryOrder.ajaxUrl, {
          action: 'cm_save_category_order',
          nonce: cmCategoryOrder.nonce,
          order: ids
        }).done(function (res) {
          $status.text(res && res.success ? 'Kaydedildi.' : 'Bir hata oluştu, tekrar deneyin.');
        }).fail(function () {
          $status.text('Bir hata oluştu, tekrar deneyin.');
        });
      }, 300);
    }
  });
});
