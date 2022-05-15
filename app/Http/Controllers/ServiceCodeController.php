<?php

namespace App\Http\Controllers;

use App\Models\ServiceCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class ServiceCodeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $code = new ServiceCode();
        $codes = $code->getAll();
        return $codes;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $code = new ServiceCode();
        $validator = Validator::make($request->all(),[
            'company_id' => 'required|string',
            'code' => 'required|string',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => 'validation failed',
                'data' => $validator->errors()
            ]);
        }else{
            $store_code = $code->create([
                'company_id' => $request->company_id,
                'code' => $request->code,
            ]);
            return response()->json([
                'status' => true,
                'message' => 'code created successfully',
                'data' => $store_code
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $code = new ServiceCode();
        $code = $code->find($id);
        return $code;
    }
}
