<?php
/**
  * Encrypted Passwords Plugin: Store encrypted passwords with syntax <decrypt></decrypt>
  *
  * @license    GPL2 (http://www.gnu.org/licenses/gpl.html)
  * @author     Wolfgang Reszel <reszel@werbeagentur-willers.de>
  */

if(!defined('DOKU_INC')) die();

class syntax_plugin_encryptedpasswords extends DokuWiki_Syntax_Plugin {

    protected $entry_pattern = '<decrypt>(?=.*?</decrypt>)';
    protected $exit_pattern  = '</decrypt>';

    public function getType(){ return 'protected'; }
    public function getSort(){ return 65; }

    /**
      * Connect pattern to lexer
      */
    public function connectTo($mode) {
        $this->Lexer->addEntryPattern($this->entry_pattern,$mode,'plugin_encryptedpasswords');
    }

    public function postConnect() {
        $this->Lexer->addExitPattern($this->exit_pattern,'plugin_encryptedpasswords');
    }

    /**
      * Handle the match
      */
    public function handle($match, $state, $pos, Doku_Handler $handler) {
        switch ($state) {
            case DOKU_LEXER_ENTER :
                return array($state, $match);
            break;
            case DOKU_LEXER_UNMATCHED :
                return array($state, $match);
            break;
            case DOKU_LEXER_EXIT :
                return array($state, '');
            break;
        }
        return array();
    }

    /**
      * Create output
      */
    public function render($format, Doku_Renderer $renderer, $data) {
        if($format == 'xhtml'){

            list($state, $match) = $data;
            switch ($state) {
                case DOKU_LEXER_ENTER :
                    $renderer->doc.= '';
                break;
                case DOKU_LEXER_UNMATCHED :
                    $id = uniqid();
                    $script = "decryptText(jQuery('.encryptedpasswords'));";
                    if ($this->getConf('reload_seconds') > 0) {
                        $script.= "window.setTimeout('location.reload()',".$this->getConf('reload_seconds')."000);";
                    }
                    $renderer->doc.= '<span class="encryptedpasswords" title="'.$match.'">';
                    $renderer->doc.= '<a title="'.$this->getLang('decrypt').'" onclick="'.$script.'">••••••••••</a>';
                    $renderer->doc.= '</span>';
                break;
                case DOKU_LEXER_EXIT :
                    $renderer->doc .= '';
                break;
            }
            return true;
        }
        return false;
    }
}
