<?php

class Plugin_Common extends Yaf_Plugin_Abstract
{
    public function preDispatch(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response)
    {
        Yaf_Dispatcher::getInstance()->disableView();
    }

    public function preResponse(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response)
    {
        $controller = Yaf_Registry::get('controller');
        $controller->preResponse();

        if ($tpl = $controller->getTpl()) {
            $controller->getView()->display($tpl);
            $controller->yafAutoRender = false;
        }
    }

    public function postDispatch(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response)
    {
        $view = Yaf_Registry::get('controller')->getView();

        if ($view->getLayout()) {
            $response->setBody($view->layout($response->getBody()));
        }
    }
}