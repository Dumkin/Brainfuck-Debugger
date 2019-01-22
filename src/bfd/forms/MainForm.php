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
        $this->interfaceCheck->call();
    }

    /**
     * @event button_back.action 
     */
    function doButton_backAction(UXEvent $e = null)
    {    
        $this->back->call();
        $this->interfaceCheck->call();
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
        
        $this->appClear->call();
        
        $f = fopen($this->fileChooser->file, 'r');
        $f->eachLine(function ($line) {
            $GLOBALS['app'] .= $line;
        });
        fclose($f);
        
        $GLOBALS['app_open'] = true;
        
        $this->interfaceCheck->call();
    }

    /**
     * @event button_skip_while.action 
     */
    function doButton_skip_whileAction(UXEvent $e = null)
    {
        $this->skipWhile->call();
        
        $this->interfaceCheck->call();
    }

    /**
     * @event button_skip_iteration.action 
     */
    function doButton_skip_iterationAction(UXEvent $e = null)
    {
        $this->skipIteration->call();
        
        $this->interfaceCheck->call();
    }

    /**
     * @event button_to_end.action 
     */
    function doButton_to_endAction(UXEvent $e = null)
    {
        $this->appEnd->call();
        
        $this->interfaceCheck->call();
    }

    /**
     * @event button_to_begin.action 
     */
    function doButton_to_beginAction(UXEvent $e = null)
    {
        $this->appBegin->call();
        
        $this->interfaceCheck->call();
        
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
