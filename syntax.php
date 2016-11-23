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
require_once(DOKU_PLUGIN.'syntax.php');
define('REPLACE_DIR', DOKU_INC . 'data/meta/macros/');
define('MACROS_FILE', REPLACE_DIR . 'macros.ser');


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
    }
    
  function handle($match, $state, $pos, Doku_Handler $handler) {
           return array($state,$match);
  }

   function render($mode, Doku_Renderer $renderer, $data) {
        if($mode == 'xhtml'){           
         $renderer->doc .=  '<DIV><FORM><input type="button" value="Select" id="selectoc_btn"  name="selectoc_btn" style="font-size:10pt;" /> <INPUT type="text" id="selectoc_id" name="selectoc_id" value="wiki:id"></FORM></DIV>';
        // $renderer->doc .=  '<DIV><FORM><input type="button" value="Select" id="selectoc_btn"  name="selectoc_btn" style="font-size:10pt;" onclick="get_seltoc(this);" /> <INPUT type="text" id="selectoc_id" name="selectoc_id" value="wiki:id"></FORM></DIV>';
          $renderer->doc .= '<div id = "setctoc_out"></div>';
        }        
   }   
}