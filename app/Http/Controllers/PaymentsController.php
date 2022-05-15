<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Payment;
use App\Models\recipts;
use App\Models\ServiceCode;
use Google\Cloud\Core\Timestamp;
use Illuminate\Http\Request;
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
            $codes_to_company = $codes->findByCompanyId($company->id());
            $company_codes = [];
            foreach ($codes_to_company as $code) {
                $company_codes[] = $code['data']['code'];
            }
            if(in_array($request->service_code, $company_codes)){
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
            }else{
                return response()->json([
                    'status' => false,
                    'message' => 'service code not found',
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
}