<?php
namespace bfd\modules;

use Error;
use std, gui, framework, bfd;


class MainModule extends AbstractModule
{

    /**
     * @event drawTable.action 
     */
    function doDrawTableAction(ScriptEvent $e = null)
    {
        $lines = intdiv($GLOBALS['memory'], 10);
        $rows = $GLOBALS['memory'] % 10;
    
        $lineSelected = intdiv($GLOBALS['selected'], 10);
        $rowSelected = $GLOBALS['selected'] % 10;
        
        $this->table->items->clear();
        
        for ($y = 0; $y <= $lines; $y++) {
            $line = ['X' => "${y}x"];
            for ($x = 0; $x < 10 && ($y * 10 + $x <= count($GLOBALS['memory'])); $x++) {
                if ($y * 10 + $x === $GLOBALS['selected']) {
                    $label = new UXLabelEx($GLOBALS['memory'][$y * 10 + $x]);
                    $label->backgroundColor = UXColor::of('#d95b5b');
                    $label->textColor = UXColor::of('#ffffff');
                    $line[$x] = $label;
                } else {
                    $line[$x] = $GLOBALS['memory'][$y * 10 + $x];
                }
                
            }
            $this->table->items->add($line);
        }
    }

    /**
     * @event next.action 
     */
    function doNextAction(ScriptEvent $e = null)
    {    
        switch ($GLOBALS['app'][$GLOBALS['app_selected']]) {
            case '>':
                $GLOBALS['selected']++;
                if (!isset($GLOBALS['memory'][$GLOBALS['selected']])) {
                    $GLOBALS['memory'][$GLOBALS['selected']] = 0;
                }
                break;
            case '<':
                $GLOBALS['selected']--;
                break;
            case "+":
                $GLOBALS['memory'][$GLOBALS['selected']]++;
                break;
            case "-":
                $GLOBALS['memory'][$GLOBALS['selected']]--;
                break;
            case ".":
                $this->out->text .= chr($GLOBALS['memory'][$GLOBALS['selected']]);
                break;
            case ",":
                $this->getInput->call();
                break;
            case "[":
                if ($GLOBALS['memory'][$GLOBALS['selected']] == 0) {
                   $GLOBALS['app_stack']++;
                    while ($GLOBALS['app_stack'] !== 0) {
                        $GLOBALS['app_selected']++;
                        if ($GLOBALS['app'][$GLOBALS['app_selected']] == '[')
                            $GLOBALS['app_stack']++;
                        if ($GLOBALS['app'][$GLOBALS['app_selected']] == ']')
                            $GLOBALS['app_stack']--;
                    }
                } else {
                    continue;
                }
                break;
            case "]":
                if ($GLOBALS['memory'][$GLOBALS['selected']] == 0) {
                    continue;
                } else {
                    if ($GLOBALS['app'][$GLOBALS['app_selected']] == ']') {
                        $GLOBALS['app_stack']++;
                    }
                    while ($GLOBALS['app_stack'] !== 0) {
                        $GLOBALS['app_selected']--;
                        if ($GLOBALS['app'][$GLOBALS['app_selected']] == '[')
                            $GLOBALS['app_stack']--;
                        if ($GLOBALS['app'][$GLOBALS['app_selected']] == ']')
                            $GLOBALS['app_stack']++;
                    }
                    $GLOBALS['app_selected']--;
                }
                break;
        }
        $GLOBALS['app_selected']++;
    }

    /**
     * @event appViewer.action 
     */
    function doAppViewerAction(ScriptEvent $e = null)
    {    
        $this->app->text = "App: ";
        for ($i = 0; $i < $GLOBALS['app_selected']; $i++) {
            $this->app->text .= $GLOBALS['app'][$i];
        }
        
        $this->app->text .= " ( " . $GLOBALS['app'][$GLOBALS['app_selected']] . " ) ";
        
        for ($i = $GLOBALS['app_selected'] + 1; $i < strlen($GLOBALS['app']); $i++) {
            $this->app->text .= $GLOBALS['app'][$i];
        }
    }

    /**
     * @event skipIteration.action 
     */
    function doSkipIterationAction(ScriptEvent $e = null)
    {    
        while ($GLOBALS['app'][$GLOBALS['app_selected']] !== ']') {
            $this->next->call();
        }
        $this->next->call();
    }

    /**
     * @event skipWhile.action 
     */
    function doSkipWhileAction(ScriptEvent $e = null)
    {    
        while ($GLOBALS['app'][$GLOBALS['app_selected']] !== ']') {
            $this->next->call();
        }
        
        $closeWhile = $GLOBALS['app_selected'];
        
        while ($closeWhile >= $GLOBALS['app_selected']) {
            $this->next->call();
        }
    }

    /**
     * @event getInput.action 
     */
    function doGetInputAction(ScriptEvent $e = null)
    {    
        $in = explode(' ', $this->in->text)[$GLOBALS['input_index']];
        
        $inT = $in[0];
        $inV = substr($in, 1);
        
        if ($inT === "i") {
            $GLOBALS['memory'][$GLOBALS['selected']] = intval($inV);
        } else {
            $GLOBALS['memory'][$GLOBALS['selected']] = ord($inV);
        }
        
        $GLOBALS['input_index']++;
    }

    /**
     * @event back.action 
     */
    function doBackAction(ScriptEvent $e = null)
    {    
        $GLOBALS['app_selected']--;
        switch ($GLOBALS['app'][$GLOBALS['app_selected']]) {
            case '>':
                $GLOBALS['selected']--;
                break;
            case '<':
                $GLOBALS['selected']++;
                if (!isset($GLOBALS['memory'][$GLOBALS['selected']])) {
                    $GLOBALS['memory'][$GLOBALS['selected']] = 0;
                }
                break;
            case "+":
                $GLOBALS['memory'][$GLOBALS['selected']]--;
                break;
            case "-":
                $GLOBALS['memory'][$GLOBALS['selected']]++;
                break;
            case ".":
                $this->out->text = ubstr($this->out->text, 0, -1);
                break;
            case ",":
                //$GLOBALS['input_index']--;
                UXDialog::showAndWait("Восстановить старую ячейку невозможно");
                $GLOBALS['app_selected']++;
                break;
            case "[":
                UXDialog::showAndWait("Использовать на циклах нельзя");
                $GLOBALS['app_selected']++;
                /*if ($GLOBALS['memory'][$GLOBALS['selected']] == 0) {
                   $GLOBALS['app_stack']++;
                    while ($GLOBALS['app_stack'] !== 0) {
                        $GLOBALS['app_selected']++;
                        if ($GLOBALS['app'][$GLOBALS['app_selected']] == '[')
                            $GLOBALS['app_stack']++;
                        if ($GLOBALS['app'][$GLOBALS['app_selected']] == ']')
                            $GLOBALS['app_stack']--;
                    }
                } else {
                    continue;
                }*/
                break;
            case "]":
                UXDialog::showAndWait("Использовать на циклах нельзя");
                $GLOBALS['app_selected']++;
                /*if ($GLOBALS['memory'][$GLOBALS['selected']] == 0) {
                    continue;
                } else {
                    if ($GLOBALS['app'][$GLOBALS['app_selected']] == ']') {
                        $GLOBALS['app_stack']++;
                    }
                    while ($GLOBALS['app_stack'] !== 0) {
                        $GLOBALS['app_selected']--;
                        if ($GLOBALS['app'][$GLOBALS['app_selected']] == '[')
                            $GLOBALS['app_stack']--;
                        if ($GLOBALS['app'][$GLOBALS['app_selected']] == ']')
                            $GLOBALS['app_stack']++;
                    }
                    $GLOBALS['app_selected']--;
                }*/
                break;
        }
        
    }
}
