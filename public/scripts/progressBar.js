function UpdateProgressBar(countAnsweredTotal, countQuestion)
{
    if(countQuestion == 0)
        countQuestion=1;
    var iProgress = parseInt(((countAnsweredTotal*100) / countQuestion));
    $(".interestOnProgressBar").html(iProgress+"%");
    $("#progressBar").removeClass();
    $("#progressBar").addClass("progressBar");
    if(iProgress < 11)
        $("#progressBar").addClass("progressBar10");
    else
    if(iProgress < 21)
        $("#progressBar").addClass("progressBar20");
    else
    if(iProgress < 26)
        $("#progressBar").addClass("progressBar25");
    else
    if(iProgress < 31)
        $("#progressBar").addClass("progressBar30");
    else
    if(iProgress < 41)
        $("#progressBar").addClass("progressBar40");
    else
    if(iProgress < 51)
        $("#progressBar").addClass("progressBar50");
    else
    if(iProgress < 61)
        $("#progressBar").addClass("progressBar60");
    else
    if(iProgress < 71)
        $("#progressBar").addClass("progressBar70");
    else
    if(iProgress < 76)
        $("#progressBar").addClass("progressBar75");
    else
    if(iProgress < 81)
        $("#progressBar").addClass("progressBar80");
    else
    if(iProgress <= 99)
        $("#progressBar").addClass("progressBar90");
    else
    if(iProgress > 99)
        $("#progressBar").addClass("progressBar100");
}