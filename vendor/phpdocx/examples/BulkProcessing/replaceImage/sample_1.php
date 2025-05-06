<?php

require_once '../../../Classes/Phpdocx/Create/CreateDocx.php';

$bulk = new Phpdocx\Utilities\BulkProcessing('../../files/bulk.docx');

// replace image placeholders using bulk methods, generate a single DOCX output

$variables = 
array(
	array(
		'LOGO' => '../../img/imageP1.png',
	),
);
$bulk->replaceImage($variables);
$documents = $bulk->getDocuments();

$documents[0]->saveDocx('example_replaceImage_1');