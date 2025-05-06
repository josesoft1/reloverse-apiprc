<?php

namespace App\Http\Controllers;

use App\Models\Relocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Carbon;

class DocumentController extends Controller
{
    public function getRoute(Request $request){
        $request->validate(['type'=>'required','id'=>'required']);
        
        switch($request->input('type')){
            case 'na':
                return URL::temporarySignedRoute('documents.na',now()->addMinutes(30),['id'=>$request->input('id')]);
                break;
        }
    }

    public function na(Request $request, $id){
        if(!$request->hasValidSignature()){
            abort(401,'Not authorized');
        }

        $relocation = Relocation::first();

        $docx = new \Phpdocx\Create\CreateDocxFromTemplate(storage_path('app/templates/na.docx'));
        // you can also enable stream mode in config/phpdocxconfig.ini
        \Phpdocx\Create\CreateDocx::$streamMode = true;
    

        $variables = [
            'employee.full_name' => $relocation->employee->full_name,
            'employee.nationality' => $relocation->employee->nationality,
            'employee.dob' => Carbon::parse($relocation->employee->date_of_birth)->format('Y-m-d'),
            'employee.title' => $relocation->employee->title,
            'employee.email' => $relocation->employee->email,
            'employee.work_email' => $relocation->employee->work_email,
            'employee.phone' => $relocation->employee->phone,
            'employee.mobile' => $relocation->employee->mobile,
            'employee.company.name' => $relocation->employee->company->name,
            'accomodation.no_baths' => $relocation['na']['accomodation']['no_baths'],
            'accomodation.type_of_property' => $relocation['na']['accomodation']['type_of_property'],
            'accomodation.min_sq' => $relocation['na']['accomodation']['min_sq'],
            'accomodation.furnish' => $relocation['na']['accomodation']['furnish'],
            'accomodation.no_beds' => $relocation['na']['accomodation']['no_beds'],
            'accomodation.combined_kitchen' => $relocation['na']['accomodation']['combined_kitchen'],
            'accomodation.needs_garden' => $relocation['na']['accomodation']['needs_garden'],
            'accomodation.needs_box' => $relocation['na']['accomodation']['needs_box'],
            'accomodation.no_ac_properties' => $relocation['na']['accomodation']['no_ac_properties'],
            'accomodation.notes' => $relocation['na']['accomodation']['notes'],
            'accomodation.notes' => $relocation['na']['accomodation']['notes'],
        ];

        $relatives_table = [['Name','Surname','Relationship','Nationality','Citizenship','Date of birth']];

        foreach($relocation->na['family']['relatives'] as $relative){
            $relatives_table[] = [$relative['name'],$relative['surname'],$relative['relationship'],$relative['nationality'],$relative['citizenship'],Carbon::parse($relative['dob'])->format('Y-m-d')];
        }
        $pets_table = [['Name','Type','Size']];

        foreach($relocation->na['family']['pets'] as $pet){
            $pets_table[] = [$pet['name'],$pet['type'],$pet['size']];
        }

        $paramsTable = array(
            'tableStyle' => 'Tabellaelenco4-colore5',
            'border' => 'single',
            'borderWidth' => 5,
            'tableLayout' => 'autofit', // this is the default value
            'tableWidth' => ['type'=>'pct','value'=>90],
            'columnWidths' => array(1500, 1500, 1500,1500,1500,1500),
            'conditionalFormatting' => ['firstCol'=>false,'noVBand'=>true,'firstRow'=>true]
        );
        
        $options = array('parseLineBreaks' =>true);
        $docx->replaceVariableByText($variables, $options);

        $wf_relatives = new \Phpdocx\Elements\WordFragment($docx);
        $wf_relatives->addTable($relatives_table, $paramsTable);
        
        $wf_pets = new \Phpdocx\Elements\WordFragment($docx);
        $wf_pets->addTable($pets_table, $paramsTable);

        $docx->replaceVariableByWordFragment(['RELATIVES'=>$wf_relatives]);
        $docx->replaceVariableByWordFragment(['PETS'=>$wf_pets]);


        $docx->createDocx('output');

        /*$headers = [
            'Content-type'        => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'Content-Disposition' => 'attachment; filename="download.docx"',
        ];

        return Response::make($docx->createDocx('doc'), 200, $headers);*/

    }
}
