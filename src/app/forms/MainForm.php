<?php
namespace app\forms;

use facade\Json;
use bundle\jurl\jURL;
use std, gui, framework, app;


class MainForm extends AbstractForm
{

    //var $test_list;
    //var $object_list;
    var $select_id;
    
    function update_tests(){
        global $APIURL,$tests,$test_list,$object_list,$select_id;
        $data = [
            'method' => 'get_tests'
        ];
        $ch = new jURL($APIURL);
        $ch->setPostData($data);
        $res = $ch->exec();
        $res = Json::decode($res);
        //for($i=0;$i<count($res);$i++){
            
        //}
        //var_dump($test_list);
        for($i=0;$i<count($test_list);$i++){
        
            $test_list[$i]->free();            
        }
        
        for($i=0;$i<count($object_list);$i++){
            $object_list[$i]->free();            
        }
        unset($test_list);
        unset($object_list);
        $oldKey = "";
        $oldKey1 = "";
        $y = 100;
        foreach($res as $key => $value){
            foreach($value as $key1 => $value1){
                if($key!=$oldKey){
                    $y +=22;
                    $label = new UXLabel();
                    $label->text = $key;
                    $label->position = [10,$y];
                    $label->font->family = "Times New Roman";
                    $label->font->bold = true;
                    $label->font->size = 16;
                    $label->autosize = 1;
                    $this->panel->add($label);
                    $object_list[] = $label;
                }
                if($key1!=$oldKey1){
                    $y +=22;
                    $label = new UXLabel();
                    $label->text = $key1;
                    $label->position = [25,$y];
                    $label->font->family = "Times New Roman";
                    $label->font->bold = true;
                    $label->font->size = 16;
                    $label->autosize = 1;
                    $this->panel->add($label);
                    $object_list[] = $label;
                    
                }
                $oldKey = $key;
                $oldKey1 = $key1;
                for($i=0;$i<count($value1);$i++){
                    $y +=30;
                    $checkbox = new UXCheckbox();
                    $checkbox->data('id', $value1[$i][0]);
                    $checkbox->text = $value1[$i][1];
                    $checkbox->font->family = "Times New Roman";
                    $checkbox->font->size = 14;
                    $checkbox->position = [35,$y];
                    $test_list[]=$checkbox;
                    $this->panel->add($checkbox);
                    $checkbox->on('click', function ($ev) { //Функция клика 
                        global $test_list;                       
                        //$test_list = $this->form("MainForm")->test_list;   
                        for($i=0;$i<count($test_list);$i++){
                            $test_list[$i]->selected = 0;
                        }
                        $ev->sender->selected = 1;  
                        $this->form("MainForm")->select_id = $ev->sender->data('id');     
                    }); 
                }
            }
        }
    }

    /**
     * @event show 
     */
    function doShow(UXWindowEvent $e = null)
    {    
        
        //$this->radioGroup->items->clear();
        //$this->radioGroup->items->addAll($list);        
        
    }

    /**
     * @event showing 
     */
    function doShowing(UXWindowEvent $e = null)
    {    
        
    }

    /**
     * @event construct 
     */
    function doConstruct(UXEvent $e = null)
    {    
        $size = $this->size;
        $w = $size[0];
        $h = $size[1];
        $x = $w / 2 - 500;
        $this->panel_error->position = [-1000, 500];
        $this->image->size = [$w,$h];
        
        $this->panel->x = $x;
        $this->panel->size = [1000,$h];
        $this->panel->y = 0;
        $this->panel->opacity = 0;
        
        $this->preparetest->y = 0;
        $this->preparetest->x = -1000;
        $this->preparetest->size = [1000,$h];
        $this->preparetest->opacity = 0;
        
        $this->paneltesting->y = 0;
        $this->paneltesting->x = -1000;
        $this->paneltesting->size = [1000,$h];
        $this->paneltesting->opacity = 0;
        
        $this->panelresult->y = 0;
        $this->panelresult->x = -1000;
        $this->panelresult->size = [1000,$h];
        $this->panelresult->opacity = 0;
        
        $this->browser->size = [994,$h-46];
        
        $this->stoppanel->y = 500;
        $this->stoppanel->x = -1000;
        
        $this->browser->size = [1000, $h-64];

        $this->progressIndicator->x = $w / 2 - 25;
        Timer::setTimeout( function() use ($this){
            global $animspeed, $animsteep;
            uiLater( function() use ($this){
                global $animspeed, $animsteep;
               $this->progressIndicator->visible = 0;
               //$this->label4->text = "ANIM: ".$animspeed;
            });

            $timer = Timer::setInterval( function($e) use ($this){
                //print_r($e);
                global $animspeed, $animsteep;
                uiLater( function() use ($this, $e){
                    global $animspeed, $animsteep;
                   $this->panel->opacity += $animsteep;
                   if($this->panel->opacity >= 1){
                       $this->panel->opacity = 1;
                       $e->cancel();
                       //self::update_tests();
                       $this->update_tests();
                   }
                });
                
            },$animspeed);
            
        }, 1000);
        
    }

    /**
     * @event appclose1.action 
     */
    function doAppclose1Action(UXEvent $e = null)
    {    
        app::shutdown();
    }

    /**
     * @event appclose2.action 
     */
    function doAppclose2Action(UXEvent $e = null)
    {    
        global $animspeed, $animsteep;
        Timer::setInterval( function($e) use ($this){
        uiLater( function() use ($this, $e){
            global $animspeed, $animsteep;
           $size = $this->size;
           $this->preparetest->opacity -= $animsteep;
           $this->panel->opacity += $animsteep;
           $this->panel->position = [$size[0]/2-500,0];
           if($this->preparetest->opacity <= 0){
               $this->preparetest->opacity = 0;
               $this->preparetest->x = -1000;
               $this->panel->opacity = 1;
               $e->cancel();
           }
        });
                
        },$animspeed);
    }

    /**
     * @event buttonAlt.action 
     */
    function doButtonAltAction(UXEvent $e = null)
    {    
        $e_fi = $this->e_fi->text; $e_gr = $this->e_group->text;
        $index = $this->select_id;
        if( strlen($e_fi)>6 and strlen($e_gr)>3 and $index>=0 ){
            global $APIURL,$tests,$systemdata, $predata, $timermin,$globaltime, $animspeed, $animsteep, $enable_timer_sec, $all_circle;
                    
            $test_id =  $index;
            $data = [
                'method' => 'start_test',
                'send_testid' => $test_id,
                'send_nname' => $this->e_fi->text,
                'send_gname' => $this->e_group->text
            ];
            $ch = new jURL($APIURL);
            $ch->setPostData($data);
            $res = $ch->exec();
            $res = Json::decode($res);
        }else{
            $size = $this->size;
            $this->panel_error->position = [$size[0]/2-244, 200];
        
            $res['success'] == 'err';
            //$this->label4->textColor = "red";
            //$this->label4->text ="Введите, пожалуйста, Вашу фамилию и имя (Проверьте введеные данные!)";
        }
        if($res['success'] == 'ok'){
            $systemdata['testid'] = $test_id;
            $systemdata['user'] = $this->e_fi->text;
            $systemdata['group'] = $this->e_group->text;
            $systemdata['test_theme'] = $res['test_theme'];
            $systemdata['test_comment'] = $res['test_comment'];
            $systemdata['time1c'] = $res['time1c'];
            $systemdata['time2c'] = $res['time2c'];
            $systemdata['time1'] = $res['time1'];
            if($systemdata['time1c'] == "on") $enable_timer_sec = true;
            else $enable_timer_sec = false;
            if($systemdata['time2c']=="on"){
                $globaltime = 60;
                $timermin=true;
            }else{
                $globaltime = 0;
                $timermin=false;
            }
            $systemdata['time2'] = $res['time2'];
            $systemdata['proc'] = $res['proc'];
            $systemdata['skipt'] = $res['skipt'];
            $systemdata['test_count_all'] = $res['test_count_all'];
            $systemdata['test_count'] = $res['test_count'];
            $systemdata['test_view'] = $res['test_view'];
            //print_r( $systemdata );
            $predata['vpall'] = $systemdata['test_count_all'];

            for($i = 0; $i<count($all_circle); $i++){
                $all_circle[$i]->free();
            }


            $text_info = "Тест: ".$res['test_theme'] ."\r\n";
            if ($res['test_count'] == 0){
                $text_info .= "Вопросов в тесте: ".$systemdata['test_count_all']."\r\n";
                $systemdata['count_vp_timer'] = $systemdata['test_count_all'];
            }else{
                $text_info .= "Вопросов в тесте: ".$systemdata['test_count']."\r\n";
                $systemdata['count_vp_timer'] = $systemdata['test_count'];
            }
            
            if($res['time1c'] == 'on') $text_info .= "Времени на вопрос: ". $res['time1'] ." сек.\r\n";
            if($res['time2c'] == 'on') $text_info .= "Времени на тест: ". $res['time2'] ." мин.\r\n";
            
            if($res['proc'] == 'on') $text_info .= "Проказывать проценты: Да\r\n";
            else $text_info .= "Проказывать проценты: Нет\r\n";
            
            if( $res['test_view'] = 'on') $text_info .= "Показывать правильные ответы: Да\r\n";
            else $text_info .= "Показывать правильные ответы: Нет\r\n";
            
            if(!empty($res['test_comment'])) $text_info .= "Комментарий от преподавателя: \r\n" . $res['test_comment'];
            $this->l_info->text = $text_info;
            $count_str = (substr_count($text_info, "\n"));
            for($i = 0; $i<$count_str; $i++){
                $new = new UXCircle();
                $new->fillColor = "#88b9e3";
                $new->strokeColor = "#88b9e3";
                $new->size = [11,11];
                $new->x = 43;
                $new->y = $this->l_info->y + ($i*21) + 4;
                $this->preparetest->add($new);
                $all_circle[] = $new;
            }
            /*
            $new = new UXCircle();
            $new->fillColor = "#88b9e3";
            $new->strokeColor = "#88b9e3";
            $new->size = [11,11];
            $new->x = 43;
            $new->y = $y;
            $this->preparetest->add();
            */
            
            Timer::setInterval( function($e) use ($this){
                global $animspeed, $animsteep;
                uiLater( function() use ($this, $e){
                    global $animspeed, $animsteep;
                   $size = $this->size;
                   $this->panel->opacity -= $animsteep;
                   $this->preparetest->opacity += $animsteep;
                   $this->preparetest->position = [$size[0]/2-500,0];
                   if($this->panel->opacity <= 0){
                       $this->panel->opacity = 0;
                       $this->panel->x = -1000;
                       $this->preparetest->opacity = 1;
                       $e->cancel();
                   }
                });
                
            },$animspeed);
            
        }
    }

    /**
     * @event button.action 
     */
    function doButtonAction(UXEvent $e = null)
    {    
        global $APIURL,$tests,$systemdata, $objarray, $testingtimer, $timermin, $animspeed, $animsteep;
        $test_id =  $systemdata['testid'];
        //var_dump($tests);
        $data = [
            'method' => 'start',
            'test_id' => $test_id,
            'nname' => $systemdata['user'],
            'gname' => $systemdata['group']
        ];
        $ch = new jURL($APIURL);
        $ch->setPostData($data);
        $res = $ch->exec();
        
        $res = Json::decode($res);
        if($res['job'] == "success"){
            $systemdata['session'] = $res['test_id'];
            
            $size = $this->size; 
            $this->paneltesting->position = [$size[0]/2-500,0];
            $this->container->height = $size[1] - 264;
            
            Timer::setInterval( function($e) use ($this){
                global $animspeed, $animsteep;
                uiLater( function() use ($this, $e){
                    global $animspeed, $animsteep;
                   $size = $this->size;
                   $this->paneltesting->opacity += $animsteep;
                   $this->preparetest->opacity -= $animsteep;
                   if($this->preparetest->opacity <= 0){
                       $this->preparetest->opacity = 0;
                       $this->preparetest->x = -1000;
                       $this->paneltesting->opacity = 1;
                       $e->cancel();
                   }
                });
                
            },$animspeed);
            
            $testingtimer = Timer::setInterval( function($e) use ($this){
                
                global $predata, $globaltime,$timermin,$systemdata, $enable_timer_sec;
                $predata['sec'] -=1;
                $text = "";
                if(!empty($predata['vp'])) $text .= "Вопрос: ".$predata['vp']."/".$systemdata['count_vp_timer']."\r\n";
                if(!empty($predata['proc'])) $text .= "Правильность ответов: ".$predata['proc']."%\r\n";
                if($timermin==true){
                    if($globaltime == 0){
                        $systemdata['time2'] -=1;
                        $globaltime = 60;
                        uiLater( function() use ($this){
                            send_test($this);
                        });
                    }
                    if($systemdata['time2']==-1){
                        uiLater( function() use ($this){
                            end_test($this);
                        });
                    }
                    $globaltime -=1;
                    $text .= "Времени на тест осталось: ". $systemdata['time2']." мин."."\r\n";
                    //$systemdata['time2']
                }
                    if($enable_timer_sec == true){
                    if(!empty($predata['sec'])) $text .= "Времени на вопрос осталось: ".$predata['sec']." сек."."\r\n";
                    if($predata['sec']==0) 
                        uiLater( function() use ($this){
                            send_test($this);
                        });
                    }
                
                uiLater( function() use ($this, $text){
                    $this->labelAlt->text = $text;
                });
                
                
            },1000);
            
            $objarray = array();
            get_test($this);
            
            
        }
    }

    /**
     * @event button4.action 
     */
    function doButton4Action(UXEvent $e = null)
    {    
        send_test($this);
    }

    /**
     * @event button7.action 
     */
    function doButton7Action(UXEvent $e = null)
    {    
        $this->stoppanel->x = -1000;
    }

    /**
     * @event button6.action 
     */
    function doButton6Action(UXEvent $e = null)
    {    
        global $APIURL,$systemdata;
         $data = [
            'method' => 'getend',
            'session' => $systemdata['session'],
        ];

        $ch = new jURL($APIURL);
        $ch->setPostData($data);
        $ch->exec();
    
        $this->stoppanel->x = -1000;
        end_test($this);
    }

    /**
     * @event button3.action 
     */
    function doButton3Action(UXEvent $e = null)
    {    
        $size = $this->size;
        $this->stoppanel->x = $size[0]/2 - 216;
    }

    /**
     * @event button5.action 
     */
    function doButton5Action(UXEvent $e = null)
    {    
        global $animspeed, $animsteep;
        Timer::setInterval( function($e) use ($this){
            global $animspeed, $animsteep;
            uiLater( function() use ($this, $e){
                global $animspeed, $animsteep;
               $size = $this->size;
               $this->panelresult->opacity -= $animsteep;
               $this->panel->opacity += $animsteep;
               $this->panel->position = [$size[0]/2-500,0];
               if($this->panelresult->opacity <= 0){
                   $this->panelresult->opacity = 0;
                   $this->panelresult->x = -1000;
                   $this->panel->opacity = 1;
                   $e->cancel();
               }
            });                
        },$animspeed);
    }

    /**
     * @event button8.action 
     */
    function doButton8Action(UXEvent $e = null)
    {    
        self::update_tests();
    }

    /**
     * @event button9.action 
     */
    function doButton9Action(UXEvent $e = null)
    {    
        $this->panel_error->position = [-1000, 500];
    }
    
    

}


function get_test($this){
    global $APIURL,$tests,$systemdata, $objarray, $DEFURL, $vptp, $lasttimesec, $predata, $steep_view;
    $test_id =  $tests[$index][0];
    $data = [
        'method' => 'getquest',
        'session' => $systemdata['session'],
        'nname' => $systemdata['user'],
        'gname' => $systemdata['group']
    ];
    //$this->timer_testing->start();
    $ch = new jURL($APIURL);
    $ch->setPostData($data);
    $res = $ch->exec();
    $res = Json::decode($res);
    
            $ImageArea = new UXImageArea(UXImage::ofUrl("http://192.168.2.6/test-online/EMPTY.png", false)); 
            //$ImageArea->position = [50, 50]; 
            $ImageArea->size = [1,1]; 
            //$ImageArea->centered = true; 
            //$ImageArea->stretch = true; 
            //$ImageArea->proportional = true;
            //$this->label->graphic = $ImageArea;
            //$ImageArea = new UXImageArea();
    
    $this->label->graphic = $ImageArea;
    for($i=0; $i<count($objarray); $i++){
        $objarray[$i]->free();
    }
    unset($objarray);
    $objarray = array();
    
    if($res['status'] == 'end'){
        end_test($this);
    }else{
        $predata['sec'] = $res['timer'];
        $predata['proc'] = $res['proc'];
        $predata['vp'] += 1;
        //$this->label_proc->text = "Правильность ответов: ".$res['proc']."%";
        $res['text'] = str_replace(["&lt;br&gt;","&lt;br &gt;", "&lt; br&gt;", "&lt; br &gt;"], "", $res['text']);
        $this->label->text = htmlspecialchars_decode( $res['text'] );
        $size_label_h = (substr_count($res['text'], "\n")+1) * 19 + 20;
        //$size_label = $this->label->size;
        $this->label->size = [968,$size_label_h];
        waitAsync(500, function(){
            uiLater(function(){
                $this->label->autosize = 1;
            });
        });
        $size_label_y = $this->label->y;
        
        if($res['yes_no'] == 1) {
            $steep_view = 1;
            $show_otv = true;
            $systemdata['yes_no'] = 1;
        }else{
            $steep_view = -1;
             $systemdata['yes_no'] = 0;
        }
        
        if($res['ispic']=="true"){
            $res['files'] = str_replace("./", "", $res['files']);
        
            $url = $DEFURL.$res['files'].$res['pic'];
            
            $url = str_replace(["\r\n","\n", "\r", "\n\r"], "", $url);
            
            $ImageArea = new UXImageArea(UXImage::ofUrl($url, false)); 
            //$ImageArea->position = [50, 50]; 
            $ImageArea->size = [250,160]; 
            $ImageArea->centered = true; 
            $ImageArea->stretch = true; 
            $ImageArea->proportional = true;
            $this->label->graphic = $ImageArea;
            $size_label_h +=160;
        }
        
        $this->container->position = [$this->container->x,$size_label_h+$size_label_y+5];
        $this->container->size = [994, $this->paneltesting->height - ($size_label_h+$size_label_y+5) ];
        
        switch($res['vptype']){
            
            case 'IMAGES':
                for($i=0; $i<$res['count_vp']+1; $i++){
                    $vptp = 'select';
                    $url = $res['files'].$res['vp'][$i]['text'];
                    $url = str_replace(" @", "", $url);
                    $url = str_replace("@ ", "", $url);
                    $url = str_replace("@", "", $url);
                
                    $new = new UXCheckbox();
                    $new->size = [900,200];
                    $new->data("yes", 0);
                    if($show_otv==true){
                        for($j=0;$j<count($res['vp+']);$j++){
                            $res['vp+'][$j] = str_replace(["\r\n","\n", "\r", "\n\r"],'',$res['vp+'][$j]);
                            if($res['vp+'][$j] == $res['vp'][$i]['id'])
                                $new->data("yes", 1);
                        }
                        
                    }
                    $gr = new UXImageArea(UXImage::ofUrl($DEFURL.$url, false));
                    $gr->size = [300,200];
                    $gr->centered = true; 
                    $gr->stretch = true; 
                    $gr->proportional = true;
                    
                    $new->graphic = $gr;
                    $new->id = $res['vp'][$i]['id'];
                    //print_r($new->id."|".$res['vp'][$i]['id']."\r\n");
                    $new->text = "";
                    $new->font = $this->label->font;
                    if($i > 0){
                        $y = $objarray[$i-1]->position;
                        $h = $objarray[$i-1]->size;
                    }else{
                        $y[1] = 5;
                        $h[1] = 0;
                    }
                    $new->position = [4, $y[1] + $h[1] + 14];
                    $new->wraptext = true;
                    //$this->panel->add($new);
                    $this->container->content->add($new);
                    $objarray[$i] = $new;
                }
            break;
            
            case 'INPUTTEXT':
                $vptp = 'input';
                $new = new UXTextField();
                $new->x = 10;
                $new->size = [980,32];
                $new->y = 15;
                $new->promptText = "Введите ответ";
                $this->container->content->add($new);
                $objarray[0] = $new;
            break;
                
            default:
                $vptp = 'select';
                for($i=0; $i<$res['count_vp']+1; $i++){
                    $new = new UXCheckbox();
                    $new->data("yes", 0);
                    if($show_otv==true){
                        for($j=0;$j<count($res['vp+']);$j++){
                            $res['vp+'][$j] = str_replace(["\r\n","\n", "\r", "\n\r"],'',$res['vp+'][$j]);
                            if($res['vp+'][$j] == $res['vp'][$i]['id'])
                                $new->data("yes", 1);
                        }
                    }
                        
                    $new->size = [900,24];
                    $new->wraptext = true;
                    $new->text = htmlspecialchars_decode($res['vp'][$i]['text']);
                    if($i > 0){
                        $y = $objarray[$i-1]->position;
                        $h = $objarray[$i-1]->size;
                    }else{
                        $y[1] = 5;
                        $h[1] = 0;
                    }
                    $new->position = [4, $y[1] + $h[1] + 14];
                    $new->id = $res['vp'][$i]['id'];
                    $new->font = $this->label->font;
                    $this->container->content->add($new);
                    $objarray[$i] = $new;
                }    
            
        }
    }
}


function send_test($this){
    global $APIURL,$tests,$systemdata, $objarray, $vptp,$steep_view,$DEFURL;
    
    //-----------------------------------
    if($systemdata['yes_no']==1 and $steep_view == 2){
        if($vptp == 'select'){
        for($i=0; $i<count($objarray); $i++){
            if( $objarray[$i]->selected == true ){
                $arr[] = $objarray[$i]->id;
            }
        }
    }else{
       $arr[] = $objarray[0]->text; 
    }
    $arr = Json::encode($arr);
    $data = [
        'method' => 'sendquest',
        'session' => $systemdata['session'],
        'vp' => $arr
    ];
    
    $ch = new jURL($APIURL);
    $ch->setPostData($data);
    $res = $ch->exec();
    $res = Json::decode($res);
    
    if($res['job'] == 'success'){
        if($res['status'] == 'end'){
            end_test($this);
        }else{
            get_test($this);
        }
    }else{
        //file_put_contents("job_log.txt", $res);
    }
    $this->button4->enabled = false;
    $this->button4->color = "#cccccc";
    $steep_view = 2;
    waitAsync(5000, function($e = NULL) use($this){
        uiLater(function() use ($this){
            global $steep_view;
           $this->button4->enabled = true;
           $this->button4->color = "#60a917";
           $steep_view = 1;
        });
    } );
    }
    //-----------------------------------
    if($systemdata['yes_no']!=1){
        if($vptp == 'select'){
        for($i=0; $i<count($objarray); $i++){
            if( $objarray[$i]->selected == true ){
                $arr[] = $objarray[$i]->id;
            }
        }
    }else{
       $arr[] = $objarray[0]->text; 
    }
    $arr = Json::encode($arr);
    $data = [
        'method' => 'sendquest',
        'session' => $systemdata['session'],
        'vp' => $arr
    ];
    
    $ch = new jURL($APIURL);
    $ch->setPostData($data);
    $res = $ch->exec();
    $res = Json::decode($res);
    
    if($res['job'] == 'success'){
        if($res['status'] == 'end'){
            end_test($this);
        }else{
            get_test($this);
        }
    }else{
        //file_put_contents("job_log.txt", $res);
    }
    $this->button4->enabled = false;
    $this->button4->color = "#cccccc";
    waitAsync(5000, function($e = NULL) use($this){
        uiLater(function() use ($this){
           $this->button4->enabled = true;
           $this->button4->color = "#60a917";
        });
    } );
    //-----------------------------------
    }
    
    if($systemdata['yes_no']==1 and $steep_view == 1){
        for($i=0; $i<count($objarray); $i++){
            if($objarray[$i]->data("yes")==1){
                $gr = new UXImageArea(UXImage::ofUrl($DEFURL."i/prav.png", false));
                $gr->size = [15,15];
                $gr->centered = true; 
                $gr->stretch = true; 
                $gr->proportional = true;
                $objarray[$i]->graphic = $gr;
            }else{
                $gr = new UXImageArea(UXImage::ofUrl($DEFURL."i/nprav.png", false));
                $gr->size = [15,15];
                $gr->centered = true; 
                $gr->stretch = true; 
                $gr->proportional = true;
                $objarray[$i]->graphic = $gr;
            }
        }
        $steep_view = 2;
    }
    
}

function end_test($this){
    global $testingtimer, $DEFURL, $systemdata, $objarray, $predata,$animspeed, $animsteep;
    unset($predata);
    for($i=0; $i<count($objarray); $i++){
        $objarray[$i]->free();
    }
    
    $testingtimer->cancel();
    $this->browser->url = $DEFURL."enddesctop.php?id=".$systemdata['session'];
    Timer::setInterval( function($e) use ($this){
        global $animspeed, $animsteep;
        uiLater( function() use ($this, $e){
            global $animspeed, $animsteep;
           $size = $this->size;
           $this->paneltesting->opacity -= $animsteep;
           $this->panelresult->opacity += $animsteep;
           $this->panelresult->position = [$size[0]/2-500,0];
           if($this->paneltesting->opacity <= 0){
               $this->paneltesting->opacity = 0;
               $this->paneltesting->x = -1000;
               $this->panelresult->opacity = 1;
               $e->cancel();
           }
        });
                
    },$animspeed);
}
