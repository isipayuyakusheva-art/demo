// показываем поле "причина отмены" только когда выбран статус "Отменено"
var forms = document.querySelectorAll('.status-form');
forms.forEach(function (form) {
    var select = form.querySelector('select');
    var reason = form.querySelector('input[name="cancel_reason"]');

    function toggle() {
        reason.style.display = select.value == 'cancel' ? 'inline-block' : 'none';
    }
    toggle();
    select.addEventListener('change', toggle);
});
