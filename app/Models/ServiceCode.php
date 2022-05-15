<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Google\Cloud\Firestore\FirestoreClient;

class ServiceCode extends Model
{
    use HasFactory;

    public $firstore;
    public $collection;
    public $documents;

    public function __construct()
    {
        $this->firstore = new FirestoreClient();
        $this->collection = $this->firstore->collection('service_codes');
        $this->documents = $this->collection->documents()->rows();
    }

    /**
     * get all Codes
     *
     * @return array of codes`
     */
    public function getAll()
    {
        $documents =  $this->documents;
        $codes = [];
        foreach ($documents as $document) {
            $id = $document->id();
            $codes[] = [
                'id' => $id,
                'data' => $document->data()
            ];
        }
        return $codes;
    }

    /**
     * get Code by id
     *
     * @param  int $id
     * @return array of code
     */
    public function find($id){
        $document = $this->collection->document($id)->snapshot();
        if ($document->exists()) {
            $code = [
                'id' => $document->id(),
                'data' => $document->data()
            ];
            return $code;
        }
        return false;
    }

    /**
     * create client
     * 
     * @param  array $data
     * @return array of client
     */
    public function create(array $data)
    {
        $document = $this->collection->add($data);
        return [
            'id' => $document->id(),
            'data' => $document->snapshot()->data()
        ];
    }


    /**
     * get codes by company id
     * 
     * @param  int $id
     * @return array of codes
     * 
     */
    public function findByCompanyId($id)
    {
        $collection = $this->collection->where('company_id' , '=' , $id);
        $documents = $collection->documents()->rows();
        $codes = [];
        foreach ($documents as $document) {
            $id = $document->id();
            $codes[] = [
                'id' => $id,
                'data' => $document->data()
            ];
        }
        return $codes;
    }

    /**
     * get code by code
     * 
     * @param  string $code
     * @return array of code
     * 
     */
    public function findByCode($code)
    {
        $collection = $this->collection->where('code' , '=' , $code);
        $documents = $collection->documents();
        if ($documents->rows() != null) {
            $document = $documents->rows()[0];
            return $document;
        }
        return false;
    }

}
