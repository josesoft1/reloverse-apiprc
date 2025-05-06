<?php

require_once '../../../Classes/Phpdocx/Create/CreateDocx.php';

$docx = new Phpdocx\Utilities\DOCXStrict();
var_dump($docx->checkVariant('../../files/no_strict.docx'));

$docx->generateStrictVariant('../../files/no_strict.docx', 'strict_variant_output.docx');