<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 12/28/15
 * Time: 4:07 PM
 */

namespace Simnang\LoanPro\SpecialOperations;


use Psr\Log\InvalidArgumentException;
use Simnang\LoanPro\Entities\Loans\DocumentInfo;
use Simnang\LoanPro\LoanPro;

class DocumentUploader
{

    private function __construct(){}

    private static $docTypes = [
        'CustomerDocuments',
        'LoanDocuments',
    ];

    public static function GetTypes()
    {
        return DocumentUploader::$docTypes;
    }

    public static function UploadDocument(File $file, LoanPro $loanPro, $docType, int $entityId, int $sectionId, DocumentInfo $info = null)
    {
        if(!in_array($docType, DocumentUploader::$docTypes))
            throw new InvalidArgumentException('Invalid DocType provided.');

        $data = [];
        $path = 'odata.svc/'.$docType;

        if($docType == "CustomerDocuments")
            $data['customerId'] = $entityId;
        else
            $data['loanId'] = $entityId;

        $data['sectionId'] = $sectionId;

        $result = $loanPro->fileRequest('POST',$path, $data, $file);
        var_dump($result);
    }
}