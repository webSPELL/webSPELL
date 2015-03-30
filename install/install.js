$(function() {
    if ($("#todo_list").length > 0) {
        var data = $("#todo_list").text(),
            todo = jQuery.parseJSON(data),
            job = todo.shift();
        handleJob(job, 0, todo.length, todo);
    }
});
function scrollDown() {
    var detailsText = $("#details_text");
    detailsText.scrollTop(detailsText[0].scrollHeight);
}
function handleJob(job, doneCount, allCount, todo) {
    jQuery.ajax({
        url: "ajax.php?function=" + job
    }).done(function(result) {
        var detailsText = $("#details_text"), percentage;
        detailsText.append(result.message + "<br/>");
        scrollDown()
        doneCount++;
        percentage = Math.ceil((doneCount / allCount) * 100);
        $("#progress_bar").attr("aria-valuenow", percentage);
        $("#progress_bar").css("width", percentage + "%");
        $("#progress_bar span").html(percentage + "%");
        if (result.status == "fail") {
            installFailedCallback()
        } else {
            if (doneCount < allCount) {
                job = todo.shift();
                handleJob(job, doneCount, allCount, todo);
            } else {
                installSuccessfulCallback();
            }
        }
    }).fail(function(result) {
        installFailedCallback()
    });
}
function installFailedCallback() {
    $("#details_text").append("<font color='red'>Install failed</font>");
    $("#progress_bar").addClass("progress-bar-danger");
    scrollDown();
}
function installSuccessfulCallback() {
    $("#details_text").append("<font color='green'>Install successful</font>");
    $("#progress_bar").addClass("progress-bar-success");
    scrollDown();
    $("#result").css("display", "block");
}
