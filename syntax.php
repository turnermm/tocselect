<?php
/**
 * 
 * 
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Myron Turner <turnermm02@shaw.ca>
 * 
 */
// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
define('TOCSEL_DIR', DOKU_BASE . 'lib/plugins/tocselect/');
require_once(DOKU_PLUGIN.'syntax.php');

/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_tocselect extends DokuWiki_Syntax_Plugin {
    
    function getType(){
        return 'substition';
    }

    /**
     * Where to sort in?
     */ 
    function getSort(){
        return 155;
    }
    function getPType() {
        return 'block';
    }

   function connectTo($mode) {
        $this->Lexer->addSpecialPattern('~~SELECTTOC~~',$mode,'plugin_tocselect'); 
        $this->Lexer->addSpecialPattern('~~SELECTTOC>curID~~',$mode,'plugin_tocselect'); 
    }
    
  function handle($match, $state, $pos, Doku_Handler $handler) {
    
            $handler->_addCall('notoc',array(),$pos);
           if(preg_match('/curID/', $match)) {
               $match = 'curID';               
           }
           else $match = 'wiki:id';
            
           return array($state,$match);
  }

   function render($mode, Doku_Renderer $renderer, $data) {
        if($mode == 'xhtml'){           
         global $lang;
         $select = $this->getLang('select');
         list($state,$wikid) = $data;  
        $renderer->doc .='<div class="tocsel_right">';
         $renderer->doc .=  '<DIV><FORM><input type="button" value="' . $select. '" id="selectoc_btn"  name="selectoc_btn" /> <INPUT type="text" id="selectoc_id" name="selectoc_id" value="'.$wikid .'"></FORM></DIV>';
         $renderer->doc .= '<div id="tocseltoggle"><img src="'  . TOCSEL_DIR. 'open.png"></div ><span class="tocsel_title">'  . $lang['toc'] .'</span><div id = "setctoc_out"></div>';
         $renderer->doc .='</div>';
        }        
   }   
}