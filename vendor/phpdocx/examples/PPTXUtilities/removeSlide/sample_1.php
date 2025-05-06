<?php

require_once '../../../Classes/Phpdocx/Create/CreateDocx.php';

$newPPTX = new Phpdocx\Utilities\PPTXUtilities();

$newPPTX->removeSlide('../../files/data_powerpoint.pptx', 'example_removeSlide_1.pptx', array('slideNumber' => array(1, 3)));