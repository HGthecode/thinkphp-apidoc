<?php
declare(strict_types = 1);

namespace hg\apidoc\parseApi;

use think\facade\App;
use hg\apidoc\Utils;
use think\facade\Config;

class ParseMarkdown
{
    protected $config = [];

    public function __construct()
    {
        $this->config = Config::get('apidoc');
    }

    /**
     * 获取md文档菜单
     * @return array
     */
    public function getDocsMenu(): array
    {
        $config  = $this->config;
        $docData = [];
        if (!empty($config['docs']) && !empty($config['docs']['menus']) && count($config['docs']['menus']) > 0) {
            $docData = $this->handleDocsMenuData($config['docs']['menus']);
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
            if (!empty($item['items']) && count($item['items']) > 0) {
                $item['items']    = $this->handleDocsMenuData($item['items']);
                $item['group']    = 'markdown_doc';
                $item['menu_key'] = "md_group_" . mt_rand(10000, 99999);
                $list[]           = $item;
            } else {
                $item['type']     = 'md';
                $item['menu_key'] = "md_" . mt_rand(10000, 99999);
                $list[]           = $item;
            }
        }
        return $list;
    }


    /**
     * 获取md文档内容
     * @param string $appKey
     * @param string $path
     * @return string
     */
    public function getContent(string $appKey, string $path): string
    {
        $currentApps = (new Utils())->getCurrentApps($appKey);
        $mdPath      = (new Utils())->replaceCurrentAppTemplate($path, $currentApps);
        $filePath    = App::getRootPath() . $mdPath . '.md';
        $contents    = Utils::getFileContent($filePath);
        return $contents;
    }
}