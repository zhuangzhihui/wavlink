<?php
/**
 * Created by PhpStorm.
 * User: guo
 * Date: 2017/8/28
 * Time: 10:00
 *内容管理之 产品管理验证规则。
 */

namespace app\wavlink\validate;
/**
 * Class Product
 * @package app\wavlink\validate
 *
 */
class Product extends BaseValidate
{
    /**验证规则**/
    protected $rule = [
        'id' => 'number',
        'name' => 'require|max:255|unique:product,name',
        'model' => 'require|unique:product,model|max:128',
        'seo_title' => 'require|max:128',
        'keywords' => 'require|max:128',
        'description' => 'require|max:255',
        'image_litpic_url' => 'max:500',
        'image_pc_url' => 'max:500',
        'image_mobile_url' => 'max:500',
        'language_id' => 'require|number|gt:0',
        'cates' => 'require',
        'status' => 'number|in:-1,0,1',
    ];
    protected $message = [
        'id' => 'id不合法',
        'name.require' => '产品名称不能为空',
        'name.unique' => '产品名称不能重复',
        'name.max' => '产品名称太长',
        'model.require' => '产品型号不能为空',
        'model.unique' => '产品型号不能重复',
        'model.max' => '产品型号不能太长',
        'seo_title.require' => 'SEO标题不能为空',
        'seo_title.max' => 'SEO标题不能太长',
        'keywords.require' => '关键词不能为空',
        'keywords.max' => '关键词不能太长',
        'description.require' => '描述不能为空',
        'description.max' => '描述太长了',
        'image_litpic_url' => '缩略图外链地址太长',
        'image_pc_url' => 'PC封面外链地址太长',
        'image_mobile_url' => '手机端封面外链地址太长',
        'language_id' => '语言不能为空',
        'cates' => '分类是不是没有选择',
        'status.number' => '状态必须是数字',
        'status.in' => '状态范围不合法',
    ];
    /**场景设置**/
    protected $scene = [
        'add' => ['name', 'model', 'seo_title', 'keywords', 'description', 'image_litpic_url', 'image_pc_url', 'image_mobile_url', 'language_id', 'cates', 'status'],
        'edit' => ['id', 'name', 'model', 'seo_title', 'keywords', 'description', 'image_litpic_url', 'image_pc_url', 'image_mobile_url', 'language_id', 'cates', 'status']
    ];
}
