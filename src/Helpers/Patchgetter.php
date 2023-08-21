<?php

namespace App\Helpers;

use Exception;

class Patchgetter {

    private string $inputData = "";
    private array $dictionaryData = [];
    private string $file_path = "";

    /**
     * @method __construct
     * @param path
     * @return void
     * This is the construct, you will need a param called path. This param
     * is the folder where you want the images saved.
     */
    public function __construct(string $path="") {
        $path ?? throw new Exception("Path is required");
        $this->file_path = $path;
        $this->inputData = file_get_contents('php://input');
        //Fallo detectado el split no va correctamente, por lo siguiente:
        // ------WebKitFormBoundaryifMOTjB8tIAB2Z7m\
        $this->getBasicParams();
        $this->getFiles();
    }

    //TODO move method, to put files in a choosen folder.

    /**
     * @method getBasicParams()
     * This method gets all simple params, like text, int ...
     * @return void
     * 
     */

    private function getBasicParams():void{
        preg_match_all('/name="\w+"\s+.+/', $this->inputData, $names);
        foreach($names[0] as $value){
            preg_match('/"\w+"/', $value, $name);
            preg_match('/\s+.+/', $value, $val);
            $value = preg_replace('/------\w+\d+/', '', $val[0]);
            $this->dictionaryData[str_replace('"', "", $name[0])] = trim($value);
        }
    }

    /**
     * @method getFiles()
     * This method make all dirty job to get mime, data, and all base things in files
     * @return void
     */
    private function getFiles(): void{
        preg_match('/filename=".+?"/', $this->inputData, $mat);
        if(!empty($mat)){
            $data = preg_split('/\r\n\r/', $this->inputData);
            $file = array_filter($data, function ($e) {
                if(str_contains($e,'filename')){
                    return $e;
                }
            });
            $fileContent = array_filter($data, function($e){
                if(str_contains($e, 'Exif')){
                    return $e;
                }
            });
            $file_content = trim(array_pop($fileContent));
            $file_metadata = array_pop($file);
            $file_name = trim(preg_replace('/filename|\"|\=/', '', array_pop($mat)));
            preg_match('/name="\w+"/', $file_metadata, $field_name);
            preg_match('/\w+\/\w+/', $file_metadata, $mime);
            $mime = trim(array_pop($mime));
            $field_name = trim(preg_replace('/name|\"|\="/', '', array_pop($field_name)));
            $this->dictionaryData[$field_name] =  $this->generateFile($file_content, $mime, $file_name);
        }
    }

    /**
     * @method generateFile()
     * When you have all params this method provides all information about the file in the php://input
     * @return void
     */
    private function generateFile(string $filedata, string $mime, string $filename):array{
        $name = null;
        $size = 0;
        if($filename!=""){
            $name = hash("md5", $filename).".".explode('.', $filename)[1];
        }
        $size = null;
        if($this->file_path!=''){
            $size = file_put_contents($this->file_path.$name, $filedata, LOCK_EX);
        }else{
            $size = file_put_contents(__DIR__.DIRECTORY_SEPARATOR.'src/'.$name, $filedata, LOCK_EX);
        }
        return [
            "mime" => $mime,
            "filename" => $name,
            "size" => $size,
            "app_path" => __DIR__.DIRECTORY_SEPARATOR.'src/'.$name,
            "file" => "data:$mime;base64,".base64_encode($filedata)
        ];
    }

    public function getRawData():string{
        return $this->inputData;
    }

    public function get():array{
        return $this->dictionaryData;
    }
}

?>