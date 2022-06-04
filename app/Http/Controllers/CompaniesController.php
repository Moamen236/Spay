<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use Illuminate\Support\Facades\Validator;

class CompaniesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $company = new Company();
        $company = $company->getAll();
        return $company;
    }


    /**
     * Display the company resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $company = new Company();
        $company = $company->find($id);
        if ($company)
            return $company;
        else
            return response()->json(['error' => 'company not found'], 404);
    }

    /**
     * get all payments by company id.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function payments(Request $request)
    {
        $company = new Company();
        $payments = $company->payments($request->company_id);
        return response()->json([
            'status' => true,
            'data' => [
                'payments' => $payments
            ]
        ]);
    }

    /**
     * get company by service
     * 
     * @param $service
     * @return array
     */
    public function findByService(Request $request)
    {
        $service = $request->service;
        $company = new Company();
        $companies = $company->findByService($service);
        $all_companies = [];
        foreach ($companies as $company) {
            $all_companies[] = [
                'id' => $company->id(),
                'name' => $company->data()['name'],
            ];
        }
        return response()->json([
            'status' => true,
            'data' =>[
                'companies' => $all_companies
            ]
        ]);
    }
}

