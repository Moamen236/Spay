<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Payment;
use App\Models\recipts;
use App\Models\ServiceCode;
use Illuminate\Http\Request;
use Google\Cloud\Core\Timestamp;
use Spatie\Crypto\Rsa\PrivateKey;
use Illuminate\Support\Facades\Validator;

class PaymentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $Payment = new Payment();
        $Payments = $Payment->getAll();
        return $Payments;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $payment = new Payment;
        $codes = new ServiceCode;

        $validator = Validator::make($request->all(),[
            'company_name' => 'nullable|string',
            'client_id' => 'required|string',
            'service_code' => 'required|string',
            'price' => 'required|string',
            'feeds' => 'required|numeric',
            'pin' => 'nullable|string'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => 'validation failed',
                'data' => $validator->errors()
            ]);
        }
        else{
            $company = $this->getCompany($request->company_name);
            if($company){
                $codes_to_company = $codes->findByCompanyId($company->id());

                $company_codes = [];
                $company_pins = [];
                foreach ($codes_to_company as $code) {
                    $company_codes[] = $code['data']['code'];
                    $company_pins[] = $code['data']['pin'] ?? '';
                }

                if(in_array($request->service_code, $company_codes)){
                    if(in_array($request->pin, $company_pins)){
                        return response()->json([
                            'status' => true,
                            'message' => 'payment successful',
                            'company_codes' => $company_codes,
                            'company_pins' => $company_pins
                        ]);
                        $payment = $payment->create([
                            'company_id' => $company->id(),
                            'client_id' => $request->client_id,
                            'service_code' => $request->service_code,
                            'price' => $request->price,
                            'feeds' => $request->feeds,
                        ]);
                        $receipt = $this->createReceipt($payment, $request->feeds);
                        return response()->json([
                            'status' => true,
                            'message' => 'Payment created successfully',
                            'data' => [
                                'id' => $payment->id(),
                                'company_name' => $company->data()['name'],
                                'client_id' => $payment->data()['client_id'],
                                'service_code' => $payment->data()['service_code'],
                                'price' => (float) $payment->data()['price'] ?? 0,
                                'receipt' => [
                                    'id' => $receipt->id(),
                                    'payment_id' => $receipt->data()['payment_id'],
                                    'feeds' => (float) $receipt->data()['feeds'] ?? 0,
                                    'total' => (float) $receipt->data()['total'] ?? 0,
                                    'date' => $receipt->data()['date']->get()->format('Y-m-d H:i:s'),
                                ]
                            ]
                        ]);
                    }
                    else{
                        return response()->json([
                            'status' => false,
                            'message' => 'Invalid pin',
                        ]);
                    }
                }else{
                    return response()->json([
                        'status' => false,
                        'message' => 'service code not found',
                    ]);
                }
            }else{
                return response()->json([
                    'status' => false,
                    'message' => 'company not found',
                ]);
            }
        }
        
    }

    /**
     * Create receipt for payment.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function createReceipt($payment , $feeds)
    {
        $receipt = new recipts();
        $total = (double) $payment['price'] + (double) $feeds;
        $now_date = new Timestamp(new \DateTime('now'));

        $receipt = $receipt->create([
            'payment_id' => $payment->id(),
            'feeds' => $feeds,
            'total' => number_format($total, 2 , '.' , ','),
            'date' => $now_date
        ]);

        return $receipt;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $payment = new Payment();
        $payment = $payment->find($id);
        if ($payment)
            return $payment;
        else
            return response()->json(['error' => 'Payment not found']);
    }

    /**
     * get company by id
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getCompany($name)
    {
        $company = new Company();
        $company = $company->findByName($name);
        return $company;
    }


    public function rsaPayments(Request $request)
    {
        $payment = new Payment;
        $codes = new ServiceCode;

        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'validation failed',
                'data' => $validator->errors()
            ]);
        } else {

            $key = base64_decode($request->token);
            $privateKey = PrivateKey::fromFile(storage_path('privateKey.pem'), 'my-password');
            $decryptedData = $privateKey->decrypt($key);
            $decryptedData = json_decode($decryptedData, true);

            if($decryptedData){
                $company = $this->getCompany($decryptedData->company_name);
                if ($company) {
                    $codes_to_company = $codes->findByCompanyId($company->id());
    
                    $company_codes = [];
                    $company_pins = [];
                    foreach ($codes_to_company as $code) {
                        $company_codes[] = $code['data']['code'];
                        $company_pins[] = $code['data']['pin'] ?? '';
                    }
    
                    if (in_array($decryptedData->service_code, $company_codes)) {
                        if (in_array($decryptedData->pin, $company_pins)) {
                            return response()->json([
                                'status' => true,
                                'message' => 'payment successful',
                                'company_codes' => $company_codes,
                                'company_pins' => $company_pins
                            ]);
                            $payment = $payment->create([
                                'company_id' => $company->id(),
                                'client_id' => $decryptedData->client_id,
                                'service_code' => $decryptedData->service_code,
                                'price' => $decryptedData->price,
                                'feeds' => $decryptedData->feeds,
                            ]);
                            $receipt = $this->createReceipt($payment, $request->feeds);
                            return response()->json([
                                'status' => true,
                                'message' => 'Payment created successfully',
                                'data' => [
                                    'id' => $payment->id(),
                                    'company_name' => $company->data()['name'],
                                    'client_id' => $payment->data()['client_id'],
                                    'service_code' => $payment->data()['service_code'],
                                    'price' => (float) $payment->data()['price'] ?? 0,
                                    'receipt' => [
                                        'id' => $receipt->id(),
                                        'payment_id' => $receipt->data()['payment_id'],
                                        'feeds' => (float) $receipt->data()['feeds'] ?? 0,
                                        'total' => (float) $receipt->data()['total'] ?? 0,
                                        'date' => $receipt->data()['date']->get()->format('Y-m-d H:i:s'),
                                    ]
                                ]
                            ]);
                        } else {
                            return response()->json([
                                'status' => false,
                                'message' => 'Invalid pin',
                            ]);
                        }
                    } else {
                        return response()->json([
                            'status' => false,
                            'message' => 'service code not found',
                        ]);
                    }
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'company not found',
                    ]);
                }
            }else{
                return response()->json([
                    'status' => true,
                    'message' => 'Payment created successfully hambozo',
                ]);
            }

        }
    }
}