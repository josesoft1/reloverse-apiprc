<?php

require_once '../../../../Classes/Phpdocx/Create/CreateDocx.php';
// include DOMPDF and create an object. DOMPDF isn't bundled in phpdocx
require_once 'dompdf/autoload.inc.php';
$dompdf = new Dompdf\Dompdf();

$docx = new Phpdocx\Create\CreateDocx();

$docx->setBackgroundColor('FFFFCC');

$text = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, ' .
    'sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut ' .
    'enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut' .
    'aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit ' .
    'in voluptate velit esse cillum dolore eu fugiat nulla pariatur. ' .
    'Excepteur sint occaecat cupidatat non proident, sunt in culpa qui ' .
    'officia deserunt mollit anim id est laborum.';

$docx->addText($text, array('textAlign' => 'left'));

$docx->addText($text, array('textAlign' => 'both'));

$docx->addText($text, array('textAlign' => 'center'));

$docx->addText($text, array('textAlign' => 'distribute'));

$docx->addText($text, array('textAlign' => 'left'));

$docx->addText($text, array('textAlign' => 'right'));

$docx->createDocx('transformDocument_native_12.docx');

$transform = new Phpdocx\Transform\TransformDocAdvDOMPDF('transformDocument_native_12.docx');
$transform->setDOMPDF($dompdf);
$transform->transform('transformDocument_native_12.pdf');