<?php

//$phar = new Phar(__DIR__.'/../dist/tmpl.phar', FilesystemIterator::CURRENT_AS_FILEINFO | FilesystemIterator::KEY_AS_FILENAME, 'tmpl');
//foreach (new RecursiveIteratorIterator($phar) as $file) {
//    if ( preg_match('/tmpl\.phar\/(src)/', $file) ) {
//        require_once $file;
//    }
//}
require_once __DIR__.'/../vendor/autoload.php';