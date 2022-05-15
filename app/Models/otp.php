<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Google\Cloud\Firestore\FirestoreClient;

class otp extends Model
{
    use HasFactory;

    public $firstore;
    public $collection;
    public $documents;

    public function __construct()
    {
        $this->firstore = new FirestoreClient();
        $this->collection = $this->firstore->collection('otp');
        $this->documents = $this->collection->documents()->rows();
    }

    /**
     * get all payment
     * 
     * @return array of payment`
     */
    public function getAll()
    {
        $documents =  $this->documents;
        $payment = [];
        foreach ($documents as $document) {
            $id = $document->id();
            $payment[] = [
                'id' => $id,
                'data' => $document->data()
            ];
        }
        return $payment;
    }

    /**
     * get client by id
     * 
     * @param  int $id
     * @return array of client
     */
    public function find($id){
        $document = $this->collection->document($id)->snapshot();
        if ($document->exists()) {
            $client = [
                'client_id' => $document->id(),
                'data' => $document->data()
            ];
            return $client;
        }
        return false;
    }

    /**
     * create client
     * 
     * @param  array $data
     * @return array of client
     */
    public function create(array $data){
        $document = $this->collection->add($data);
        return $document->snapshot();
    }

    /**
     * update client
     * 
     * @param  int $id
     * @param  array $data
     * @return array of client
     */
    public function edit ($id, array $data){
        $document = $this->collection->document($id);
        $document->set($data);
        return $document->snapshot();
    }

    /**
     * get by otp
     * 
     * @param  int $otp
     * @return array of otp
     */
    public function findByOtp($otp)
    {
        $documents = $this->collection->where('otp', '=', $otp)->documents();
        if ($documents->rows() != null) {
            $document = $documents->rows()[0];
            return $document;
        }
    }

    /**
     * get by id and delete otp
     * 
     * @param  int $id
     * @return array of company
     */
    public function deleteThisOtp($id)
    {
        $document = $this->collection->document($id);
        $document->delete();
    }

    /**
     * delete otp
     *
     * @param  int $id
     * @return array of otp
     */
    public function deleteOtp($id)
    {
        $document_id = $this->collection->where('client_id', '==', $id)->documents()->rows()[0]->id();
        $document = $this->collection->document($document_id);
        $document->delete();
    }
 
    /**
     * get otp by user id
     *
     * @param  int $id
     * @return array of otp
     */
    public function userOtp($id)
    {
        $collection = $this->collection->where('client_id', '==', $id);
        $documents = $collection->documents();

        if ($documents->rows() != null) {
            $document = $documents->rows()[0];
            return $document;
        }
    }


}
