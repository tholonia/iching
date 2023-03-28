$(document).ready(function () {

    $(function () {
        $("#searchtipmsg").dialog({
            autoOpen: false
        });
        $("#searchtip").on("click", function () {
            console.log("SEARCH TIP clicked");
            $("#searchtipmsg").dialog("open");
        });
    });


    $("#quotes").on("click", function () {
        var sterm = $("#searchterm").val().toString();
        var test = sterm.search('"');
        var javascriptSUCKS = sterm;

        if (test != -1) {
            console.log(sterm);
            console.log("quotes");
            javascriptSUCKS = sterm.replace(/"/g, '');
            $("#searchterm").val(javascriptSUCKS);
        } else {
            console.log(sterm);
            console.log("no quotes");
            javascriptSUCKS = '"' + sterm + '"';
            $("#searchterm").val(javascriptSUCKS);
        }
    });

});