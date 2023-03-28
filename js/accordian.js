$(document).ready(function () {
    $(function () {
//        $("#accordion1").accordion({autoHeight: false});
//        $("#accordion2").accordion({autoHeight: false});
        $("#accordion1").accordion(
                {
                    heightStyle: "content",
                    collapsible: true
                }
         );
        $("#accordion2").accordion({heightStyle: "content"});
//        $("#accordion1").accordion();
//        $("#accordion2").accordion();
    });
});
