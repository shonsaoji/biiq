<?php
class ScoreController extends Zend_Controller_Action
{

    function init()
    {
        Zend_Loader::loadClass('Answers');
        Zend_Loader::loadClass('Persons');        
        Zend_Loader::loadClass('GroupQuestion');
        $this->view->baseUrl = $this->_request->getBaseUrl();
    }

    function indexAction()
    {
        $personId = $this->_getParam('xsession_id')/712;
        $this->view->title = "BIIQ Score";        
        $this->view->checked = " ";
        $this->view->personId = 0;
        if(is_numeric($personId))
        {
            $this->view->personId = $personId;
            $answers = new Answers();
            $personClass = new Persons();
            $groupQuestionClass = new GroupQuestion();
            $listBar = $answers->totalBar();
            //$listGroup = $groupQuestionClass->getListGroup();
            
            $returnGroup = "[";
            $returnGroupValue = "[";
            $i = 0;
            $max = 0;
            foreach($listBar as $jb)
            {
                if($i!=0)
                {
                    $returnGroup .= ",";
                    $returnGroupValue .= ",";
                }

                $returnGroup .= "'".$jb->name."'";
                $count = round($jb->sum_score,0);
                $returnGroupValue .= round($count/2.1,1); //Remove this hardcode
                if($max < $count)
                    $max = $count;
                $i++;
            }
            $returnGroup .= "]";
            $returnGroupValue .= "]";
            $this->view->max_group = 10; // $max + 5;
            $this->view->group = $returnGroup;
            $this->view->groupvalue = $returnGroupValue; 

            $score = $answers->scoreByPerson($personId);
            $this->view->score = round($score,0);

            $this->view->stepbystep = "<div class='stepbystepQuestion'>Business Intelligence IQ (BIIQ) <div style='font-size: 12pt;'>Take the BIIQ test and find out how to improve your business decision making</div></div>";
            $this->view->litle_logo = "<div class='litle_logo'></div>";

        }
        else
            return $this->_redirect('/question');
        $this->render();
    }
}