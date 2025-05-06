<?php

require_once '../../../Classes/Phpdocx/Create/CreateDocx.php';

$docx = new Phpdocx\Crypto\CryptoPHPDOCX();
$source = '../../files/Text.docx';
$target = 'protected.docx';
$docx->protectDOCX($source, $target, array('password' => 'phpdocx'));