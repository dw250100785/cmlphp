<?php
/* * *********************************************************
 * [cml] (C)2012 - 3000 cml http://cmlphp.51beautylife.com
 * @Author  linhecheng<linhechengbush@live.com>
 * @Date: 14-2-211 下午2:23
 * @version  2.5
 * cml框架 无限级分类
 * *********************************************************** */

namespace Cml\Vendor;

/**
 * 无限级分类处理
 *
 * @package Cml\Vendor
 */
class Tree
{
    /**
     * @var array 默认配置
     */
    private static $config = array(
        'pid' => 'pid', //低级id字段名
        'id' => 'id', //主键字段名
        'name' => 'name' //名称字段名
    );

    /**
     * @var int 当前为第几层树
     */
    private static $times = 0;

    /**
     * 修改配置
     *
     * @param  array $config array('pid'=>'', 'id' => '', 'name' =>'name')
     *
     * @return mixed
     */
    public static function setConfig($config = array())
    {
        if (!is_array($config)) return false;
        self::$config = array_merge(self::$config, $config);
        return true;
    }

    /**
     * 获取树--返回格式化后的数据
     *
     * @param  array $list 数据列表数组
     * @param  int   $pid  初始化树时候，代表获取pid下的所有子集
     * @param  int   $selectedId  选中的ID值
     * @param  string  $str  组装后的字串
     * @param  string  $prefix  前缀
     * @param  string  $selectedString  选中时的字串 如selected checked
     *
     * @return string|array
     */
    public static function getTree($list, $pid = 0, $selectedId = 0, $str = "<option value='\$id' \$selected>\$tempPrefix\$name</option>", $prefix = '|--', $selectedString = 'selected')
    {
        if (!is_array($list)) { //遍历结束
            self::$times = 0;
            return '';
        }
        $string = $tempPrefix = '';
        self::$times += 1;
        for ($i=0; $i < self::$times; $i++) {
            $tempPrefix .= $prefix;
        }

        foreach ($list as $v) {
            if ($v[self::$config['pid']] == $pid) { //获取pid下的子集
                $id = $v[self::$config['id']]; //主键id
                $name = $v[self::$config['name']]; //显示的名称
                $selected = ($id == $selectedId) ? $selectedString : ''; //被选中的id
                $tempCode = '';
                eval("\$tempCode = \"{$str}\";");//转化
                $string .=  $tempCode;
                $string .=  self::getTree($list, $v[self::$config['id']], $selectedId, $str, $prefix, $selectedString);
            }
        }
        return $string ;
    }

    /**
     * 获取树--返回数组
     *
     * @param  array $list 数据列表数组
     * @param  int   $pid  初始化树时候，代表获取pid下的所有子集
     *
     * @return string|array
     */
    public static function getTreeNoFormat(&$list, $pid = 0)
    {
        $res = array();
        if (!is_array($list)) { //遍历结束
            return $res;
        }

        foreach ($list as $v) {
            if (isset($v[self::$config['pid']]) && $v[self::$config['pid']] == $pid) { //获取pid下的子集
                $v['sonNode'] =  self::getTreeNoFormat($list, $v[self::$config['id']]);
                $res[$v[self::$config['id']]] = $v;
            }
        }
        return $res ;
    }

    /**
     * 获取子集
     *
     * @param  array $list 树的数组
     * @param  int   $id   父类ID
     *
     * @return string|array
     */
    public static function getChild($list, $id)
    {
        if (!is_array($list)) return array();
        $temp = array();
        foreach ($list as $v) {
            if ($v[self::$config['pid']] == $id) {
                $temp[] = $v;
            }
        }
        return $temp;
    }

    /**
     * 获取父集
     *
     * @param  array $list 树的数组
     * @param  int   $id   子集ID
     *
     * @return string|array
     */
    public static function getParent($list, $id)
    {
        if (!is_array($list)) return array();
        $temp = array();
        foreach ($list as $v) {
            $temp[$v[self::$config['id']]] = $v;
        }
        $parentid = $temp[$id][self::$config['pid']];
        return $temp[$parentid];
    }
}
