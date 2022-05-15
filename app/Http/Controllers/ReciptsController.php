<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\recipts;
use Illuminate\Support\Facades\Validator;

class ReciptsController extends Controller
{
   /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $recipts = new recipts();
        $recipts = $recipts->getAll();
        return $recipts;
    }

    /**
     * Store a newly created resource in storage.
     * 3del ya heshaaaaaam b3d el data
     * 7eta security
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        
        $validator =  Validator::make($request->all(),[
            'date' => 'required|string',
            'feed' => 'required|numeric',
            'payment_id' => 'requried|numeric',
            'total' => 'requried|numeric',
            
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 'error',
                'message' => 'validation faild',
                'data' => $validator->errors()
            ], 400);
        }
        else{
            $recipts = new recipts();
            $createrecipts = $recipts->create($data);
        }
        if ($createrecipts)
            return response()->json(['success' => true, 'data' => $createrecipts], 200);
        else
            return response()->json(['error' => 'Something went wrong'], 500);
    }

    /**
     * Display the recipts resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $recipts = new recipts();
        $recipts = $recipts->find($id);
        if ($recipts)
            return $recipts;
        else
            return response()->json(['error' => 'recipts not found'], 404);
    }

    /**
     * Update the recipts resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();
        $validator =  Validator::make($request->all(),[
            'date' => 'required|string',
            'feed' => 'required|numeric',
            'payment_id' => 'requried|numeric',
            'total' => 'requried|numeric',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 'error',
                'message' => 'validation faild',
                'data' => $validator->errors()
            ], 400);
        }
        else{
            $recipts = new recipts();
            $updaterecipts = $recipts->edit($id, $data);
        }

        if ($updaterecipts)
            return $updaterecipts;
        else
            return response()->json(['error' => 'Something went wrong'], 500);
    }

    /**
     * get all reciptss of recipts
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function reciptss($id)
    {
        $recipts = new recipts();
        $reciptss = $recipts->reciptss($id);
        if ($reciptss)
            return $reciptss;
        else
            return response()->json(['error' => 'recipts not found'], 404);
}
}


