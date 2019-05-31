<?php

namespace marcocesarato\markdown;


/**
 * Class Markdown Docs
 * @author Marco Cesarato <cesarato.developer@gmail.com>
 * @copyright Copyright (c) 2018
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link https://github.com/marcocesarato/PHP-Class-Markdown-Docs
 * @version 0.1.9
 */
class Markdown
{
    public $file;

    /**
     * ClassMarkdown constructor.
     */
    public function __construct()
    {
    }

    /**
     * Parse a given class var
     * @param $class
     * @return array
     */
    protected static function parseClass($class)
    {
        $rows = array();
        foreach ($class['functions'] as $key => $value) {
            $row = array();
            $description = array();
            $parameters = array();
            $return = '';
            $row[] = $key;
            $value['doc'] = trim(str_replace(array("\r", "*", "/", '@', '|'), array('', '', '', '', '\|'), $value['doc']));
            $value['doc'] = explode("\n", $value['doc']);
            foreach ($value['doc'] as $line) {
                $line = trim(preg_replace('~[.[:cntrl:]]~', '', $line));
                if (preg_match('/^param/i', $line)) {
                    $line = str_replace('param ', '', $line);
                    $parameters[] = $line;
                } else if (preg_match('/^return/i', $line)) {
                    $line = str_replace('\|', '<br>', $line);
                    $line = str_replace('return ', '', $line);
                    $return = $line;
                } else {
                    $description[] = $line;
                }
            }
            $row[] = implode('<br>', $description);
            $row[] = implode('<br>', $value['modifiers']);
            $row[] = implode('<br>', $parameters);
            $row[] = $return;
            $rows[] = $row;
        }
        return $rows;
    }

    /**
     * Get markdown class documentation
     * @param $file
     * @return string
     */
    public static function getMarkdown($file)
    {
        $result = '';
        $cp = new ClassParser();
        $cp->parse($file);
        $classes = $cp->getClasses();
        foreach ($classes as $k => $class) {
            $class['name'] = $k;
            $result .= "### " . $class['name'] . PHP_EOL . PHP_EOL;
            $rows = self::parseClass($class);
            $columns = ['Method', 'Description', 'Type', 'Parameters', 'Return'];
            $t = new TextTable($columns, $rows);
            $result .= $t->render();
            $result .= PHP_EOL . PHP_EOL;
        }
        return $result;
    }

    /**
     * Print Markdown class documentation
     * @param $file
     */
    public static function printMarkdown($file)
    {
        echo self::getMarkdown($file);
    }

    /**
     * Get php array class documentation
     * @param $file
     * @return array
     */
    public static function getArray($file)
    {
        $result = array();
        $cp = new ClassParser();
        $cp->parse($file);
        $classes = $cp->getClasses();
        foreach ($classes as $k => $class) {
            $class['name'] = $k;
            $result[] = self::parseClass($class);
        }
        return $result;
    }
}
