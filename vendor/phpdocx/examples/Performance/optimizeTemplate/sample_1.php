<?php

require_once '../../../Classes/Phpdocx/Utilities/ProcessTemplate.php';

$docx = new Phpdocx\Utilities\ProcessTemplate();
$docx->optimizeTemplate('../../files/template_not_optimized.docx', 'template_optimized.docx', array('VAR_1_1', 'VAR_1_2', 'VAR_1_3', 'VAR_2_1', 'VAR_2_2', 'VAR_2_3'));