<?php
namespace app\modules;

use std, gui, framework, app;


class MainModule extends AbstractModule
{

    /**
     * @event timer.construct 
     */
    function doTimerConstruct(ScriptEvent $e = null)
    {    
        
    }


    /**
     * @event action 
     */
    function doAction(ScriptEvent $e = null)
    {    
        global $APIURL,$DEFURL;
        $APIURL = "http://192.168.2.6/test-online/api.desktop.php";
        $DEFURL = "http://192.168.2.6/test-online/";
    }

}
