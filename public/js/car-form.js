(function ($) {
    'use strict';

    async function refreshPtraField($typeSelect, $container) {
        const url = $container.data('ptra-url');
        const type = encodeURIComponent($typeSelect.val() || '');

        try {
            const html = await $.ajax({
                url: url,
                method: 'GET',
                data: { type: type },
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });

            $container.html(html);
        } catch (e) {
            $container.empty();
        }
    }

    $(function () {
        const $typeSelect = $('#car_type');
        const $container = $('#ptra-container');

        if (!$typeSelect.length || !$container.length) return;

        $typeSelect.on('change', function () {
            refreshPtraField($typeSelect, $container);
        });

        // Ne pas écraser un champ déjà rendu côté serveur (edit)
        const hasInitial = $.trim($container.html()).length > 0;
        if (!hasInitial) {
            refreshPtraField($typeSelect, $container);
        }
    });

})(jQuery);
