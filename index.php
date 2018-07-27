<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'classes/ClassMarkdown.php';
ClassMarkdown::printMarkdown('sample.class.php');
