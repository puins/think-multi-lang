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
use think\helper\Str;
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

        $controller = $request->controller();
        //控制器名称搜索及转换
        preg_match('/[A-Z]{1}.*/', $controller, $matches);
        $controller = preg_filter('/[A-Z]{1}.*/', Str::snake($matches[0]), $controller);
        $path = str_replace('.', DIRECTORY_SEPARATOR, $controller);
        $modulePath = str_replace(Str::snake($matches[0]), '', $path);

        //模块公用语言
        $moduleLangPath = $this->app->getAppPath() . 'lang' . DIRECTORY_SEPARATOR . $modulePath . $langset . '.php';
        if (is_file($moduleLangPath)) {
            $this->lang->load($moduleLangPath);
        }

        //控制器语言
        $langPath = $this->app->getAppPath() . 'lang' . DIRECTORY_SEPARATOR . $langset . DIRECTORY_SEPARATOR . $path . '.php';
        if (is_file($langPath)) {
            $this->lang->load($langPath);
        }

        return $next($request);
    }
}
