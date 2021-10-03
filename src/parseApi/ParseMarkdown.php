<?php
declare(strict_types = 1);

namespace hg\apidoc\parseApi;

use think\facade\App;
use hg\apidoc\Utils;
use think\facade\Config;
use think\facade\Lang;

class ParseMarkdown
{
    protected $config = [];

    public function __construct()
    {
        $this->config = Config::get('apidoc')?Config::get('apidoc'):Config::get('apidoc.');
    }

    /**
     * 获取md文档菜单
     * @return array
     */
    public function getDocsMenu(): array
    {
        $config  = $this->config;
        $docData = [];
        if (!empty($config['docs']) && count($config['docs']) > 0) {
            $docData = $this->handleDocsMenuData($config['docs']);
        }
        return $docData;
    }

    /**
     * 处理md文档菜单数据
     * @param array $menus
     * @return array
     */
    protected function handleDocsMenuData(array $menus): array
    {
        $list = [];
        foreach ($menus as $item) {
            $item['title']     = Utils::getLang($item['title']);
            if(!empty($item['path'])){
                $lang = Lang::getLangSet();
                $item['path'] = Utils::replaceTemplate($item['path'],['lang'=>$lang]);
            }
            if (!empty($item['children']) && count($item['children']) > 0) {
                $item['children']    = $this->handleDocsMenuData($item['children']);
                $item['menu_key'] = Utils::createRandKey("md_group");
            } else {
                $item['type']     = 'md';
                $item['menu_key'] = Utils::createRandKey("md");
            }
            $list[]           = $item;
        }
        return $list;
    }


    /**
     * 获取md文档内容
     * @param string $appKey
     * @param string $path
     * @return string
     */
    public function getContent(string $appKey, string $path,$lang="")
    {
        if (!empty($appKey)){
            $currentApps = (new Utils())->getCurrentApps($appKey);
            $fullPath      = (new Utils())->replaceCurrentAppTemplate($path, $currentApps);
        }else{
            $fullPath = $path;
        }
        $fullPath = Utils::replaceTemplate($fullPath,[
            'lang'=>$lang
        ]);

        if (strpos($fullPath, '#') !== false) {
            $mdPathArr = explode("#", $fullPath);
            $mdPath=$mdPathArr[0];
            $mdAnchor =$mdPathArr[1];
        } else {
            $mdPath = $fullPath;
            $mdAnchor="";
        }
        $fileSuffix = "";
        if (strpos($fullPath, '.md') === false) {
            $fileSuffix = ".md";
        }
        $filePath    = App::getRootPath() . $mdPath . $fileSuffix;
        $contents    = Utils::getFileContent($filePath);
        // 获取指定h2标签内容
        if (!empty($mdAnchor)){
            if (strpos($contents, '## ') !== false) {
                $contentArr = explode("\r\n", $contents);
                $contentText = "";
                foreach ($contentArr as $line){
                    $contentText.="\r\n".trim($line);
                }
                $contentArr = explode("\r\n## ", $contentText);
                $content="";
                foreach ($contentArr as $item){
                    $itemArr = explode("\r\n", $item);
                    if (!empty($itemArr) && $itemArr[0] && $mdAnchor===$itemArr[0]){
                        $content = str_replace($itemArr[0]."\r\n", '', $item);
                        break;
                    }
                }
                return $content;
            }
        }
        return $contents;
    }


}