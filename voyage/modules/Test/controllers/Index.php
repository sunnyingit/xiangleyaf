<?php

class Controller_Index extends Core_Controller_Web
{
    public $yafAutoRender = false;

    public function indexAction()
    {
        echo 'hello modules/Controller_Index';
        echo Com_DB_Hash::tableName('xxxxx');
    }

    public function memcacheAction()
    {
        $a = microtime(true);

        echo '<pre>';
        var_dump($a);
        var_dump($this->_memcache->set('a', $a));
        var_dump($this->_memcache->get('a'));
        var_dump($this->_memcache->default->get('a'));

        var_dump($this->_memcache->static->set('aaa', uniqid()));
        var_dump($this->_memcache->static->get('aaa'));

        var_dump($this->_memcache->static->set('bbb', 'abc'));
        var_dump($this->_memcache->static->get('bbb'));

        return false;
    }

    public function redisAction()
    {
        $a = microtime(true);

        var_dump($a);
        var_dump($this->_redis->default->set('a', $a));
        var_dump($this->_redis->default->get('a'));
        var_dump($this->_redis->get('a'));

        var_dump($this->_redis->static->set('aaa', uniqid()));
        var_dump($this->_redis->static->get('aaa'));

        var_dump($this->_redis->blog->rpush('list', uniqid()));
        var_dump($this->_redis->blog->lrange('list', 0, -1));
        var_dump($this->_redis->blog->lpop('list'));
        var_dump($this->_redis->blog->lrange('list', 0, -1));

        return false;
    }

    public function xcacheAction()
    {
        $xcahe = Com_Cache_Broker::getInstance('XCache');

        var_dump($xcahe->set('good', 'hello-' . uniqid()));
        var_dump($xcahe->get('good', 'hello-' . uniqid()));

        return false;
    }

    public function slashesAction()
    {
        $data = array(
            'abc' => 'hello \'s boy',
            'abcd' => array(
                'key' => 'hello \'s boy',
            ),
        );

        var_dump($data);

        return false;
    }

    public function insertAction()
    {
        if ($this->isPost()) {

            $ip = 'abc\'efg';
            $setArr = array(
                'title' => $this->getx('title'),
                'content' => json_encode(array('key' => '我知道')),
                'ip' => $ip,
            );

            $db = Com_DB::get('test');
            var_dump($db->insert('my_blog', $setArr));

        } else {

            $form = <<<EOF
<form action="/test/pdo" method="POST">
    <input type="text" name="title" value="" />
    <input type="text" name="content" value="" />
    <input type="submit" />
</form>
EOF;
            echo $form;
        }

        return false;
    }

    public function whereAction()
    {
        $whereArr = array(
            'title' => 123,
            'ip' => array('IN', array('撒旦法', 'abc\'efg')),
            'created' => array('SQL', 'BETWEEN 10 AND 20'),
        );

        $whereSql = Com_DB::getWhereSql($whereArr);

        var_dump($whereSql);
        exit;

        if ($this->isPost()) {

            $title = $this->get('title');

            $whereArr = array(
                'title' => $title,
                'ip' => array('IN', array('撒旦法', 'abc\'efg')),
            );

            $whereSql = Com_DB::getWhereSql($whereArr);

            $db = Com_DB::get('test');

            echo '<pre>';
            print_r($db->fetchAll("select * from `my_blog` where {$whereSql}"));

        } else {

            $form = <<<EOF
<form action="" method="post">
    <input type="text" name="title" value="" />
    <input type="submit" />
</form>
EOF;
            echo $form;
        }

        return false;
    }

    public function prepareAction()
    {
        $db = Com_DB::get('test');

        $db->query('asdfasdfasd');
    }

    public function arrayFilterAction()
    {
        $a = array(
            'a' => '',
            'b' => 'asd',
            'c' => array(
                'dd' => 1,
                'ee' => array(),
                'ff' => array(
                    'tt' => null,
                    'oo' => 'xx',
                ),
                'gg' => 0,
                'hh' => false,
            ),
        );

        var_dump(Helper_Array::filter($a));
    }

    public function nullAction()
    {
        $db = Com_DB::get('test');

        $setArr = array(
            'title' => '',
            'content' => uniqid(),
        );
        var_dump($db->update('my_blog', $setArr, 'id=37'));
    }

    public function doLockAction()
    {
        $key = 'testlock';

        if ($this->_lock->doLock($key)) {
            echo uniqid();
            echo '<br />';
            sleep(5);
            $this->_lock->doUnlock($key);
        }
    }

    public function isLockAction()
    {
        $key = 'testlock';
        var_dump($this->_lock->isLocked($key));
    }

    public function xEncryptAction()
    {
        var_dump(xEncrypt('/index/index'));
    }

    public function xDecryptAction()
    {
        var_dump(xDecrypt($this->getx('url')));
    }
}