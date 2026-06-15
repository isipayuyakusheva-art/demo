/* ============================================================
   Клиентский JS: маска телефона и логика поля «причина отмены».
   Серверная валидация остаётся главной — JS лишь улучшает UX.
   ============================================================ */
(function () {
    'use strict';

    // ---- Маска телефона: +7(XXX)-XXX-XX-XX ----
    document.querySelectorAll('input[data-phone]').forEach(function (input) {
        input.addEventListener('input', function () {
            var d = input.value.replace(/\D/g, '');
            if (d.startsWith('8')) d = '7' + d.slice(1);
            if (!d.startsWith('7')) d = '7' + d;
            d = d.slice(0, 11);

            var out = '+7';
            if (d.length > 1) out += '(' + d.slice(1, 4);
            if (d.length >= 4) out += ')';
            if (d.length >= 5) out += '-' + d.slice(4, 7);
            if (d.length >= 8) out += '-' + d.slice(7, 9);
            if (d.length >= 10) out += '-' + d.slice(9, 11);
            input.value = out;
        });
    });

    // ---- Показ поля «Причина отмены» только при статусе «Отменено» ----
    document.querySelectorAll('.status-form').forEach(function (form) {
        var select = form.querySelector('.js-status');
        var reason = form.querySelector('.js-reason');
        if (!select || !reason) return;
        select.addEventListener('change', function () {
            reason.style.display = (select.value === 'cancelled') ? '' : 'none';
            if (select.value === 'cancelled') reason.focus();
        });
    });
})();
