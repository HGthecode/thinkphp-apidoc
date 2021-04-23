<?php
declare(strict_types = 1);

namespace hg\apidoc\parseApi;


use hg\apidoc\Utils;
use think\facade\Config;

class CacheApiData
{
    protected $config = [];

    public function __construct()
    {
        $this->config = Config::get('apidoc');
    }

    /**
     * 获取缓存目录
     * @param string $appKey
     * @return string
     */
    protected function getCacheFolder(string $appKey):string
    {
        $config         = $this->config;
        $currentApps    = (new Utils())->getCurrentApps($appKey);
        $configPath     = !empty($config['cache']) && !empty($config['cache']['path']) ? $config['cache']['path'] : '../runtime/apidoc/';
        $cacheAppFolder = "";
        if (!empty($currentApps) && count($currentApps) > 0) {
            foreach ($currentApps as $keyIndex => $appItem) {
                $cacheAppFolder .= $appItem['folder'] . "/";
            }
        }
        $cacheFolder = $configPath . $cacheAppFolder;
        return $cacheFolder;
    }

    /**
     * 获取指定目录下缓存文件名列表
     * @param string $folder
     * @return array
     */
    public function getCacheFileList(string $folder):array
    {
        $filePaths  = glob($folder . '*.json');
        $cacheFiles = [];
        if (count($filePaths) > 0) {
            foreach ($filePaths as $item) {
                $cacheFiles[] = str_replace(".json", "", basename($item));
            }
        }
        return $cacheFiles;
    }


    /**
     * 获取接口缓存数据
     * @param string $appKey
     * @param string $cacheFileName
     * @return array|false
     */
    public function get(string $appKey, string $cacheFileName)
    {
        $cacheFolder   = $this->getCacheFolder($appKey);
        $cacheFileList = $this->getCacheFileList($cacheFolder);
        if (!file_exists($cacheFolder)) {
            return false;
        }
        if (empty($cacheFileName) && count($cacheFileList) > 0) {
            // 默认最后一个缓存文件
            $cacheFileName = $cacheFileList[count($cacheFileList) - 1];
        }
        $cacheFilePath = $cacheFolder . "/" . $cacheFileName . '.json';
        if (file_exists($cacheFilePath)) {
            // 存在缓存文件
            $fileContent = file_get_contents($cacheFilePath);
            if (empty($fileContent)) {
                return false;
            }
            $json = json_decode($fileContent);
            if (is_object($json)) {
                $json = [
                    "data"   => $json->data,
                    "tags"   => $json->tags,
                    "groups" => $json->groups,
                ];
            }
            return [
                'name' => $cacheFileName,
                'data' => $json,
                'list' => $cacheFileList
            ];
        }
        return false;
    }

    /**
     * 设置接口缓存
     * @param string $appKey
     * @param array $json
     * @return array|false
     */
    public function set(string $appKey, array $json):array
    {
        if (empty($json)) {
            return false;
        }
        $config      = $this->config;
        $fileName    = date("Y-m-d H_i_s");
        $fileContent = json_encode($json);
        $cacheFolder = $this->getCacheFolder($appKey);
        $path        = $cacheFolder . $fileName . ".json";
        Utils::createFile($path, $fileContent);
        $filePaths = $this->getCacheFileList($cacheFolder);
        if ($config['cache']['max'] && count($filePaths) >= $config['cache']['max']) {
            //达到最大数量，删除第一个
            $filePath = $cacheFolder . $filePaths[0] . ".json";
            Utils::delFile($filePath);
        }
        return [
            "name" => $fileName,
            "data" => $json,
            "list" => $this->getCacheFileList($cacheFolder)
        ];
    }
}