<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Support\mailparser\EmlService;

class EmlParserController extends Controller
{
    protected $emlService;

    public function __construct(EmlService $emlService)
    {
        $this->emlService = $emlService;
    }

    public function parseEml(Request $request)
    {
        // Prende il contenuto EML dal corpo della richiesta POST
        $emlContent = $request->input('emlContent');

        // Usa il EmlService per caricare il contenuto EML
        $this->emlService->loadEmlFromString($emlContent);

        // Estrai informazioni utilizzando il EmlService
        $subject = $this->emlService->getHeader('subject');
        $from = $this->emlService->getHeader('from');
        $to = $this->emlService->getHeader('to');

        $html = $this->emlService->getBody();
        
        $body = cleaner_body($html); // Support/helpers.php

        $body_tmp = str_ireplace("</p><p", "</p>\r\n<p", $body);
        $text = strip_tags( $body_tmp );
        
        $attachments = $this->emlService->getAttachments();

        // Preparazione della lista di allegati
        $attachmentsArray = array_map(function ($attachment) {

            $encoding = mb_detect_encoding( $attachment->getContent(), "auto" );
            
            return [
                'filename' => $attachment->getFilename(),
                'filetype' => $attachment->getContentType(),
                'content' => base64_encode($attachment->getContent()),
                'encoding' => $encoding
            ];
        }, $attachments);

        // Costruzione della risposta JSON
        $response = [
            'subject' => $subject,
            // 'from' => $from,
            // 'to' => $to,
            // 'html' => $html,
            'body' => $body,
            // 'text' => $text,
            'attachments' => $attachmentsArray
        ];

        return response()->json($response);
    }
}
