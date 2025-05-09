<?php

require_once '../../../Classes/Phpdocx/Create/CreateDocx.php';

$bulk = new Phpdocx\Utilities\BulkProcessing('../../files/bulk.docx');

// replace table placeholders using bulk methods, generate a DOCX for each array value

$variables =
array(
    // first DOCX
	array(
        array(
            array(
                'TABLE_1_PROJECT' => 'Project A.1',
                'TABLE_1_DATE' => date('Y/m/d'),
                'TABLE_1_ID' => '1A45A',
            ),
            array(
                'TABLE_1_PROJECT' => 'Project B.1',
                'TABLE_1_DATE' => date('Y/m/d'),
                'TABLE_1_ID' => 'EA78A',
            ),
            array(
                'TABLE_1_PROJECT' => 'Project C.1',
                'TABLE_1_DATE' => date('Y/m/d'),
                'TABLE_1_ID' => 'YA99A',
	       ),
        ),
        array(
            array(
                'TABLE_2_ID' => 'ID122',
                'TABLE_2_NAME' => 'Name A',
                'TABLE_2_PROJECT' => 'Project 2A',
                'TABLE_2_DATE_START' => date('Y/m/d'),
                'TABLE_2_DATE_END' => date('Y/m/d'),
            ),
            array(
                'TABLE_2_ID' => 'ID123',
                'TABLE_2_NAME' => 'Name B',
                'TABLE_2_PROJECT' => 'Project 2B',
                'TABLE_2_DATE_START' => date('Y/m/d'),
                'TABLE_2_DATE_END' => date('Y/m/d'),
            ),
            array(
                'TABLE_2_ID' => 'ID124',
                'TABLE_2_NAME' => 'Name C',
                'TABLE_2_PROJECT' => 'Project 2C',
                'TABLE_2_DATE_START' => date('Y/m/d'),
                'TABLE_2_DATE_END' => date('Y/m/d'),
            ),
            array(
                'TABLE_2_ID' => 'ID125',
                'TABLE_2_NAME' => 'Name D',
                'TABLE_2_PROJECT' => 'Project 2D',
                'TABLE_2_DATE_START' => date('Y/m/d'),
                'TABLE_2_DATE_END' => date('Y/m/d'),
            ),
            array(
                'TABLE_2_ID' => 'ID126',
                'TABLE_2_NAME' => 'Name E',
                'TABLE_2_PROJECT' => 'Project 2E',
                'TABLE_2_DATE_START' => date('Y/m/d'),
                'TABLE_2_DATE_END' => date('Y/m/d'),
            ),
        ),
    ),
    // second DOCX
    array(
        array(
            array(
                'TABLE_1_PROJECT' => 'Project A.2',
                'TABLE_1_DATE' => date('Y/m/d'),
                'TABLE_1_ID' => '1A45A',
            ),
            array(
                'TABLE_1_PROJECT' => 'Project B.2',
                'TABLE_1_DATE' => date('Y/m/d'),
                'TABLE_1_ID' => 'EA78A',
            ),
            array(
                'TABLE_1_PROJECT' => 'Project C.2',
                'TABLE_1_DATE' => date('Y/m/d'),
                'TABLE_1_ID' => 'YA99A',
           ),
        ),
        array(
            array(
                'TABLE_2_ID' => 'ID122',
                'TABLE_2_NAME' => 'Name A',
                'TABLE_2_PROJECT' => 'Project 2A',
                'TABLE_2_DATE_START' => date('Y/m/d'),
                'TABLE_2_DATE_END' => date('Y/m/d'),
            ),
            array(
                'TABLE_2_ID' => 'ID123',
                'TABLE_2_NAME' => 'Name B',
                'TABLE_2_PROJECT' => 'Project 2B',
                'TABLE_2_DATE_START' => date('Y/m/d'),
                'TABLE_2_DATE_END' => date('Y/m/d'),
            ),
            array(
                'TABLE_2_ID' => 'ID124',
                'TABLE_2_NAME' => 'Name C',
                'TABLE_2_PROJECT' => 'Project 2C',
                'TABLE_2_DATE_START' => date('Y/m/d'),
                'TABLE_2_DATE_END' => date('Y/m/d'),
            ),
            array(
                'TABLE_2_ID' => 'ID125',
                'TABLE_2_NAME' => 'Name D',
                'TABLE_2_PROJECT' => 'Project 2D',
                'TABLE_2_DATE_START' => date('Y/m/d'),
                'TABLE_2_DATE_END' => date('Y/m/d'),
            ),
            array(
                'TABLE_2_ID' => 'ID126',
                'TABLE_2_NAME' => 'Name E',
                'TABLE_2_PROJECT' => 'Project 2E',
                'TABLE_2_DATE_START' => date('Y/m/d'),
                'TABLE_2_DATE_END' => date('Y/m/d'),
            ),
        ),
    ),
    // third DOCX
    array(
        array(
            array(
                'TABLE_1_PROJECT' => 'Project A.3',
                'TABLE_1_DATE' => date('Y/m/d'),
                'TABLE_1_ID' => '1A45A',
            ),
            array(
                'TABLE_1_PROJECT' => 'Project B.3',
                'TABLE_1_DATE' => date('Y/m/d'),
                'TABLE_1_ID' => 'EA78A',
            ),
            array(
                'TABLE_1_PROJECT' => 'Project C.3',
                'TABLE_1_DATE' => date('Y/m/d'),
                'TABLE_1_ID' => 'YA99A',
           ),
        ),
        array(
            array(
                'TABLE_2_ID' => 'ID122',
                'TABLE_2_NAME' => 'Name A',
                'TABLE_2_PROJECT' => 'Project 2A',
                'TABLE_2_DATE_START' => date('Y/m/d'),
                'TABLE_2_DATE_END' => date('Y/m/d'),
            ),
            array(
                'TABLE_2_ID' => 'ID123',
                'TABLE_2_NAME' => 'Name B',
                'TABLE_2_PROJECT' => 'Project 2B',
                'TABLE_2_DATE_START' => date('Y/m/d'),
                'TABLE_2_DATE_END' => date('Y/m/d'),
            ),
            array(
                'TABLE_2_ID' => 'ID124',
                'TABLE_2_NAME' => 'Name C',
                'TABLE_2_PROJECT' => 'Project 2C',
                'TABLE_2_DATE_START' => date('Y/m/d'),
                'TABLE_2_DATE_END' => date('Y/m/d'),
            ),
            array(
                'TABLE_2_ID' => 'ID125',
                'TABLE_2_NAME' => 'Name D',
                'TABLE_2_PROJECT' => 'Project 2D',
                'TABLE_2_DATE_START' => date('Y/m/d'),
                'TABLE_2_DATE_END' => date('Y/m/d'),
            ),
            array(
                'TABLE_2_ID' => 'ID126',
                'TABLE_2_NAME' => 'Name E',
                'TABLE_2_PROJECT' => 'Project 2E',
                'TABLE_2_DATE_START' => date('Y/m/d'),
                'TABLE_2_DATE_END' => date('Y/m/d'),
            ),
        ),
    ),
);

$bulk->replaceTable($variables, $options);
$documents = $bulk->getDocuments();

for ($i = 0; $i < count($documents); $i++) {
    $documents[$i]->saveDocx('example_replaceTable_2_' . ($i + 1));
}