<?php
/**
 * @Create by PhpStorm
 * @author:jinxiu89@163.com
 * @Create Date:2020/3/26 15:58
 * @User: admin
 * @Current File : BaseService.php
 * @格言：溪涧岂能留得住，终归大海做波涛 --李忱
 * @格言： 我的内心因看见大海而波涛汹涌
 **/

namespace app\common\service\customer;

use app\common\model\Country;

/**
 * Class BaseService
 * @package app\common\service\customer
 */
class BaseService
{
    protected $model;

    public function create($data)
    {
        try {
            return $this->model->create($data); //返回的是一个当前模型的实例
        } catch (\Exception $exception) {
            return $exception->getMessage();//todo:: 异常
        }
    }
    /**
     * @return array
     */
    public function getCountry(){
        try{
            $data=(new Country())->field('country_id,name')->select();
            return $data->toArray();
        }catch (\Exception $exception){
            return [];
        }
    }

    /**
     * @param $id
     * @return
     */
    public function getDataById($id)
    {
        try {
            return $this->model->get($id);
        } catch (\Exception $exception) {
            //todo 异常处理
        }
    }


    /**
     * @param $data
     * @return mixed
     */
    public function updateData($data)
    {
        try {
            return $this->model->save($data, $data['id']);
        }catch (\Exception $exception){

        }
    }

}