<?php


namespace hg\apidoc;


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
                                $docList[]=$doc;
                            }
                        }
                    }
                    $docData[]=[
                        'title'=>$item['title'],
                        'children'=>$docList,
                        'group'=>'markdown_doc'
                    ];
                }else if(!empty($item['path'])){
                    $item['content'] =  $this->renderContent($item['path']);
                    $item['type']='md';
                    if (!empty($item['content'])){
                        $docData[]=$item;
                    }
                }
            }
        }
        return $docData;
    }

    protected function renderContent($path){
        $mdPath = (new Utils())->replaceCurrentAppTemplate($path,$this->currentApps);
        $filePath = $this->app->getRootPath().$mdPath.'.md';
        $contents = Utils::getFileContent($filePath);
        return $contents;
    }
}