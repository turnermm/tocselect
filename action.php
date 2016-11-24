<?php
if(!defined('DOKU_INC')) die();

if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'action.php');
define ("TOC_URL", DOKU_BASE ."doku.php?id=");

class action_plugin_tocselect extends DokuWiki_Action_Plugin {
 private $retv;  
 
    function register(&$controller){    
       $controller->register_hook('AJAX_CALL_UNKNOWN', 'BEFORE', $this,'_ajax_call');           
    }
 
    function _ajax_call(Doku_Event $event, $param) {
            global $INPUT, $USERINFO,$INFO;
           if ($event->data == 'tocselect') {
             $event->stopPropagation();
             $event->preventDefault();
             $wikifn = $INPUT->str('seltoc_val');
             $file = wikiFN($wikifn) ;
             if(file_exists($file)) {
                 $time = time()+120;
                 setcookie('tocselect',$wikifn,$time,DOKU_BASE);
             $this->get_toc($wikifn);
                 if($this->retv ) {
             echo $this->retv;
           }   
                     else {
                         echo "<H3>No TOC for $wikifn</H3>";
                    }     
             }
             else {
                    echo 'E_FNF';
             }
             
           }   
    }
    
    function get_toc($id) {
        $this->retv = "";
        $toc = p_get_metadata($id,'description tableofcontents');
        if(!$toc) return "";
        $current=0;
        $start_level = 0;
        $this->retv .=  "<UL>\n";
        foreach ($toc as $head) {
            $level =  $this->format_item($head, $current,$id);
            if($start_level==0) $start_level = $level;            
        }
        if($start_level != $level) $this->retv .= "</UL>\n";
        $this->retv .=  "</UL>\n";
    }
    
    function format_item($h, &$n,$id){
            if($n==0) $n=$h['level'];
          
            if($n < $h['level'] ) {
              $this->retv .= "<UL>\n";             
            } 
            else if ($n != $h['level']) $this->retv .= "</UL>\n";
            
            $this->retv .=    '<li>' . $this->format_link($h['title'], $h['hid'],$id) ;//. '(', $h['level'] .")</li>\n";
            $n = $h['level'];
            return $n;
        }
    function format_link($title,$anchor,$id) {   
        $link = "<a href ='". TOC_URL  . $id. '#'. $anchor."'>" . "$title</a>";
        return $link;
    }     
 }
     
  

