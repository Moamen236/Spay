<?php

namespace App\Models;

use App\Models\recipts;
use Illuminate\Database\Eloquent\Model;
use Google\Cloud\Firestore\FirestoreClient;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Company extends Model
{
    use HasFactory;

    public $firstore;
    public $collection;
    public $documents;

    public function __construct()
    {
        $this->firstore = new FirestoreClient();
        $this->collection = $this->firstore->collection('companies');
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
     * get company by id
     * 
     * @param  int $id
     * @return array of company
     */
    public function find($id){
        $document = $this->collection->document($id)->snapshot();
        if ($document->exists()) {
            $company = [
                'id' => $document->id(),
                'data' => $document->data()
            ];
            return $company;
        }
        return false;
    }

    /**
     * get client by id
     * 
     * @param  int $id
     * @return array of client
     */
    public function findByName($name)
    {
        $collection = $this->collection->where('name' , '=' , $name);
        $documents = $collection->documents();
        if ($documents->rows() != null) {
            $document = $documents->rows()[0];
            return $document;
        }
        return false;
    }

    /**
     * get user by email
     * 
     * @param  int $id
     * @return array of company
     */
    public function findByEmail($email)
    {
        $collection = $this->collection->where('email', '=', $email);
        $documents = $collection->documents();
        if ($documents->rows() != null) {
            $document = $documents->rows()[0];
            return $document;
        }
    }

    /**
     * get company by service
     * 
     * @param $service
     * @return array
     */
    public function findByService($service)
    {
        $collection = $this->collection->where('service', '=', $service);
        $documents = $collection->documents()->rows();
        return $documents;
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
     * get receipts by company id
     * 
     * @param  int $id
     * @return array of receipts
     */
    public function payments($id)
    {
        $collection = $this->firstore->collection('payments');
        $documents = $collection->where('company_id', '==', $id)->documents()->rows();
        $payments = [];
        foreach ($documents as $document) {
            $id = $document->id();
            $receipt = new recipts();
            $client = new client();
            $find_client = $client->find($document->data()['client_id']);
            $get_receipt = $receipt->payment($id);
            $payments[] = [
                'id' => $document->id(),
                // 'client_name' => $find_client['data']['name'],
                'service_code' => $document->data()['service_code'],
                'price' => $document->data()['price'],
                'date' => $get_receipt['data']['date']->get()->format('d F Y h:i A') ?? null,
                // 'company_id' => $company->data()['name'],
                // 'receipt' => [
                //     'id' => $get_receipt->id(),
                //     'payment_id' => $get_receipt->data()['payment_id'],
                //     'feeds' => $get_receipt->data()['feeds'],
                //     'total' => $get_receipt->data()['total'],
                // ]
            ];
        }
        return $payments;
    }

}

