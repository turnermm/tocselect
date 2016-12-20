<?php
/**
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Myron Turner <turnermm02@shaw.ca>
 * 
 */
if(!defined('DOKU_INC')) die();

if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'action.php');
define ("TOC_URL", DOKU_BASE ."doku.php?id=");

class action_plugin_tocselect extends DokuWiki_Action_Plugin {
 private $retv;  
 private $ul_count;
 private $ul_open;
 private $ul_closed;
    function register(Doku_Event_Handler $controller){    
       $controller->register_hook('AJAX_CALL_UNKNOWN', 'BEFORE', $this,'_ajax_call');           
    }
 
    function _ajax_call(Doku_Event $event, $param) {
            
            global $INPUT;
            
           if ($event->data == 'tocselect') {
             $event->stopPropagation();
             $event->preventDefault();
             $wikifn = rawurldecode($INPUT->str('seltoc_val'));
             $regex = preg_quote(':*');
              if(preg_match('/^(.*?)' . $regex . '\s*$/',$wikifn,$matches))
              {
                   $wikifn = $matches[1] . ':file';
                    $ns = getNS($wikifn);
                   $pathinf = pathinfo(wikiFN($wikifn) );
                   $list =  $this->get_dir_list($pathinf['dirname'], $ns);
                   echo $list;
                    return;
              }    
              else   $file = wikiFN($wikifn) ;

             $exists =  file_exists($file);
             if($exists &&  auth_quickaclcheck( $wikifn) ) {                 
                 setcookie('tocselect',$wikifn,0,DOKU_BASE);
                $this->ul_count =  $this->ul_open = $this->ul_closed = 0;                 
             $this->get_toc($wikifn);
                 if($this->retv) {
             echo $this->retv;
           }   
                     else {
                     echo "<b>" . $this->getLang('notoc') ." $wikifn</b>";
                    }     
             }
             else {
                    if($exists && !auth_quickaclcheck( $wikifn) ) {
                     echo $this->getLang('perm');
                    }
                   else if(!$exists) echo 'E_FNF';
             }
             
           }   
    }
    
    function get_toc($id) {
        $this->retv = "";
        $toc = p_get_metadata($id,'description tableofcontents');
        if(!$toc) return "";
        $current=0;
        $start_level = 0;
        $this->ulcount('open');
        $this->retv .=  "<UL>\n";
     
        foreach ($toc as $head) {
            $level =  $this->format_item($head, $current,$id);
            if($start_level==0) $start_level = $level;            
        }
        if($start_level != $level)  {
            $this->retv .= "</UL>\n";
            $this->ulcount('closed');
        }   
        $this->retv .=  "</UL>\n";
        $this->ulcount('closed');
        if($this->ul_open > $this->ul_closed) {
        $this->retv .=  "</UL>\n";
    }
    
    }
    
    function format_item($h, &$n,$id){
            if($n==0) $n=$h['level'];
          
            if($n < $h['level'] ) {
              $this->ulcount('open');              
              $this->retv .= "<UL>\n";             
              
            } 
            else if ($n != $h['level']) {
              $this->retv .= "</UL>\n";
               $this->ulcount('closed');
            } 
            
            $this->retv .=    '<li>' . $this->format_link($h['title'], $h['hid'],$id) . "</li>\n";
            $n = $h['level'];
            return $n;
        }
    function format_link($title,$anchor,$id) {   
        $link = "<a href ='". TOC_URL  . $id. '#'. $anchor."'>" . "$title</a>";
        return $link;
    }     
    
    function ulcount($which) {
        
            if ($which == "open") { 
                if($this->ul_open> 0) $this->retv .="\n" .'<li class="ihidden">'; 
                $this->ul_count++;
                $this->ul_open++;
            }
            else if ($which == "closed") {                
                $this->ul_count --; 
                 if($this->ul_closed> 0) $this->retv .="</li>\n"; 
                $this->ul_closed ++;                                 
            }   
    }
    
    private function get_dir_list($dir, $namespace){
        $ret = "<UL>";
         $dh = opendir($dir);
         if(!$dh) return;
         while (($file = readdir($dh)) !== false) {
            if($file == '.' || $file == '..') continue;           # cur and upper dir
            if(is_dir("$dir/$file")) {
                $ret .= $this->handle_directory($file, $namespace);
            }  
            else {
                $ret .=  $this->handle_file($file, $namespace);                     
            }
        }
        closedir($dh);
        $ret = $ret . "</UL>";
        return $ret;
    }
    
    private function handle_directory($curdir, $namespace) {
        return "<li><span  class='clicker' onclick=\"tocsel_updatetoc('$namespace:$curdir:*');\">$namespace:$curdir:*</span></li>";
    }
    
    private function handle_file($file, $namespace) {
        $file = preg_replace("/\.txt$/","", $file);
        return "<li><span  class='clicker' onclick=\"tocsel_updatetoc('$namespace:$file');\">$namespace:$file</span></li>";
    }
    
 }
     
  

