document.querySelectorAll('.remote_course_checkbox')
        .forEach(e => e.addEventListener('change', toggleImportButton));

function toggleImportButton(e) {
    let checked = document.querySelectorAll('.remote_course_checkbox:checked');
    document.querySelector('#remote_form button, #remote_form input[type="button"]').disabled = (checked.length==0);
}