<?php namespace lib;
class Fun extends Db {
    //用于存放实例化的对象
    private $_pdo = null;
    public $a = 2;
    function __construct() {
         $this->_pdo =  self::getInstance();
    }

    function add($_tables, Array $_addData) {
        return $this->_pdo->add($_tables, $_addData);
    }

    function select($_tables, Array $_fileld, Array $_param = array()) {
        return $this->_pdo->select($_tables, $_fileld, $_param);
    }
}