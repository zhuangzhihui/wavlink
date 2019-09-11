<?php
/**公共控制器
 * Created by PhpStorm.
 * User: guo
 * Date: 2017/10/31
 * Time: 10:22
 */

namespace app\wavlink\controller;

use app\common\model\BaseModel;
use app\common\model\Language as LanguageModel;
use app\wavlink\validate\Mark;
use app\wavlink\validate\ParamMustBePositiveInt;
use think\Controller;
use think\Request;
use think\Session;

class BaseAdmin extends Controller
{
    protected $currentLanguage;
    protected $currentUser;
    protected $beforeActionList = [
        'isLogin', 'Auth'
    ];

    /**
     * BaseAdmin constructor.
     * @param Request|null $request
     */
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
    }

    /***
     */
    public function _initialize()
    {
        parent::_initialize();
        $userSession = session('userName', '', 'admin');
        $this->currentLanguage = session('current_language', 'admin');
        $mangerName = $userSession->name;
        $username = $userSession->username;
        $this->assign('mangerName', $mangerName);
        $this->assign('username', $username);
    }

    /***
     * //判定是否登录
     * 20190911
     *
     */
    public function isLogin()
    {
        if (Session::has('userName', 'admin')) {
            $session = Session::get('userName', 'admin');
            $this->currentUser = $session;
            $this->assign('session', $session);
        } else {
            $next = Request::instance()->url(true);
            $this->redirect(url('login/index', ["next" => $next]));
        }
    }

    public function Auth()
    {
        $userSession = session('userName', '', 'admin');
        $mangerName = $userSession->name;
        $username = $userSession->username;
        $this->assign('mangerName', $mangerName);
        $this->assign('username', $username);
        $auth = new Auth();
        $request = Request::instance();
        $con = $request->controller();
        $action = $request->action();
        $name = $con . '/' . $action;
        $uid = $userSession->id;
        $notCheck = array('Index/index', 'Content/index', 'Service/index', 'System/index');//对一些（控制器/方法）不需要验证
        //权限部分
        if ($uid != 1) {
            $_access = $auth->getAuthById($uid);
            $this->assign('access', $_access);
            if (!in_array($name, $notCheck)) {
                if (!$auth->check($name, $uid)) {
                    return show(0, 'Sorry!没权限操作', '', '', $_SERVER['HTTP_REFERER'], '');
//                    $this->error('Sorry,没有权限.', url('index/index'));
                }
            }
            $language_ids = $userSession->language_id;
            $languages = LanguageModel::getLanguageByIDs($language_ids);
        } else {
            //获取全部语言
            $languages = LanguageModel::all(['status' => 1]);
            $rules = $auth->getAllAuth();
            $this->assign('access', $rules);
        }
        $this->assign('uid', $uid);
        $this->assign('languages', $languages);
    }


    /**
     * @return array
     * 写的一个状态值共用的方法，
     * 启用 status = 1
     * 回收 status = -1
     * 下架 status = 0
     */
    public function byStatus()
    {
        $data = input('get.');

        $validate = Validate('Admin');
        if (!$validate->scene('status')->check($data)) {
            $this->error($validate->getError());
        }
        //获取控制器
        $model = request()->controller();
        $res = model($model)->save(['status' => $data['status']], ['id' => $data['id']]);
        if ($res) {
            return show(1, "success", '', '', '', '操作成功');
        } else {
            return show(0, 'error', '', '', '', '操作失败');
        }
    }

    /**
     * 编辑后更新数据操作
     * @param $data
     * @return array
     */
    public function update($data)
    {

        //自动获取对应控制器
        $model = request()->controller();
        $res = model($model)->allowField(true)->save($data, ['id' => $data['id']]);
        if ($res) {
            return show(1, "success", '', '', '', '操作成功');
        } else {
            return show(0, 'error', '', '', '操作失败');
        }
    }

    //置顶，上移，下移，置顶操作。需要的数据 type操作类型，语言id，需要移动的数据id
    public static function order($data, $map2 = '')
    {
        (new Mark())->goCheck('type');
        (new ParamMustBePositiveInt())->goCheck();
        $con = request()->controller();
        $res = BaseModel::listorder($data, $con, $map2);
        $url = $_SERVER['HTTP_REFERER'];
        if ($res == 1) {
            return show(1, '', '', '', $url, '操作成功');
        } elseif ($res == 0) {
            return show(0, 'error', '', '', '', '操作失败');
        } elseif ($res == -1) {
            return show(-1, 'error', '', '', '', '嗷呜~到极限了哇！,点别的。');
        } else {
            return show(0, 'error', 'error', '', '', '操作失败');
        }
    }

    //严格校验 传递的参数必须是正整数
    //防止在地址栏随意修改参数
    protected function MustBePositiveInteger($value)
    {
        if (is_numeric($value) && is_int($value + 0) && ($value + 0) > 0) {
            return $value;
        }
        $this->error('别乱改参数');
    }

    //批量更新listorder
    public function allRecycle(Request $request)
    {
        $ids = $request::instance()->post();
        if (!is_array($ids)) {
            return show(0, '', '', '', '', '数据错误，没有选择');
        }
        $con = request()->controller();
        try {
            foreach ($ids as $k => $v) {
                if (model($con)->get($k)) {
                    model($con)->where('id', $k)->update(['status' => -1]);
//                    model($con)->where('id', $k)->update(['listorder' => $k+100]);
                } else {
                    return show('0', 'error', '', '', '', '操作失败');
                }
            }
            return show(1, 'success', '', '', '', '批量回收成功');
        } catch (\Exception $e) {
            return show(0, 'error', '', '', '', $e->getMessage());
        }
    }
}