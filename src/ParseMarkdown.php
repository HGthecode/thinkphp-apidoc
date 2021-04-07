<?php


namespace hg\apidoc;

use think\facade\App;

trait ParseMarkdown
{

    protected function renderDocs($docs){
        $docData = [];
        if (!empty($docs) && count($docs)>0){
            foreach ($docs as $item){

                if (!empty($item['items']) && count($item['items'])>0){
                    // 有子级
                    $docList = [];
                    foreach ($item['items'] as $doc){
                        if (!empty($doc['path'])){
                            $doc['content'] = $this->renderContent($doc['path']);
                            $doc['type']='md';
                            if (!empty($doc['content'])){
                                $doc['menu_key']="md_".mt_rand(10000,99999);
                                $docList[]=$doc;
                            }
                        }
                    }
                    $docData[]=[
                        'title'=>$item['title'],
                        'children'=>$docList,
                        'group'=>'markdown_doc',
                        'menu_key'=>"md_group_".mt_rand(10000,99999)
                    ];
                }else if(!empty($item['path'])){
                    $item['content'] =  $this->renderContent($item['path']);
                    $item['type']='md';
                    if (!empty($item['content'])){
                        $item['menu_key']="md_".mt_rand(10000,99999);
                        $docData[]=$item;
                    }
                }
            }
        }
        return $docData;
    }

    protected function renderContent($path){
        $mdPath = (new Utils())->replaceCurrentAppTemplate($path,$this->currentApps);
        $filePath =App::getRootPath().$mdPath.'.md';
        $contents = Utils::getFileContent($filePath);
        return $contents;
    }
}