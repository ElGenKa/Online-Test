<?php
namespace app\forms;

use std, gui, framework, app;


class settings extends AbstractForm
{

    /**
     * @event button.action 
     */
    function doButtonAction(UXEvent $e = null) //Применение параметров
    {    
        global $animspeed, $animsteep;
        $save = $this->checkbox->selected;
        $item = $this->combobox->selectedIndex;
        if($item == -1 or $item == 0){
            $gr = "low";
            $animsteep = 1;
            $animspeed = 200;
        }
        if($item == 1){
            $gr = "high";  
            $animsteep = 0.05;
            $animspeed = 50;  
        }
        
        if($save == true){
            $this->ini->set("graphix",$gr,"settings");
        }
        $this->form("MainForm")->show();
        $this->hide();
        $this->free();
    }

    /**
     * @event show 
     */
    function doShow(UXWindowEvent $e = null) //При появлении читаем ini файл
    {    
        global $animspeed, $animsteep;
        $gr = $this->ini->get("graphix","settings");
        if(!empty($gr)) {
            if($gr=="low"){
                $animsteep = 1;
                $animspeed = 200;
            }elseif($gr=='high'){
                $animsteep = 0.05;
                $animspeed = 50;
            }
            $this->form("MainForm")->show();
            $this->hide();
            $this->free();
        }
    }

}
