<?php

/**
 * Data Access Object
 *
 * @author JiangJian <silverd@sohu.com>
 * $Id: Dao.php 293 2012-11-06 01:41:03Z jiangjian $
 */

class Com_Dao
{
    /**
     * 当前库名
     *
     * @var string
     */
    protected $_dbName;

    /**
     * 当前表名
     *
     * @var string
     */
    protected $_tableName;

    /**
     * 主键
     *
     * @var string
     */
    protected $_primaryKey = 'id';
    protected $_primaryKeyType = 'int';

    /**
     * 可选属性，用于 pairs()
     *
     * @var string
     */
    protected $_nameField = 'name';

    /**
     * Db 连接实例
     *
     * @var Com_DB_PDO
     */
    protected $_db;

    /**
     * 获取 Db 连接实例
     *
     * @return Com_DB_PDO
     */
    protected function _db()
    {
        if ($this->_db === null) {
            if (!$this->_dbName) {
                throw new Core_Exception_Fatal(get_class($this) . ' 没有定义 $_dbName，无法使用 Com_Dao');
            }
            if (!$this->_tableName) {
                throw new Core_Exception_Fatal(get_class($this) . ' 没有定义 $_tableName，无法使用 Com_Dao');
            }
            if (!$this->_primaryKey) {
                throw new Core_Exception_Fatal(get_class($this) . ' 没有定义 $_primaryKey，无法使用 Com_Dao');
            }
            $this->_db = Com_DB::get($this->_dbName);
        }

        return $this->_db;
    }

    /**
     * 设置哈希库名
     *
     * @return $this
     */
    public function hashDb($hashKey)
    {
        $this->_dbName = Com_DB_Hash::dbName($this->_dbName, $hashKey);

        return $this;
    }

    /**
     * 设置哈希表名
     *
     * @return $this
     */
    public function hashTable($hashKey)
    {
        $this->_tableName = Com_DB_Hash::tableName($this->_tableName, $hashKey);

        return $this;
    }

    /**
     * 检查主键合法性
     *
     * @param mixed $pk
     * @return bool
     */
    protected function _checkPk($pk)
    {
        switch ($this->_primaryKeyType) {
            case 'bigint':
                $pk = xintval($pk);
                break;
            case 'string':
                $pk = trim($pk);
                break;
            case 'int':
            default:
                $pk = intval($pk);
                break;
        }

        return $pk ? true : false;
    }

    /**
     * 获取一条
     *
     * @param mixed $whereArr
     * @param string $fields
     * @param string $orderBy
     * @return array
     */
    public function row($whereArr, $fields = '*', $orderBy = null)
    {
        if ($orderBy !== null) {
            $orderBy = 'ORDER BY ' . $orderBy;
        }

        $whereSql = Com_DB::getWhereSql($whereArr);
        return $this->_db()->fetchRow("SELECT {$fields} FROM `{$this->_tableName}` WHERE {$whereSql} {$orderBy}");
    }

    /**
     * 获取一条（根据主键）
     *
     * @param mixed $pk
     * @return array
     */
    public function rowByPk($pk)
    {
        if (!$this->_checkPk($pk)) {
            return false;
        }

        return $this->row(array($this->_primaryKey => $pk));
    }

    /**
     * rowByPk 的别名
     *
     * @param mixed $pk
     * @return array
     */
    public function get($pk)
    {
        return $this->rowByPk($pk);
    }

    /**
     * 获取一个单元格
     *
     * @param string $field
     * @param mixed $whereArr
     * @param string $orderBy
     * @return mixed
     */
    public function one($field, $whereArr, $orderBy = null)
    {
        if ($orderBy !== null) {
            $orderBy = 'ORDER BY ' . $orderBy;
        }

        $whereSql = Com_DB::getWhereSql($whereArr);
        return $this->_db()->fetchOne("SELECT {$field} FROM `{$this->_tableName}` WHERE {$whereSql} {$orderBy}");
    }

    /**
     * 获取一个单元格（根据主键）
     *
     * @param string $field
     * @param mixed $pk
     * @return mixed
     */
    public function oneByPk($field, $pk)
    {
        if (!$this->_checkPk($pk)) {
            return false;
        }

        return $this->one($field, array($this->_primaryKey => $pk));
    }

    /**
     * 获取一列
     *
     * @param string $field
     * @param mixed $whereArr
     * @param string $orderBy
     * @return array
     */
    public function col($field, $whereArr = array(), $orderBy = null)
    {
        if ($orderBy !== null) {
            $orderBy = 'ORDER BY ' . $orderBy;
        }

        $whereSql = Com_DB::getWhereSql($whereArr);
        return $this->_db()->fetchCol("SELECT {$field} FROM `{$this->_tableName}` WHERE {$whereSql} {$orderBy}");
    }

    /**
     * 获取条数 COUNT
     *
     * @param mixed $whereArr
     * @param array $params
     * @return array
     */
    public function count($whereArr = array(), $params = array())
    {
        $whereSql = Com_DB::getWhereSql($whereArr);
        return $this->_db()->fetchOne("SELECT COUNT(0) FROM `{$this->_tableName}` WHERE {$whereSql}", $params);
    }

    /**
     * 获取列表
     *
     * @param mixed $whereArr
     * @param string $fields
     * @param string $fetchMethod
     * @return array
     */
    public function find($whereArr = array(), $orderBy = null, $fields = '*', $fetchMethod = 'fetchAll')
    {
        if ($orderBy !== null) {
            $orderBy = 'ORDER BY ' . $orderBy;
        }

        $whereSql = Com_DB::getWhereSql($whereArr);
        return $this->_db()->$fetchMethod("SELECT {$fields} FROM `{$this->_tableName}` WHERE {$whereSql} {$orderBy}");
    }

    /**
     * 相当于 find 取所有数据
     *
     * @return array
     */
    public function all()
    {
        return $this->find();
    }

    /**
     * 获取列表（分页）
     *
     * @param mixed $whereArr
     * @param int $start
     * @param int $pageSize
     * @param string $orderBy
     * @param string $fields
     * @return array
     */
    public function findByPage($whereArr = array(), $start = 0, $pageSize = 0, $orderBy = null, $fields = '*', $fetchMethod = 'fetchAll')
    {
        if ($orderBy !== null) {
            $orderBy = 'ORDER BY ' . $orderBy;
        }

        $whereSql = Com_DB::getWhereSql($whereArr);
        return $this->_db()->limitQuery("SELECT {$fields} FROM `{$this->_tableName}` WHERE {$whereSql} {$orderBy}", $start, $pageSize, $fetchMethod);
    }

    /**
     * 获取键值对
     *
     * @param mixed $whereArr
     * @return array
     */
    public function pairs($whereArr = array())
    {
        if (!$this->_nameField) {
            return null;
        }

        $whereSql = Com_DB::getWhereSql($whereArr);
        return $this->_db()->fetchPairs("SELECT `{$this->_primaryKey}`, `{$this->_nameField}` FROM `{$this->_tableName}` WHERE {$whereSql}");
    }

    /**
     * 根据id获取名字（可批量）
     *
     * @param int/array $pk
     * @return string
     */
    public function name($pk)
    {
        if (!$pk || !$this->_nameField) {
            return null;
        }

        if (is_array($pk)) {
            return $this->_db()->fetchPairs("SELECT `{$this->_primaryKey}`, `{$this->_nameField}` FROM `{$this->_tableName}`
                WHERE {$this->_primaryKey} IN (" . ximplode($pk) . ')');
        } else {
            return $this->_db()->fetchOne("SELECT `{$this->_nameField}` FROM `{$this->_tableName}` WHERE {$this->_primaryKey} = '{$pk}'");
        }
    }

    /**
     * 直接映射DB类中的方法
     *
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        $methods = array(
            'insert'      => 1, // 插入
            'replace'     => 1, // 替换
            'batchInsert' => 1, // 批量插入/替换
            'update'      => 1, // 更新
            'delete'      => 1, // 删除
            'increment'   => 1, // 自增
            'decrement'   => 1, // 自减
            'decrementx'  => 1, // 自减（保证不小于0）
        );

        if (!isset($methods[$method])) {
            throw new Core_Exception_Fatal('Call to undefined method Com_Dao::' . $method);
        }

        // 给 $args 左侧插入第一个参数：表名
        array_unshift($args, $this->_tableName);

        return call_user_func_array(array($this->_db(), $method), $args);
    }

    /**
     * 更新（根据主键）
     *
     * @param array $setArr
     * @param mixed $pk
     * @return bool/int
     */
    public function updateByPk($setArr, $pk)
    {
        if (!$this->_checkPk($pk) || !$setArr) {
            return false;
        }

        return $this->_db()->update($this->_tableName, $setArr, array($this->_primaryKey => $pk));
    }

    /**
     * 删除（根据主键）
     *
     * @param mixed $pk
     * @return bool
     */
    public function deleteByPk($pk)
    {
        if (!$this->_checkPk($pk)) {
            return false;
        }

        return $this->_db()->delete($this->_tableName, array($this->_primaryKey => $pk));
    }

    /**
     * 直接调用 Com_DB_PDO 中的原始方法
     *
     * @return mixed
     */
    public function query()
    {
        $args   = func_get_args();
        $method = array_shift($args);
        return call_user_func_array(array($this->_db(), $method), $args);
    }
}