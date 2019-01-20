<?php
namespace bfd\forms;

use std, gui, framework, bfd;


class MainForm extends AbstractForm
{

    /**
     * @event button_next.action 
     */
    function doButton_nextAction(UXEvent $e = null)
    {    
        $this->next->call();
        $this->drawTable->call();
        $this->appViewer->call();
    }

    /**
     * @event button_back.action 
     */
    function doButton_backAction(UXEvent $e = null)
    {    
        if ($GLOBALS['app_selected'] > 0) {
            $this->back->call();
            $this->drawTable->call();
            $this->appViewer->call();
        }
    }

    /**
     * @event button_select.action 
     */
    function doButton_selectAction(UXEvent $e = null)
    {    
        $this->fileChooser->execute();
        if (!$this->fileChooser->isApplied()) {
            return;
        }
        
        $GLOBALS['app'] = "";
        $GLOBALS['app_selected'] = 0;
        
        $GLOBALS['app_stack'] = 0;
        
        $GLOBALS['input_index'] = 0;
        
        $GLOBALS['memory'] = [0];
        $GLOBALS['selected'] = 0;
        
        $f = fopen($this->fileChooser->file, 'r');
        $f->eachLine(function ($line) {
            $GLOBALS['app'] .= $line;
        });
        fclose($f);
        
        $this->drawTable->call();
        $this->appViewer->call();
    }

    /**
     * @event button_skip_while.action 
     */
    function doButton_skip_whileAction(UXEvent $e = null)
    {
        $this->skipWhile->call();
        
        $this->drawTable->call();
        $this->appViewer->call();
    }

    /**
     * @event button_skip_iteration.action 
     */
    function doButton_skip_iterationAction(UXEvent $e = null)
    {
        $this->skipIteration->call();
        
        $this->drawTable->call();
        $this->appViewer->call();
    }

    /**
     * @event button_run.action 
     */
    function doButton_runAction(UXEvent $e = null)
    {
        while ($GLOBALS['app_selected'] < strlen($GLOBALS['app'])) {\
            $this->next->call();
        }
        
        $this->drawTable->call();
        $this->appViewer->call();
    }

    /**
     * @event button_on_start.action 
     */
    function doButton_on_startAction(UXEvent $e = null)
    {
        $GLOBALS['app_selected'] = 0;
        
        $GLOBALS['app_stack'] = 0;
        
        $GLOBALS['memory'] = [0];
        $GLOBALS['selected'] = 0;
        
        $this->drawTable->call();
        $this->appViewer->call();
        $this->out->text = "Output: ";
    }

    /**
     * @event button.action 
     */
    function doButtonAction(UXEvent $e = null)
    {    
        UXDialog::showAndWait("Формат ввода:\n\n1) Каждый вводимый символ через пробел\n2) Перед вводом числа 'i', перед символом 'c'\n\nПример: i23 c3 cF");
    }

}
