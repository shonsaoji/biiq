<?php
session_start();
class QuestionController extends Zend_Controller_Action
{
    private $m_person = null;
    
    function init()
    {
       Zend_Loader::loadClass('Questions');
       Zend_Loader::loadClass('Persons');
       Zend_Loader::loadClass('TypeAction');
       Zend_Loader::loadClass('AnswerVariants');
       Zend_Loader::loadClass('TypeQuestion');
       Zend_Loader::loadClass('TypeViewing');
       Zend_Loader::loadClass('Answers');
       $contextSwitch = $this->_helper->getHelper('contextSwitch');
       $contextSwitch->addActionContext('questions', 'json')->initContext('json');
    }

    function indexAction()
    {
        if(!isset($_SESSION['person_id']))
        {        
            return $this->_redirect('/group');
        }
        $this->view->baseUrl = $this->_request->getBaseUrl();        
        $this->view->title = "BIIQ Questions";
        $this->view->right_block = "";
        $this->view->stepbystep = "<div class='stepbystepQuestion'>Business Intelligence IQ (BIIQ) <div style='font-size: 12pt;'>Take the BIIQ test and find out how to improve your business decision making</div></div>";
        $this->view->litle_logo = "<div class='litle_logo'></div>";
        $this->render();
    }
    
    public function questionsAction()
    {
        $request = $this->_getParam('request');
        $request = stripslashes($request);
        $requestObject = Zend_Json::decode($request, Zend_Json::TYPE_OBJECT);
        $action = $requestObject->action;
        $answerClass = new Answers();
        $questionClass = new Questions();
        $responseObject = new QuestionResult;
        if(strcmp($action,"GetFreedomAnswer")==0)
        {
            $idAnswer = $requestObject->idAnswer;
            $idQuestion = $requestObject->idQuestion;
            $numberPage = $requestObject->numberPage;
            $strText = $requestObject->text;
            if(isset($_SESSION['person_id']))
            {
                $personId = $_SESSION['person_id'];
                $this->SaveAnswer($personId,$idQuestion,$idAnswer,$strText);

                $responseObject->countQuestion                  = $questionClass->countTotal();
                $responseObject->countQuestionInPage            = $questionClass->countByPage($numberPage);
                $responseObject->countAnsweredMainQuestionThisPage  = $answerClass->CountAnsweredMainQuestionPage($personId,$numberPage);
                $responseObject->countAnsweredQuestionThisPage  = $answerClass->countAnsweredQuestionPage($personId,$numberPage);
                $responseObject->countQuestionThisPage  = $answerClass->countQuestionPage($personId,$numberPage);
                $responseObject->countAnsweredTotal             = $answerClass->countAnsweredByPerson($personId);
                $responseObject->questionIdNotAnswered          = $answerClass->getNotAnsweredQuestionPage($personId,$numberPage); 
            }
             echo Zend_Json::encode($responseObject);
        }
        else
        if(strcmp($action,"viewPage")==0)
        {
            if(!isset($_SESSION['numberPage']))
                $numberPage = 1;
            else
                $numberPage = $_SESSION['numberPage'];

            $changeNumberPage = $requestObject->changeNumberPage;
            if(isset($changeNumberPage))
            {
                $numberPage += $changeNumberPage;
                $_SESSION['numberPage'] = $numberPage; 
            }
            $responseObject = new QuestionResult();

            if(isset($_SESSION['person_id']))
            {
                $personId = $_SESSION['person_id'];
                $responseObject->countQuestion                  = $questionClass->countTotal();//общее число вопросов
                $responseObject->countQuestionInPage            = $questionClass->countByPage($numberPage);//колличество главных вопросов на странице
                $responseObject->countTotalPage                 = $questionClass->pagesCount();
                $responseObject->countAnsweredMainQuestionThisPage  = $answerClass->CountAnsweredMainQuestionPage($personId,$numberPage);
                $responseObject->countAnsweredQuestionThisPage  = $answerClass->countAnsweredQuestionPage($personId,$numberPage);
                $responseObject->countQuestionThisPage  = $answerClass->countQuestionPage($personId,$numberPage);
                $responseObject->countAnsweredTotal             = $answerClass->countAnsweredByPerson($personId);
                $responseObject->questionIdNotAnswered          = $answerClass->getNotAnsweredQuestionPage($personId,$numberPage);
                $responseObject->curentPage                     = $numberPage;
                $responseObject->content                        = $this->GenerationMainQuestion($numberPage, $personId);
            }

            echo Zend_Json::encode($responseObject);
        } else
        if(strcmp($action,"GetVariableResponse")==0)
        {
            $idQuestion = $requestObject->idQuestion;
            $idAnswer = $requestObject->idAnswer;
            $numberPage = $requestObject->numberPage;
            if(isset($_SESSION['person_id']))
            {
                $personId = $_SESSION['person_id'];
                $this->SaveAnswer($personId,$idQuestion,$idAnswer);
                $answerVariant = new AnswerVariants();
                $answerValue = $answerVariant->fetchRow('id='.$idAnswer);
                $typeActionClass = new TypeAction();
                $idActionNo = $typeActionClass->getIdByValue("No");

                $responseObject->countQuestion                  = $questionClass->countTotal();
                $responseObject->countQuestionInPage            = $questionClass->countByPage($numberPage);
                $responseObject->countAnsweredMainQuestionThisPage  = $answerClass->CountAnsweredMainQuestionPage($personId,$numberPage);
                $responseObject->countAnsweredQuestionThisPage  = $answerClass->countAnsweredQuestionPage($personId,$numberPage);
                $responseObject->countQuestionThisPage  = $answerClass->countQuestionPage($personId,$numberPage);
                $responseObject->countAnsweredTotal             = $answerClass->countAnsweredByPerson($personId);
                $responseObject->questionIdNotAnswered          = $answerClass->getNotAnsweredQuestionPage($personId,$numberPage);
                
                if($answerValue->type_action == $idActionNo)
                {
                    $responseObject->content = " ";
                    echo Zend_Json::encode($responseObject);
                    return;
                }

                $responseObject->content                        = $this->GenerateVariableResponse($idQuestion, null);
            }
            echo Zend_Json::encode($responseObject);            
        }else
        if(strcmp($action,"GetVariableResponseCheckBox")==0)
        {
            $idQuestion = $requestObject->idQuestion;
            $idAnswer = $requestObject->idAnswer;
            $numberPage = $requestObject->numberPage;
            $flag = $requestObject->flag;
            if(isset($_SESSION['person_id']))
            {
                $personId = $_SESSION['person_id'];
                $answerClass = new Answers();
                $answerClass->SaveAnswerCheckBox($personId,$idQuestion,$idAnswer,$flag);

                $responseObject->countQuestion                  = $questionClass->countTotal();
                $responseObject->countQuestionInPage            = $questionClass->countByPage($numberPage);
                $responseObject->countAnsweredMainQuestionThisPage  = $answerClass->CountAnsweredMainQuestionPage($personId,$numberPage);
                $responseObject->countAnsweredQuestionThisPage  = $answerClass->countAnsweredQuestionPage($personId,$numberPage);
                $responseObject->questionIdNotAnswered          = $answerClass->getNotAnsweredQuestionPage($personId,$numberPage);
                $responseObject->countQuestionThisPage  = $answerClass->countQuestionPage($personId,$numberPage);
                $responseObject->countAnsweredTotal             = $answerClass->countAnsweredByPerson($personId);
            }
            echo Zend_Json::encode($responseObject);
        }
    }

    private function GenerationMainQuestion($numberPage, $person)
    {
        $strReturn = "";
        $question = new Questions();
        $listQuestion = $question->getByPage($numberPage);
        $listQuestionAnswer = null;

        if(!is_null($person))
        {
            $listQuestionAnswer = $question->getAnsweredQuestionsbyPersion($person);
        }

        if(!is_null($listQuestion))
        {
            $i = 1;
            $answerVariantClass = new AnswerVariants();
            $typeQuestionClass = new TypeQuestion();
            $typeActionClass = new TypeAction();
            $idActionYes = $typeActionClass->getIdByValue("Yes");
            $idActionNo = $typeActionClass->getIdByValue("No");
            $sumQuestion = $question->sumQuestionsPrevPage($numberPage);
            foreach($listQuestion as $quest)
            {
                $listAnswer = $answerVariantClass->getByQuestionId($quest->id);                
                $strRBYes = "";
                $strRBNo = "";
                foreach($listAnswer as $variant)
                {
                    $strSelect = "";
                    if($variant->type_action == $idActionYes)
                    {
                        if($this->SearchInAnswer($quest->id, $variant->id, $listQuestionAnswer))
                            $strSelect = "RadioSelectedYes";
                          $strRBYes = "<input type='radio' class='RadioClass' id='responseYes".$quest->id."' name='response".$quest->id."'>";
					      $strRBYes .= "<label id='responseYes".$quest->id."_Lbl' for='responseYes".$quest->id."' class='RadioLabelClass RadioLabelYes ".$strSelect."' onClick='changeMyRadio(\"responseYes".$quest->id."\");GetVariableResponse(".$quest->id.",".$variant->id.");'></label>";
//                        $strRBYes .= "<a href='#' id='responseYes".$quest->id."' name='response".$quest->id."' class='RadioClass RadioLabelClass RadioLabelYes ".$strSelect."' onClick='changeMyRadio(\"responseYes".$quest->id."\");GetVariableResponse(".$quest->id.",".$variant->id.");click_yes();'><a>";
                    }
                    if($variant->type_action == $idActionNo)
                    {
                        if($this->SearchInAnswer($quest->id, $variant->id, $listQuestionAnswer))
                            $strSelect = "RadioSelectedNo";
                        $strRBNo = "<input type='radio' class='RadioClass' id='responseNo".$quest->id."' name='response".$quest->id."'>";
					    $strRBNo .= "<label id='responseNo".$quest->id."_Lbl' for='responseNo".$quest->id."' class='RadioLabelClass RadioLabelNo ".$strSelect."' onClick='changeMyRadio(\"responseNo".$quest->id."\");GetVariableResponse(".$quest->id.",".$variant->id.")'></label>";
                        //$strRBYes .= "<a href='#' id='responseNo".$quest->id."' name='response".$quest->id."' class='RadioClass RadioLabelClass RadioLabelNo ".$strSelect."' onClick='changeMyRadio(\"responseNo".$quest->id."\");GetVariableResponse(".$quest->id.",".$variant->id.");click_no();'><a>";
                    }
/*                        if($this->SearchInAnswer($quest->id, $variant->id, $listQuestionAnswer))
                            $strSelect = "checked";
                        $strReturn = $strReturn . "<input type='radio' class='radioAnswer' name='response".$idQuestion."' ".$strSelect." onclick='GetVariableResponse(".$idQuestion.",".$variant->id.");'>".$variant->text;
                        if(!is_null($question) && !is_null($question->parent_id))
                            $strReturn .= "<br>";*/
                }
                $sumQuestion++;
 /* first table width=541, 125  
  * second table width=85, height=125
  */             
                $strReturn .= "<div class='TextMainQuestion' align='center'>
<table width='541' height='125' border='0' cellpadding='0' cellspacing='0'>
	<tr>
		<td><a id='an_question".$quest->id."'></a>
			<div class='question'>
				<div class='cell'>
					<div class='text'>
						".$sumQuestion.") ".$quest->text."
					</div>
				</div>
			</div>
		</td>
		<td>
			<table width='85' height='125' border='0' cellpadding='0' cellspacing='0'>
					<tr>
					<td>
						<img src='".$this->_request->getBaseUrl()."/public/images/form_question_02.png' width='85' height='27' alt=''>
					</td>
				</tr>
				<tr>
					<td>".$strRBYes."					
					</td>
				</tr>
				<tr>
					<td>".$strRBNo."
					</td>
				</tr>
				<tr>
					<td>
						<img src='".$this->_request->getBaseUrl()."/public/images/form_question_06.png' width='85' height='26' alt=''>
					</td>
				</tr>
			</table>
		</td>
		<td>
			<img src='".$this->_request->getBaseUrl()."/public/images/close_box.png' width='27' height='125' alt=''>
		</td>
	</tr>
</table></div>";
                //$strReturn = $strReturn . "<div class='TextMainQuestion'>".($i).". ".$quest->text."</div>";
                //$strReturn .= $this->GenerationVariableAnswer($quest->id, $listQuestionAnswer);

                $strReturn .= "<table width='541' align='center' border=0 cellspacing=0 cellpadding=0><tr align='center'><td><img src=/biiq/public/images/spacer.gif width=40 height=1></td><td align='left'><div align='center' class='DependentQuestions' id='DependentQuestions".$quest->id."'>";

                if($this->SearchInAnswerYes($quest->id,$listQuestionAnswer))
                    $strReturn .= $this->GenerateVariableResponse($quest->id, $listQuestionAnswer);

                $strReturn .= "</div></td></tr></table>";
                $i++;
            }
        }
        return $strReturn;
    }

    private function GenerationVariableAnswer($idQuestion, $listQuestionAnswer)
    {
        $strReturn = "";
        $answerVariantClass = new AnswerVariants();
        $typeQuestionClass = new TypeQuestion();
        $typeViewingClass = new TypeViewing();
        $listAnswer = $answerVariantClass->getByQuestionId($idQuestion);
        $strSelect = "";
        $questionClass = new Questions();
        $question = $questionClass->getByQuestionId($idQuestion);
        $idTypeFreedom = $typeQuestionClass->getIdByValue("freedom");
        $idTypeViewingRadio = $typeViewingClass->getIdByValue("radio");
        $idTypeViewingCheckBox = $typeViewingClass->getIdByValue("checkbox");
        
        foreach($listAnswer as $variant)
        {
            $strSelect = "";

            if($variant->type == $idTypeFreedom)
            {
                $strSelect = $this->GetFreedomAnswer($idQuestion,$variant->id,$listQuestionAnswer);
                $strReturn .= "<input type='text' class='textAnswer' onchange='GetFreedomAnswer(".$idQuestion.",".$variant->id.");' id='freedom".$variant->id."' value='".$strSelect."' />";
            }
            else
            {

                if($this->SearchInAnswer($idQuestion, $variant->id, $listQuestionAnswer))
                    $strSelect = "checked";
                if($question->type_viewing_id == $idTypeViewingRadio)
                    $strReturn = $strReturn . "<input type='radio' class='radioAnswer' name='response".$idQuestion."' ".$strSelect." onclick='GetVariableResponse(".$idQuestion.",".$variant->id.");'>".$variant->text;
                else
                    $strReturn = $strReturn . "<input type='checkbox' class='radioAnswer' id='response".$variant->id."' ".$strSelect." onclick='GetVariableResponseCheckBox(".$idQuestion.",".$variant->id.");'>".$variant->text; 
                if(!is_null($question) && !is_null($question->parent_id))
                    $strReturn .= "<br>";
            }
        }
        return $strReturn;
    }

    private function GetFreedomAnswer($idQuestion, $idAnswer, $questionAnswer)
    {
        if(!is_null($questionAnswer))
        foreach($questionAnswer as $answer)
        {
            if($answer->question_id == $idQuestion && $answer->answer_variant_id == $idAnswer)
            {
                return htmlspecialchars($answer->another_answer,ENT_QUOTES);
            }
        }
        return "";
    }

    private function SearchInAnswer($idQuestion, $idAnswer, $questionAnswer)
    {
        if($questionAnswer != null)
        foreach($questionAnswer as $answer)
        {
            if($answer->question_id == $idQuestion && $answer->answer_variant_id == $idAnswer)
                return true;
        }
        return false;
    }
    private function SearchInAnswerYes($idQuestion, $questionAnswer)
    {
        if($questionAnswer != null)
        {
            $typeActionClass = new TypeAction();
            $idActionYes = $typeActionClass->getIdByValue("Yes");
            foreach($questionAnswer as $answer)
            {
                if($answer->question_id == $idQuestion && $answer->type_action == $idActionYes)
                    return true;
            }
        }
        return false;
    }
    private function GenerateVariableResponse($idQuestion, $listQuestionAnswer)
    {
        $strReturn = "";
        $questionClass = new Questions();
        $listChildQuestion = $questionClass->getByChildPage($idQuestion);
        if($listChildQuestion != null)
        {
            foreach($listChildQuestion as $childQuestion)
            {
                $strReturn = $strReturn . "<div class='TextChildQuestion'>".$childQuestion->text."</div>";
                $strReturn .= $this->GenerationVariableAnswer($childQuestion->id,$listQuestionAnswer);
            }
        }
        return $strReturn;
    }

    private function SaveAnswer($idPerson, $idQuestion, $idAnswer, $text=null)
    {        
            $answer = new Answers();
            $answer->saveMain($idPerson, $idQuestion, $idAnswer, $text);
    }
}

class QuestionResult 
{
   public $content = "";
   public $cnahgeNumberPage = 0;
   public $countQuestion = 0;//всего вопросов
   public $countQuestionInPage = 0;//вопросов на этой странице
   public $countTotalPage = 0;//всего страниц
   public $countAnsweredQuestionThisPage = 0;   //на сколько вопросов ответил посетитель на этой странице
   public $curentPage = 1;
   public $countAnsweredTotal = 0;
   public $countAnsweredMainQuestionThisPage = 0;
   public $countQuestionThisPage = 0;
   public $questionIdNotAnswered = 0;
}