<?php
/**
 * Created by PhpStorm.
 * User: jinxiu89@163.com
 * Date: 2018/12/18
 * Time: 17:05
 */

namespace app\en_us\controller;

use app\common\model\Manual as ManualModel;
use app\common\model\ServiceCategory;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\DbException;
use think\Request;

/**
 * Class Manual
 * @package app\en_us\controller
 */
class Manual extends Base
{
    /**
     * Manual constructor.
     * @param Request|null $request
     */
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        try {
            $cate = ServiceCategory::getSecondCategory($this->code);
            $this->assign('cate', $cate);
        } catch (DataNotFoundException $e) {
        } catch (ModelNotFoundException $e) {
        } catch (DbException $e) {
        }
    }

    /**
     *
     */
    public function _initialize()
    {
        parent::_initialize(); // TODO: Change the autogenerated stub
        $this->assign('language_id', $this->language_id);
    }

    /**
     * @param string $order
     * @return mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @throws \think\Exception
     *
     */
    public function index($order = 'desc')
    {
        //获取manual分类信息
        $parent = ServiceCategory::getTopCategory($this->code, 'manual');
        //根据分类来获取manual
        $data = (new ManualModel())->getManualByCategoryId($this->code, '', $order);
        return $this->fetch($this->template . '/manual/index.html', [
            'count' => $data['count'],
            'data' => $data['result'],
            'parent' => $parent,
            'name' => '',
            'order' => $order
        ]);
    }

    /**
     * @param string $category
     * @param string $order
     * @return \think\response\View
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @throws \think\Exception
     *
     */
    public function category($category = "",$order="desc")
    {
        if (empty($category) || !isset($category)) {
            abort(404);//直接报404 不存在的意思
        }
        $parent = ServiceCategory::getCategoryIdByName($this->code, $category);//Wirless这个分类的数据 array('id'=>52,'name'=>'wirless'
        $nav = ServiceCategory::getNavByCategoryId($this->code, $category);
        $this->assign('path', $nav);
        $this->assign('parent', $parent);
        $this->assign('current',$category);
        $this->assign('order',$order);
        if (empty($parent)) {
            abort(404);
        } else {
            /***
             *1 如果查出来的parent 的level 小于2 那么 他一定还有下一级
             * //pc 这个分类 的level肯定小于等于2
             * //返回他所有的子分类
             * //返回的是这个分类及他以下的所有的说明书数据
             */
            $child = ServiceCategory::getChild($parent['id']);//他没有下一级的情况，下去怎么查？
            $categoryIDS[]=$parent['id'];
            foreach ($child as $item){
                $categoryIDS[]=$item['id'];
            }
            $manual = (new ManualModel())->getManualByCategoryId($this->code, $categoryIDS, $order);
            return view($this->template . '/manual/category.html', [
                'data' => $manual['result'],
                'count'=>$manual['count'],
                'name' => $parent['name'],
            ]);
        }
        abort(404);
    }

    public function details($url_title)
    {
        $result = (new ManualModel())->getDownloadByTitle($this->code, $url_title);
        $url_title = (new ManualModel())->getUrlTitle($result['manual']['category_id']);
        return $this->fetch($this->template . '/manual/details.html', [
            'url_title' => $url_title,
            'manual' => $result['manual'],
            'downloads' => $result['downloads']
        ]);
    }
}