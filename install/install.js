$(function () {
    if ($('#todo_list').length > 0) {
        var data = $('#todo_list').text();
        var todo = jQuery.parseJSON(data);
        var all_count = todo.length;
        var job = todo.shift();
        handleJob(job, 0, all_count, todo);
    }
});
function scroll_down() {
    var details_text = $('#details_text');
    details_text.scrollTop(details_text[0].scrollHeight);
}
function handleJob(job, done_count, all_count, todo) {
    jQuery.ajax({
        url: 'ajax.php?function=' + job
    }).done(function (result) {
        var details_text = $('#details_text');
        details_text.append(result.message + "<br/>");
        scroll_down()
        done_count++;
        var percentage = Math.ceil((done_count / all_count) * 100);
        $('#progress_bar').attr('aria-valuenow', percentage);
        $('#progress_bar').css('width', percentage + '%');
        $('#progress_bar span').html(percentage + '%');
        if (result.status == "fail") {
            install_failed()
        }
        else {
            job = todo.shift();
            if (job) {
                handleJob(job, done_count, all_count, todo);
            }
            else {
                install_successful();
            }
        }
    }).fail(function (result) {
        install_failed()
    });
}
function install_failed() {
    $('#details_text').append('<font color="red">Install failed</font>');
    $('#progress_bar').addClass('progress-bar-danger');
    scroll_down();
}
function install_successful() {
    $('#details_text').append('<font color="green">Install successful</font>');
    $('#progress_bar').addClass('progress-bar-success');
    scroll_down();
    $('#result').css('display', '');
}
