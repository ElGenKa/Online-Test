<?php
namespace app\forms;
use facade\Json;
use Exception;
use bundle\jurl\jURL;
use std, gui, framework, app;

class testsystem 
{

    var $apiurl;
    var $defurl;
    var $isinit = false;
    var $data = array();
    var $jcurl;
    
    var $systemdata;
    var $testlist;
    var $objectlist;

    public function __construct($apiurl, $defurl){ //Функция инициализации класса
        $this->apiurl = $apiurl;
        $this->defurl = $defurl;
        $this->isinit = true;
    }
    
    public function get_test_list(){ //Функция получения списка тестов
        $this->connect();
        $data = [
            'method' => 'get_tests'
        ];
        $array = $this->send($data);
        
        $this->free_testlist();
        $this->free_testlist();
        
        return $array;
    }
    
    public function free_testlist(){ //Очистка данных о списке тестов
        $test_list = $this->testlist;
        for($i=0;$i<count($test_list);$i++){
            $test_list[$i]->free();            
        }
        unset($this->testlist);
    }
    
    public function free_objectlist(){ //Очистка данных о списке объектов
        $object_list = $this->objectlist;
        for($i=0;$i<count($object_list);$i++){
            $object_list[$i]->free();            
        }
        unset($this->objectlist);
    }
    
    public function connect(){ //Функция подключения к API
        if($this->isinit == true){
            try{ 
                $this->jcurl = new jURL( $this->apiurl );
            } catch (Exception $e) { //Если не смогли подключится
                $msg = "Error: " . $e->getCode() ."/r/n" . $e->getTraceAsString();
                $data = new time();
                file_put_contents("Error_UNIX$data".".txt",$msg);
                alert("Произошла критическая ошибка! Лог: " . "Error_UNIX$data".".txt");
                app::shutdown(); //Закрываем программу
            }
        }else{
            alert("Класс не инициализированн!");
        }
    }
    
    public function send($data){ //Функция отправки данных
        $this->jcurl->setPostData($data);
        return $this->parse( $this->jcurl->exec() );
    }
    
    private function parse($res){ //Функция парсинга данных
        return Json::decode($res);
    }
}