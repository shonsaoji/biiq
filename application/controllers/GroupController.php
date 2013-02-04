<?php
session_start();
class GroupController extends Zend_Controller_Action
{
    function init()
    {
        Zend_Loader::loadClass('GroupQuestion');
        Zend_Loader::loadClass('SubGroupQuestion');
        Zend_Loader::loadClass('Persons');
        $this->view->baseUrl = $this->_request->getBaseUrl();
    }
    
    function savegroupAction()
    {
        $request = $this->_getParam('request');
        $requestObject = Zend_Json::decode($request, Zend_Json::TYPE_OBJECT);
        $groupId = $requestObject->group_id;
        if(isset($_SESSION['person_id']))
        {
            $personId = $_SESSION['person_id'];

            $data = array
            (
                "group_id" => $groupId
            );

            $personClass = new Persons();
            $where = 'id = ' . $personId;
            $personClass->update($data, $where);
        }
        echo Zend_Json::encode("");
        return;
    }

    function savesicAction()
    {
        $request = $this->_getParam('request');
        $requestObject = Zend_Json::decode($request, Zend_Json::TYPE_OBJECT);
        $sic = $requestObject->sic;
        if(isset($_SESSION['person_id']))
        {
            $personId = $_SESSION['person_id'];
            $groupQuestionClass = new GroupQuestion();
            $groupId = $groupQuestionClass->getIdBySic($sic);
            $data = array
            (
                "group_id" => $groupId
            );

            $personClass = new Persons();
            $where = 'id = ' . $personId;
            $personClass->update($data, $where);
        }
        echo Zend_Json::encode("");
        return;
    }
    
    function indexAction()
    {
        if (!isset($_SESSION['person_id']))
        {
            $person = new Persons();
			// Added default values to array
            $data = array
            (
                "company_name" => "",
                "contact" => "",
                "email" => "donotreply@cerebrateinc.com",
                "first_name" => "New User ".$_SERVER['REMOTE_HOST'],
                "last_name" => $_SERVER['REQUEST_TIME'],
                "group_id" => null,
				"opt_in" => 1,
				"ipaddr" => $_SERVER['REMOTE_ADDR']
            );
            $person->InsertPerson($data);
            $_SESSION['person_id']= $person->GetId();
        }

        $this->view->title = "BIIQ - Choose your industry group";
        $groupQuestionClass = new GroupQuestion();
        $subgroupQuestionClass = new SubGroupQuestion();
        $listGroup = $groupQuestionClass->getListGroup();
        $i = 1;
        $returnStr = "";
        $returnOption = "<option>Pick from the List</option>";
        $oldSic = "";
        $oldText = "";
        $value = "";
        $oldValue = "";
        $listSic = "";
        $step = 0;
		$listSicInName = "";
        for($i=0;$i<sizeof($listGroup);$i++)
        {
            $group = $listGroup[$i];

            if(strlen($group->css) != 0)
            {
                $returnStr .= "<li><a href='#' onclick='click_group(".$group->id.")' class='".$group->css." menu current'></a></li>";
            }
            else
            {
                if($step == 0)
                {
                    $oldValue = $group->text;
                    $listSic =  "|".$group->sic;
					$listSicInName  =  $group->sic;
                }
                else
                {
                    if($group->text != $oldValue)
                    {
                        $returnOption .= "<option value='".$listSic."|'>".$oldValue." (".$listSicInName.")"."</option>";
                        $oldValue = $group->text;
                        $listSic = "|".$group->sic;
						$listSicInName  =  $group->sic;
                    }
                    else
                    {
                        $listSic .= "|". $group->sic;
						$listSicInName  .=  ",".$group->sic;
                    }
                    if($i == (sizeof($listGroup)-1))
                    {
                        $returnOption .= "<option value='".$listSic."|'>".$group->text." (".$listSicInName.")"."</option>";
                    }
                }
                $step++;
            }            
        }
        $returnStr .= '<li><a href="#anchor" onclick="click_group(0);" class="oi menu current"></a></li>';

        $this->view->stepbystep = "<div class='whichindustry'></div>";
        $this->view->litle_logo = "<div class='litle_logo'></div>";
        $this->view->buttomRight = "<input type='button' onClick='click_next();' class='buttomRight' />";
        $this->view->listGroup = $returnStr;
        $this->view->returnOption = $returnOption;         
        $this->render();
    }
}