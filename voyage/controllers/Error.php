<?php

/**
 * 错误异常处理器
 *
 * @author JiangJian <silverd@sohu.com>
 * $Id: Error.php 105 2012-09-29 04:22:18Z silverd30@gmail.com $
 */

class Controller_Error extends Core_Controller_Web
{
    public function errorAction()
    {
        $e = $this->_request->getException();

        if (!$e instanceof Exception) {
            exit('Access Denied');
        }

        try {

            throw $e;

        } catch (Core_Exception_404 $e) {

            header404();

        } catch (Core_Exception_Logic $e) {

            exit($e->getMessage());

        } catch (Yaf_Exception_LoadFailed_View $e) {

            exit($e->getMessage());

        } catch (Exception $e) {

            switch ($e->getCode()) {
                case YAF_ERR_NOTFOUND_MODULE:
                case YAF_ERR_NOTFOUND_CONTROLLER:
                case YAF_ERR_NOTFOUND_ACTION:
                    header404();
            }

            if (!isDebug()) {
                header500();
            }

            echo '<pre>', $e, '</pre>';
            exit();
        }

        return false;
    }
}
