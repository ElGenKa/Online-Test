<?php
namespace app\forms;

use std, gui, framework, app;
use Thread;


class loadform extends AbstractForm
{

     var $thr;
     var $thrinit;
     var $system;

    /**
     * @event image.mouseEnter 
     */

    function doImageMouseEnter(UXMouseEvent $e = null) //Анимация кнопки обновления
    {    
        if($this->thrinit == false){
            $this->thrinit = true;
            //$this->image->effects->add( RotateAnimationBehaviour );
            $this->image->rotateAnim->enable();
        }
    }

    /**
     * @event image.mouseExit 
     */
    function doImageMouseExit(UXMouseEvent $e = null) //Выключение анимации
    {    
        if($this->thrinit == true){
            $this->image->rotateAnim->disable();
            $this->thrinit = false;
            $this->image->rotate = 0;
        }
    }

    /**
     * @event show 
     */
    function doShow(UXWindowEvent $e = null) //Появление формы
    {    
        $APIURL = "http://192.168.2.6/test-online/api.desktop.php"; //Указываем URL до API
        $DEFURL = "http://192.168.2.6/test-online/";
        $test = new testsystem($APIURL,$DEFURL);
        $this->system = $test;
        self::updtests(); //Получаем список тестов
    }

    /**
     * @event image.click-Left 
     */
    function doImageClickLeft(UXMouseEvent $e = null) //При нажатии на кнопку обновления
    {    
        self::updtests();
    }

    function updtests(){
        $res = $this->system->get_test_list();
        $oldKey = "";
        $oldKey1 = "";
        $y = 60; //Позиция по высоте первого элемента
        $fade = 250; //Начальный тайминг плавного появления
        if(!is_array($res)) {}
        foreach($res as $key => $value){
            foreach($value as $key1 => $value1){
                $fade +=50; //Добавляем по 50 к анимции для каждого нового элемента
                if($key!=$oldKey){ //Группа
                    $y +=22;
                    $label = new UXLabel(); 
                    $label->text = $key;
                    $label->position = [10,$y];
                    $label->classesString .= " labelbig";
                    $label->autosize = 1;
                    $label->opacity = 0;
                    $this->add($label);
                    $this->system->objectlist[] = $label;
                }
                if($key1!=$oldKey1){ //Дисциплина
                    $y +=22;
                    $label = new UXLabel();
                    $label->text = $key1;
                    $label->position = [25,$y];
                    $label->classesString .= " labelbig";
                    $label->autosize = 1;
                    $label->opacity = 0;
                    $this->add($label);
                    $this->system->objectlist[] = $label;
                    
                }
                Animation::fadeIn($label, $fade);
                $oldKey = $key;
                $oldKey1 = $key1;
                for($i=0;$i<count($value1);$i++){ //Тесты для группы и дисциплины
                    $y +=30; 
                    $checkbox = new UXCheckbox();
                    $checkbox->data('id', $value1[$i][0]);
                    $checkbox->text = $value1[$i][1];
                    $checkbox->font->classesString .= " label";
                    $checkbox->position = [35,$y];
                    $checkbox->opacity = 0;
                    
                    $this->system->testlist[]=$checkbox;
                    $this->add($checkbox);
                    Animation::fadeIn($checkbox, $fade);
                    $fade+=50;
                    $checkbox->on('click', function ($ev) { //Функция клика 
                        global $test_list;                       
                        //$test_list = $this->form("MainForm")->test_list;   
                        for($i=0;$i<count($test_list);$i++){
                            $this->system->testlist[$i]->selected = 0;
                        }
                        $ev->sender->selected = 1;  
                        $this->form("MainForm")->select_id = $ev->sender->data('id');     
                    }); 
                }
            }
        }
    }

}
