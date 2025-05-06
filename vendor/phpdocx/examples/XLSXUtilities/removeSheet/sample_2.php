<?php

require_once '../../../Classes/Phpdocx/Create/CreateDocx.php';

$newXLSX = new Phpdocx\Utilities\XLSXUtilities();

$newXLSX->removeSheet('../../files/data_excel.xlsx', 'example_removeSheet_2.xlsx', array('sheetName' => array('Chart')));