<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Google\Cloud\Firestore\FirestoreClient;

class wallet extends Model
{
    use HasFactory;
    public $firstore;
    public $collection;
    public $documents;

    public function __construct()
    {
        $this->firstore = new FirestoreClient();
        $this->collection = $this->firstore->collection('wallets');
        $this->documents = $this->collection->documents()->rows();
    }

    /**
     * get all wallets
     * 
     * @return array of wallets`
     */
    public function getAll()
    {
        $documents =  $this->documents;
        $wallets = [];
        foreach ($documents as $document) {
            $id = $document->id();
            $wallets[] = [
                'id' => $id,
                'data' => $document->data()
            ];
        }
        return $wallets;
    }

    /**
     * get wallet by id
     * 
     * @param  int $id
     * @return array of client
     */
    public function find($id){
        $document = $this->collection->document($id)->snapshot();
        if ($document->exists()) {
            $client = [
                'id' => $document->id(),
                'data' => $document->data()
            ];
            return $client;
        }
        return false;
    }

    /**
     * get wallet by user id
     * 
     * @param  int $id
     * @return array of client
     */
    public function findByUserId($id){
        $collection = $this->collection->where('client_id', '=', $id);
        $documents = $collection->documents();
        if ($documents->rows() != null) {
            $document = $documents->rows()[0];
            return $document;
        }
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
     * delete client
     * 
     * @param  int $id
     * @return array of client
     */
    public function wallet($id)
    {
        $collection = $this->firstore->collection('wallet');
        $documents = $collection->where('client_id', '==', $id)->documents()->rows();
        $wallet = [];
        foreach ($documents as $document) {
            $wallet[] = [
                'id' => $document->id(),
                'data' => $document->data()
            ];
        }
        return $wallet;
    }

}

