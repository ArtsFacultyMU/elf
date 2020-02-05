document.querySelectorAll('.remote_course_checkbox')
        .forEach(e => e.addEventListener('change', toggleImportButton));

function toggleImportButton(e) {
    let checked = document.querySelectorAll('.remote_course_checkbox:checked');
    document.querySelector('#remote_form input, #remote_form button').disabled = (checked.length==0);
}
