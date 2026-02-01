$(function () {
  const $form = $('#car-search-form');

  $form.find('select, input').on('change', function () {
    if (this.name && this.name.includes('[brand]')) return;
    $form.trigger('submit');
  });

  $form.find('input[name*="[brand]"]').on('keydown', function (e) {
    if (e.key === 'Enter') {
      e.preventDefault();
      $form.trigger('submit');
    }
  });
});
