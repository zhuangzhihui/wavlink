<?php
/**
 * @Create by PhpStorm
 * @author:jinxiu89@163.com
 * @Create Date:2020/4/15 17:34
 * @User: admin
 * @Current File : Common.php
 * @格言：溪涧岂能留得住，终归大海做波涛 --李忱
 * @格言： 我的内心因看见大海而波涛汹涌
 **/

namespace app\en_us\controller;


use app\common\service\en_us\About as aboutService;
use think\App;
use think\captcha\Captcha;
use think\Controller;
use think\facade\Config;
use think\facade\Cookie;
use think\facade\Env;
use think\Response;
use think\response\Redirect;

/**
 * Class Common
 * @package app\en_us\controller
 */
class Common extends Controller
{
    protected $lang;

    public function __construct(App $app = null)
    {
        parent::__construct($app);
        $this->lang = Cookie::get('lang_var') ? Cookie::get('lang_var') : 'en_us';
    }

    /**
     * @return mixed
     */
    public function driver()
    {
        //TODO::中间跳转页面后面准备做
        return \redirect('/' . $this->lang . '/drivers.html');
    }

    /**
     * @return Redirect
     */
    public function manual()
    {
        //TODO::中间跳转页面后面准备做
        return \redirect('/' . $this->lang . '/manuals.html');
    }

    /**
     * @return mixed
     */
    public function terms()
    {
        $result = (new aboutService())->getArticle('terms_en', $this->code);
        return $this->fetch('', ['result' => $result->toArray()]);
    }

    /**
     * @return mixed
     */
    public function privacy()
    {
        $result = (new aboutService())->getArticle('Privacy', $this->code);
        return $this->fetch('', ['result' => $result->toArray()]);
    }

    /**
     * @return Response
     * 生成验证码
     */
    public function verify()
    {
        $captcha = new Captcha(Config::get('verify.config'));
        return $captcha->entry();
    }

    /**
     * @return string
     */
    public function miss()
    {
        return view(Env::get('APP_PATH') . '/en_us/view/error/404.html', [], $code = 404);
    }
}