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
define ("TOCSEL_IMGDIR", DOKU_BASE . 'lib/plugins/tocselect/img/');

class action_plugin_tocselect extends DokuWiki_Action_Plugin {
 private $retv;  
 private $ul_count;
 private $ul_open;
 private $ul_closed;
 private $up;
    function register(Doku_Event_Handler $controller){    
       $controller->register_hook('AJAX_CALL_UNKNOWN', 'BEFORE', $this,'_ajax_call');    
       $controller->register_hook('DOKUWIKI_STARTED', 'BEFORE', $this,'handle_started');    
    }
 
    function handle_started(Doku_Event $event, $param) {
             global $conf;
            if($this->getConf('notoc')) {
                 $conf['tocminheads'] = 0;                   
            }       
                
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
                   $wikifn = $matches[1];
                   $ns = getNS($wikifn . ':file');
                   $pathinf = pathinfo(wikiFN($wikifn . ':file') );
                   if($matches[1]) {
                       $this->up =  $this->get_up_dir($pathinf );  //inserted in get_dir_list()
                   }
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
                     $up =  $this->get_up_dir(pathinfo("$file/file"));
                     echo "<ul>$up";                     
                     echo "<li><span class='ten__ptb'>" . $this->getLang('notoc') ." $wikifn</span></li></ul>";
                    }     
            }
             else {
                    if($exists && !auth_quickaclcheck( $wikifn) ) {
                     echo $this->getLang('perm');
                    }                  
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
              
         $up = $this->get_up_dir(pathinfo(wikiFN("$id:file")));        
         $this->retv .=  "<UL class='tocsel_li1'>$up\n";
     
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
        $retdir = "<UL>";
        if(!empty($this->up)) $retdir .= $this->up;
        $retfile = "";
        $dir_ar = array();
        $file_ar = array();
        
         $dh = opendir($dir);
         if(!$dh) return;
         while (($file = readdir($dh)) !== false) {
            if($file == '.' || $file == '..') continue;           # cur and upper dir
            if(is_dir("$dir/$file")) {              
                $dir_ar[$file] = $this->handle_directory($file, $namespace);
            }  
            else {                
                if(!preg_match("/\.txt$/",$file)|| preg_match("/^_/",$file) ) continue;  //exclude non .txt files and templates
                $file_ar[$file] =  $this->handle_file($file, $namespace);                     
            }
        }
        closedir($dh);
        ksort($dir_ar);
        ksort($file_ar);
        foreach ($dir_ar as $key=>$val) {
            $retdir .= $val;
        }
        foreach ($file_ar as $key=>$val) {
            $retfile .= $val;
        }
        $ret = $retdir . $retfile  . "</UL>";
        return $ret;
    }
    
    private function handle_directory($curdir, $namespace) {
        
        $page = urldecode("$namespace:$curdir") . ':*';
        return "<li><span  class='clickerdir  tocselb' onclick=\"tocsel_updatetoc('$page');\">$page</span></li>";
    }
    
    private function handle_file($file, $namespace) {
        $file = preg_replace("/\.txt$/","", $file);
        $page =  urldecode("$namespace:$file"); 
        return "<li><span  class='clickerfile' onclick=\"tocsel_updatetoc('$page');\">$page</span></li>";
    }
    
    private function handle_up($namespace) {
        if(empty($namespace))
              $title = 'Root NS';
         else {
            $namespace = urldecode($namespace);      
            $title =$namespace;
         } 
        $png = '<img title = "' . $title. '"src = "' . TOCSEL_IMGDIR.'up.png' . '" />';
         return "<li class= 'tocsel_up'><span  class='clicker  tocselb' onclick=\"tocsel_updatetoc('$namespace:*');\">$png</span></li>  ";
    }
    
    private function get_up_dir($pathinf) {
         $up = dirname($pathinf['dirname']);
         $up = preg_replace("#.*?/data/pages#","",$up);
        $up = str_replace('/', ':',  $up);                       
        return $this-> handle_up($up);   // empty $up = root ns
    }
 }
     
  

