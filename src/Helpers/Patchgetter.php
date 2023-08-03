<?php

namespace App\Helpers;

class Patchgetter {

    private string $inputData = "";
    private array $dictionaryData = [];

    public function __construct() {
        $this->inputData = file_get_contents('php://input');
        $this->getIndex();
        $this->getFiles();
    }

    private function getIndex():void{
        preg_match_all('/name="\w+"\s+.+/', $this->inputData, $names);
        foreach($names[0] as $value){
            preg_match('/"\w+"/', $value, $name);
            preg_match('/\s+.+/', $value, $val);
            $value = preg_replace('/----------------------------\d+/', '', $val[0]);
            $this->dictionaryData[str_replace('"', "", $name[0])] = trim($value);
        }
    }

    private function getFiles(): void{
        preg_match('/filename=".+?"/', $this->inputData, $mat);
        $file_data = explode('----------------------------', $this->inputData)[1];
        if(!empty($mat)){
            $file_metadata = explode(';', preg_replace('/\n/', ';', $file_data));
            preg_match('/"\w+"/', $file_metadata[2], $field_name);
            preg_match('/"\w+.+/', $file_metadata[3], $file_name);
            preg_match('/\w+\/\w+/', $file_metadata[4], $mime);
            $filename = trim(str_replace('"', "", $file_name[0]));
            $data = explode("Content-Type: ".$mime[0], $file_data)[1];
            $file = trim($data);
            $this->dictionaryData[str_replace('"', "", $field_name[0])] =  $this->generateFile($file, $mime[0], $filename);
        }
    }

    private function generateFile(string $filedata, string $mime, string $filename):array{
        $name = null;
        $size = 0;
        if($filename!=""){
            $name = hash("md5", $filename).".".explode('.', $filename)[1];
        }
        $size = file_put_contents(__DIR__.DIRECTORY_SEPARATOR.'src/'.$name, $filedata, LOCK_EX);
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