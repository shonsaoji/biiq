<?php

class IndexController extends Zend_Controller_Action
{

    function init()
    {
        Zend_Loader::loadClass('Questions');
        $this->view->baseUrl = $this->_request->getBaseUrl();
    }
    function indexAction()
    {
        $this->view->title = "Cerebrate BIIQ - Business Intelligence IQ";
        $this->view->right_block = "";
        $this->view->stepbystep = "";
        $this->view->litle_logo = "";
        $this->render();
    }
}
