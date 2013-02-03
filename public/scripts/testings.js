    var iCountQuestion = 0;
    var iCountQuestionInPage = 0;
    var iCountTotalPage = 0;
    var iCountAnsweredTotal = 0;
    var iCountAnsweredMainQuestionThisPage = 0;
    var iCountAnsweredQuestionThisPage = 0; 
    var iCountQuestionThisPage = 0;   
    var iCurentPage = 1;
    var iQuestionIdNotAnswered = 0;
    var strBaseUrl = "";

    function SetBaseUrl(url)
    {
        strBaseUrl = url;
    }
    function InitPage()
    {
        $("#btnPrev").css("display","none");
        $("#btnNext").css("display","none");
		window.scrollTo(0,0);
    }
    function TrimStr(s)
    {
        s = s.replace( /^\s+/g, '');
        return s.replace( /\s+$/g, '');
    }
    function GetFreedomAnswer(idQuestion,idAnswer)
    {
        var str = $("#freedom"+idAnswer).attr("value");
        var requestObject = new Object();
        requestObject.action = "GetFreedomAnswer";
        requestObject.idQuestion = idQuestion;
        requestObject.idAnswer = idAnswer;
        requestObject.numberPage = iCurentPage;
        requestObject.text = str;

        var jsonRequest = JSON.stringify(requestObject);

        var url = './question/questions';
        $.ajax({
           type: "get",
           url: url,
           dataType : "json",
           data: "request="+jsonRequest,
           error:  function(transport)
                {
                    var str =  new String(transport.responseText);
                    var responseObject = JSON.parse(str.substring(2));
                    try
                    {
                        iCountQuestion = responseObject.countQuestion;
                        iCountAnsweredTotal = responseObject.countAnsweredTotal;
                        iCountQuestionInPage = responseObject.countQuestionInPage;
                        iCountAnsweredMainQuestionThisPage = responseObject.countAnsweredMainQuestionThisPage;
                        iCountAnsweredQuestionThisPage = responseObject.countAnsweredQuestionThisPage;
                        iCountQuestionThisPage = responseObject.countQuestionThisPage;
                        iQuestionIdNotAnswered = responseObject.questionIdNotAnswered;
                        UpdateProgressBar(iCountAnsweredTotal, iCountQuestion);
                        changeControl();
                    }catch(Exeption)
                    {

                    }
                }
        });
    }
    function viewPage(changeNumberPage)
    {
        InitPage();
        var requestObject = new Object();
        requestObject.action = "viewPage";
        requestObject.changeNumberPage = changeNumberPage;

        var jsonRequest = JSON.stringify(requestObject);
        var url = './question/questions';

       $.ajax({
           type: "get",
           url: url,
           dataType : "json",
           data: "request="+jsonRequest,
           error: renderResults
        });
    }

    function renderResults(response)
    {
        var str =  new String(response.responseText);
        var responseObject = JSON.parse(str.substring(2));
        try
        {
            $('#pageQuestion').html(responseObject.content);
            iCurentPage = responseObject.curentPage;
            iCountQuestion = responseObject.countQuestion;
            iCountQuestionInPage = responseObject.countQuestionInPage;
            iCountTotalPage = responseObject.countTotalPage;
            iCountAnsweredTotal = responseObject.countAnsweredTotal;

            iCountAnsweredMainQuestionThisPage = responseObject.countAnsweredMainQuestionThisPage;
            iCountAnsweredQuestionThisPage = responseObject.countAnsweredQuestionThisPage;
            iCountQuestionThisPage = responseObject.countQuestionThisPage;
            iQuestionIdNotAnswered = responseObject.questionIdNotAnswered;
            iCurentPage = responseObject.curentPage; 
            UpdateProgressBar(iCountAnsweredTotal, iCountQuestion);
            changeControl();
        }catch(Exeption)
        {

        }
    }
    function GetVariableResponseCheckBox(idQuestion, idAnswer)
    {
        var requestObject = new Object();
        requestObject.action = "GetVariableResponseCheckBox";
        requestObject.idQuestion = idQuestion;
        requestObject.idAnswer = idAnswer;
        requestObject.numberPage = iCurentPage;
        requestObject.flag = $("#response"+idAnswer).attr("checked");
        var jsonRequest = JSON.stringify(requestObject);
        var url = './question/questions';
        $.ajax({
           type: "get",
           url: url,
           dataType : "json",
           data: "request="+jsonRequest,
           error: function(transport)
                {
                    var str =  new String(transport.responseText);
                    var responseObject = JSON.parse(str.substring(2));
                    try
                    {
                        $("#DependentQuestions"+idQuestion).html(responseObject.content);
                        iCountQuestion = responseObject.countQuestion;
                        iCountAnsweredTotal = responseObject.countAnsweredTotal;
                        iCountQuestionInPage = responseObject.countQuestionInPage;
                        iCountAnsweredMainQuestionThisPage = responseObject.countAnsweredMainQuestionThisPage;
                        iCountAnsweredQuestionThisPage = responseObject.countAnsweredQuestionThisPage;
                        iCountQuestionThisPage = responseObject.countQuestionThisPage;
                        iQuestionIdNotAnswered = responseObject.questionIdNotAnswered;
                        UpdateProgressBar(iCountAnsweredTotal, iCountQuestion);
                        changeControl();
                    }catch(Exeption)
                    {

                    }
                }
        });
    }
    function GetVariableResponse(idQuestion, idAnswer)
    {
        var requestObject = new Object();
        requestObject.action = "GetVariableResponse";
        requestObject.idQuestion = idQuestion;
        requestObject.idAnswer = idAnswer;
        requestObject.numberPage = iCurentPage;

        var jsonRequest = JSON.stringify(requestObject);
        var url = './question/questions';
        $.ajax({
           type: "get",
           url: url,
           dataType : "json",
           data: "request="+jsonRequest,
           error: function(transport)
                {
                    var str =  new String(transport.responseText);
                    var responseObject = JSON.parse(str.substring(2));
                    try
                    {
                        $("#DependentQuestions"+idQuestion).html(responseObject.content);
                        iCountQuestion = responseObject.countQuestion;
                        iCountAnsweredTotal = responseObject.countAnsweredTotal;            
                        iCountQuestionInPage = responseObject.countQuestionInPage;
                        iCountAnsweredMainQuestionThisPage = responseObject.countAnsweredMainQuestionThisPage;
                        iCountAnsweredQuestionThisPage = responseObject.countAnsweredQuestionThisPage;
                        iCountQuestionThisPage = responseObject.countQuestionThisPage;
                        iQuestionIdNotAnswered = responseObject.questionIdNotAnswered;
                        UpdateProgressBar(iCountAnsweredTotal, iCountQuestion);
                        changeControl();
                    }catch(Exeption)
                    {

                    }
                }
        });
    }
    function changeControl()
    {
        if(iCurentPage == 1)
            $("#btnPrev").css("display","none");
        else
            $("#btnPrev").css("display","");
        $("#btnNext").css("display","none");
        if(iCountQuestionInPage == iCountAnsweredMainQuestionThisPage)
        {
            if(iCountQuestionThisPage == iCountAnsweredQuestionThisPage)
            {
                $("#btnNext").css("display","");
            }
        }
    }    
    
    function ViewNextPage()
    {
        if(iQuestionIdNotAnswered != 0)
            location='#an_question'+iQuestionIdNotAnswered;
        else
        if(iCountQuestionInPage == iCountAnsweredMainQuestionThisPage)
        {
            if(iCountQuestionThisPage == iCountAnsweredQuestionThisPage)
            {
                if(iCurentPage < iCountTotalPage)
                {
                    viewPage(+1);
                }
                else
                    if(iCurentPage == iCountTotalPage)
                    {
                        location.href=strBaseUrl+'/registration';
                    }
            }
        }
    }
    function ViewPrevPage()
    {
        if(iCurentPage > 1)
        {
            viewPage(-1);
        }
    }