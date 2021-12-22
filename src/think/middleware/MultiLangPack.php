<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2021 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace think\middleware;

use Closure;
use think\App;
use think\Lang;
use think\Request;
use think\Response;

/**
 * 多语言加载
 */
class MultiLangPack
{
    protected $app;

    protected $lang;

    public function __construct(App $app, Lang $lang)
    {
        $this->app = $app;
        $this->lang = $lang;
    }

    /**
     * 路由初始化（路由规则注册）
     * @access public
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle($request, Closure $next)
    {
        // 自动侦测当前语言
        $langset = $this->lang->detect($request);

        $controller = str_replace('._', '/', parse_name($request->controller()));

        //控制器语言
        $langPath = $this->app->getAppPath() . 'lang' . DIRECTORY_SEPARATOR . $langset . DIRECTORY_SEPARATOR . $controller . '.php';
        if (is_file($langPath)) {
            $this->lang->load($langPath);
        }

        return $next($request);
    }
}
