<?php
session_start();
class RegistrationController extends Zend_Controller_Action
{

    function init()
    {
        Zend_Loader::loadClass('GroupQuestion');
        Zend_Loader::loadClass('Persons');
        Zend_Loader::loadClass('Questions');
        Zend_Loader::loadClass('Answers');     		
        Zend_Loader::loadClass('PHPMailer');         
        $this->view->baseUrl = $this->_request->getBaseUrl();
    }

    function indexAction()
    {
        $answerClass = new Answers();
        $questionClass = new Questions();
        $groupQuestionClass = new GroupQuestion();
        $personClass = new Persons();
        $listGroup = $groupQuestionClass->getListGroup();
        $returnOption = "<option value='0' >Pick from the List</option>";
        foreach($listGroup as $group)
        {
            $returnOption .= "<option value='".$group->id."'>".$group->text."</option>";
        }
        $returnOption .= "<option value='-1'>Other</option>";
        $this->view->returnOption = $returnOption;

        $this->view->title = "BIIQ Registration";
        $this->view->right_block = "";
        $this->view->stepbystep = "<div class='stepbystepQuestion'>Business Intelligence IQ (BIIQ) <div style='font-size: 12pt;'>Take the BIIQ test and find out how to improve your business decision making</div></div>";
        $this->view->litle_logo = "<div class='litle_logo'></div>";
        if(isset($_SESSION['person_id']))
        {
            $personId = $_SESSION['person_id'];
            $person = $personClass->getPersonByID($personId);
            $personScore = $personClass->getPersonScore($personId);
            $iCountAnsweredTotal = $questionClass->countTotal();
            $iCountQuestion = $answerClass->countAnsweredByPerson($personId);
            if($iCountAnsweredTotal != $iCountQuestion)
                return $this->_redirect('/group');
            $this->view->person_id = $personId;
            if(((int)$person->opt_in) == 1)
            {
                $this->view->checked = "checked='true'";
            }
        }
        else
            return $this->_redirect('/question');
        $this->render();
    }
    function registrationAction()
    {
        $request = $this->_getParam('request');
        $requestObject = Zend_Json::decode($request, Zend_Json::TYPE_OBJECT);
        $strFname = $requestObject->fname;
        $strLname = $requestObject->lname;
        $strCname = $requestObject->cname;
        $strEmail = $requestObject->email;
        $strNumber = $requestObject->number;
        $strCheckBox = $requestObject->checkBox;
        $strName = $strFname." ".$strLname;
        $stripaddr = $_SERVER['REMOTE_ADDR'];
//        $personId = $_SESSION['person_id'];
//        $score = round($answers->scoreByPerson($personId),0);
		$score = 0;
        // $numlastscore = $personClass->getPersonScore($personId);
        if(isset($_SESSION['person_id']))
        {
            $data = array
            (
                "company_name" => $strCname,
                "contact" => $strNumber,
                "email" => $strEmail,
                "first_name" => $strFname,
                "last_name" => $strLname,
                "opt_in" => $strCheckBox? 1:0,
            	"ipaddr" => $stripaddr,
                "score" => $score 
             );

            $personClass = new Persons();
            $personId = $_SESSION['person_id'];
            //$personScore = $personClass->getPersonScore($personId);
            $where = 'id = ' . $personId;
            $personClass->update($data, $where);
			
			$answers = new Answers();
			$score = round($answers->scoreByPerson($personId),0);
			
            session_destroy();
        }
        if($strCheckBox)
        {
            $mail = new PHPMailer();
            $mail->IsSMTP();// отсылать использу€ SMTP
            $mail->Host     = "mail.tychio.com"; // SMTP сервер
            $mail->Port = 25;

            $mail->SMTPAuth = true;     // включить SMTP аутентификацию
            $mail->Username = "biiq@tychio.com";  // SMTP username
            $mail->Password = "5RL3oiYh"; // SMTP password

            $mail->From     = "biiq@tychio.com"; // укажите от кого письмо
            $mail->FromName = "Tychio BIIQ";
            $mail->AddAddress($strEmail,$strName); // е-мэил кому отправл€ть ///"Name"
            $mail->AddBCC("biiq@tychio.com","BIIQ"); 
			$mail->AddReplyTo("biiq@tychio.com","Tychio BIIQ"); // е-мэил того кому прейдет ответ на ваше письмо
            $mail->WordWrap = 50;// set word wrap
            $mail->IsHTML(true);// отправить в html формате

            $mail->Subject  =  "Your BIIQ Score and Recommendations "; // тема письма
			

			
            $mail->Body  =  "<p>Dear ".$strFname." ".$strLname.",</P><p>Thank you for taking the Business Intelligence IQ Survey. Your current BIIQ score is ".$score.".<br/><br/>
            <p>Your feedback is very important to us and acts as a benchmark for your industry. The BIIQ score helps you identify opportunities for further improvement in decision making through innovative visualization and analysis of dashboards and score cards. Tychio has developed this survey so that you could continually monitor 
    		the decision making process most pragmatically and effectively.<br/><br/>
    		To find out more about how to improve your score, read your report <a href='http://www.tychio.com/howtoimprove.phtml?id=".($score*856)."&id2=".($personId*712)."&ref=email"."' >here.</a> <br/><br/></p>
    		<p><br/><br/>Regards,<br/><br/>Tychio Marketing<BR><BR><a href='http://www.tychio.com'>tychio.com<a></p>"; 
            // тело письма текстовое
            if(!$mail->Send())
            {
               exit;
            }
        }
    }
}