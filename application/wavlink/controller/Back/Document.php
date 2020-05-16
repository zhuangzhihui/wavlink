<?php
/**
 * Created by PhpStorm.
 * User: guo
 * Date: 2017/8/23
 * Time: 10:37
 */

namespace app\wavlink\controller;

use app\wavlink\validate\UrlTitleMustBeOnly;
use think\Facade\Request;
use app\common\model\Document as DocumentModel;
use app\common\model\ServiceCategory as ServiceCategoryModel;
use app\wavlink\validate\Document as DocumentValidate;

/**
 * Class Document
 * @package app\wavlink\controller
 * 20190916
 */
Class Document extends BaseAdmin
{
    /**
     * @return mixed
     * 语言
     */
    public function index()
    {
        $document = DocumentModel::getDataByStatus(1, $this->currentLanguage['id']);
        return $this->fetch('', [
            'document' => $document['data'],
            'counts' => $document['count'],
            'language_id' => $this->currentLanguage['id']
        ]);
    }

    //添加文档页面开发
    public function add()
    {
        //获取语言
        $language_id = $this->MustBePositiveInteger(input('get.language_id'));
        //获取所有分类
        $categorys = ServiceCategoryModel::getSecondCategory($language_id);
        return $this->fetch('', [
            'categorys' => $categorys,
            'language_id' => $language_id,
        ]);
    }

    //文档新增保存操作

    /**
     * @return array|void
     * 修改验证结构
     *
     */
    public function save()
    {
        if (request()->isAjax()) {
            $data=input('post.');
            $validate=new DocumentValidate();
            if(isset($data['id']) || !empty($data['id'])){
                if($validate->scene('edit')->check($data)){
                    return $this->update($data);
                }else{
                    return show(0, '', '', '', '', $validate->getError());
                }
            }else{
                if($validate->scene('add')->check($data)){
                    $res = (new DocumentModel())->add($data);
                    if ($res) {
                        return show(1, '', '', '', '', '添加成功');
                    } else {
                        return show(1, '', '', '', '', '添加失败');
                    }
                }else{
                    return show(0, '', '', '', '', $validate->getError());
                }
            }
        }
    }

    //文档编辑页面
    public function edit($id = 0)
    {
        $id = $this->MustBePositiveInteger($id);
        $document = DocumentModel::get($id);
        //获取所有分类
        $categorys = ServiceCategoryModel::getSecondCategory($this->currentLanguage['id']);
        return $this->fetch('', [
            'document' => $document,
            'categorys' => $categorys,
            'language_id' => $this->currentLanguage['id'],
        ]);
    }

//    //批量文档放入回收站
//    public function allRecycle(Request $request)
//    {
//        $ids = $request::instance()->post();
//        if (!is_array($ids)) {
//            return show(0, '', '传入的不是数组');
//        }
//        try {
//            foreach ($ids as $k => $v) {
//                if (DocumentModel::get($k)) {
////                    model('Document')->where('id', $k)->update(['status' => -1]);
//                    model('Document')->where('id', $k)->update(['listorder' => $k + 50]);
//                } else {
//                    return show(0, '', '回收失败');
//                }
//            }
//            return show(1, '', '批量回收成功');
//        } catch (\Exception $e) {
//            return show(1, '', $e->getMessage());
//        }
//    }

    //回收站数据列表，状态值为-1的
    public function doc_recycle()
    {
        $document = DocumentModel::getDataByStatus(-1);
        return $this->fetch('', [
            'document' => $document['data'],
            'counts' => $document['count'],
        ]);
    }

    /**
     * 排序功能开发
     * 默认 必须数据 id,type,language_id
     **type == 1 时 置底
     * type == 4 时 置顶
     * type == 3 时 上移
     * type == 2 时 下移
     */
    public function listorder()
    {
        if (request()->isAjax()) {
            $data = input('post.'); //id ,type ,language_id
            self::order(array_filter($data));
        } else {
            return show(0, '置顶失败，未知错误', 'error', 'error', '', '');
        }
    }
}