<?php
include_once(__DIR__.'/ClassParser.php');
include_once(__DIR__.'/TextTable.php');
class ClassMarkdown {
    public $file;
    public function __construct() {
    }
    protected static function parseClass($class){
        $rows = array();
        $parameters = array();
        $return = '';
        foreach ($class['functions'] as $key => $value) {
              $row = array();
              $description = array();
              $queries = array('param','return');
              $row[] = $key;
              $value['doc'] = trim(str_replace(array("\r","*","/",'@','|'),array('','','','','\|'),$value['doc']));
              $value['doc'] = explode("\n", $value['doc']);
              foreach ($value['doc'] as $line) {
                   $line = trim(preg_replace('~[.[:cntrl:]]~', '', $line));
                  if(preg_match('/^param/i', $line)){
                      $line = str_replace('param ','',$line);
                      $parameters[] = $line;
                  } else if(preg_match('/^return/i', $line)){
                      $line = str_replace('\|','<br>',$line);
                      $line = str_replace('return ','',$line);
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
    public static function getMarkdown($file) {
        $result = '';
        $cp = new ClassParser();
        $cp->parse($file);
        $classes = $cp->getClasses();
        foreach ($classes as $k => $class){
            $class['name'] = $k;
            $result .= "### ".$class['name'].PHP_EOL.PHP_EOL;
            $rows = self::parseClass($class);
            $columns = ['Method','Description','Type','Parameters','Return'];
            $t = new TextTable($columns, $rows);
            $result .= $t->render();
            $result .= PHP_EOL.PHP_EOL;
        }
        return $result;
    }
    public static function printMarkdown($file) {
        echo self::getMarkdown($file);
    }
    public static function getArray($file){
        $result = array();
        $cp = new ClassParser();
        $cp->parse($file);
        $classes = $cp->getClasses();
        foreach ($classes as $k => $class){
            $class['name'] = $k;
            $result[] = self::parseClass($class);
        }
        return $result;
    }
}
