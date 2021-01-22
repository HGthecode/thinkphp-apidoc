<?php


namespace hg\apidoc;


trait ParseMarkdown
{

    protected function renderDocs($docs,$version){
        $docData = [];
        if (!empty($docs) && count($docs)>0){
            foreach ($docs as $item){

                if (!empty($item['items']) && count($item['items'])>0){
                    // 有子级
                    $docList = [];
                    foreach ($item['items'] as $doc){
                        if (!empty($doc['path'])){
                            $doc['content'] = $this->renderContent($doc['path'],$version);
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
                    $item['content'] =  $this->renderContent($item['path'],$version);
                    $item['type']='md';
                    if (!empty($item['content'])){
                        $docData[]=$item;
                    }
                }
            }
        }
        return $docData;
    }

    protected function renderContent($path,$version){
        $mdPath = $path;
        if (!empty($path) && strpos($path,'{:version}') !== false){
            $mdPath = str_replace("{:version}",$version,$path);
        }
        $filePath = $this->app->getRootPath().$mdPath.'.md';
        if (file_exists($filePath)){
            $handle = fopen($filePath, "r");
            $contents = fread($handle, filesize ($filePath));
            fclose($handle);
        }else if (empty($path) || strpos($path,'{:version}') === false){
            $contents="[file 404]".$mdPath;
        }else{
            $contents = '';
        }
        return $contents;
    }
}