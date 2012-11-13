<?php

class Plugin_Common extends Yaf_Plugin_Abstract
{
    public function routerStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response)
    {
        // 将 XianglePHP 风格的 URI 转为 Yaf 风格
        // 例如：/index-test/hello-world => /indextest/helloworld
        $uri = $request->getRequestUri();
        if (strpos($uri, '-') !== false) {
            $request->setRequestUri(str_replace('-', '', $uri));
        }
    }

    // TODO 无效
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
            $response->setBody($view->layout($response->getBody(), array(), true));
        }
    }
}