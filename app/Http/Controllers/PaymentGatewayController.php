<?php

namespace App\Http\Controllers;

use App\Models\ChargeLimit;
use App\Models\Currency;
use App\Models\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PaymentGatewayController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        date_default_timezone_set(get_option('timezone', 'Africa/Accra'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $paymentgateways = PaymentGateway::all();
        return view('backend.payment_gateway.list', compact('paymentgateways'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if (!$request->ajax()) {
            return view('backend.payment_gateway.create');
        } else {
            return view('backend.payment_gateway.modal.create');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'   => 'required',
            'slug'   => 'required',
            'image'  => 'nullable|image',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('payment_gateways.create')
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        $image = '';
        if ($request->hasfile('image')) {
            $file  = $request->file('image');
            $image = time() . $file->getClientOriginalName();
            $file->move(public_path() . "/backend/images/gateways/", $image);
        }

        $parameters = array();
        if (!empty($request->parameter_name)) {
            $i = 0;
            foreach ($request->parameter_name as $parameter) {
                $parameters[$parameter] = $request->parameter_value[$i] != null ? $request->parameter_value[$i] : '';
            }
        }

        $paymentgateway                       = new PaymentGateway();
        $paymentgateway->name                 = $request->input('name');
        $paymentgateway->slug                 = $request->input('slug');
        $paymentgateway->image                = $image;
        $paymentgateway->status               = $request->input('status');
        $paymentgateway->parameters           = json_encode($parameters);
        $paymentgateway->supported_currencies = $request->input('supported_currencies');
        $paymentgateway->extra                = $request->input('extra');

        $paymentgateway->save();

        if (!$request->ajax()) {
            return redirect()->route('payment_gateways.create')->with('success', _lang('Saved Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'store', 'message' => _lang('Saved Successfully'), 'data' => $paymentgateway, 'table' => '#payment_gateways_table']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $paymentgateway = PaymentGateway::find($id);
        return view('backend.payment_gateway.edit', compact('paymentgateway', 'id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name'          => 'required',
            'image'         => 'nullable|image',
            'status'        => 'required',
            'exchange_rate' => 'required_if:status,1',
        ], [
            'exchange_rate.required_if' => _lang('Exchange rate is required'),
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('payment_gateways.edit', $id)
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        if ($request->hasfile('image')) {
            $file  = $request->file('image');
            $image = time() . $file->getClientOriginalName();
            $file->move(public_path() . "/backend/images/gateways/", $image);
        }

        DB::beginTransaction();

        $paymentgateway = PaymentGateway::find($id);

        $parameters = array();
        if (!empty($paymentgateway->parameters)) {
            $i = 0;
            foreach ($paymentgateway->parameters as $parameter => $value) {
                $parameters[$parameter] = $request->parameter_value[$parameter] != null ? $request->parameter_value[$parameter] : '';
            }
        }

        $paymentgateway->name = $request->input('name');
        if ($request->hasfile('image')) {
            $paymentgateway->image = $image;
        }
        $paymentgateway->status        = $request->input('status');
        $paymentgateway->currency      = $request->input('currency');
        $paymentgateway->parameters    = json_encode($parameters);
        $paymentgateway->exchange_rate = $request->exchange_rate;

        $paymentgateway->save();

        //Store charge and limits
        $paymentgateway->chargeLimits()->whereNotIn('id', $request->limit_id)->delete();

        if ($request->has('minimum_amount')) {
            foreach ($request->minimum_amount as $key => $value) {

                if (isset($request->limit_id[$key])) {
                    $chargeLimits = ChargeLimit::firstOrNew(['id' => $request->limit_id[$key]]);
                } else {
                    $chargeLimits = new ChargeLimit();
                }

                $chargeLimits->minimum_amount       = $request->minimum_amount[$key];
                $chargeLimits->maximum_amount       = $request->maximum_amount[$key];
                $chargeLimits->fixed_charge         = $request->fixed_charge[$key];
                $chargeLimits->charge_in_percentage = $request->percent_charge[$key];
                $chargeLimits->gateway_id           = $paymentgateway->id;
                $chargeLimits->gateway_type         = get_class($paymentgateway);
                $chargeLimits->save();
            }
        }

        DB::commit();

        if (!$request->ajax()) {
            return redirect()->route('payment_gateways.index')->with('success', _lang('Updated Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Updated Successfully'), 'data' => $paymentgateway, 'table' => '#payment_gateways_table']);
        }
    }
}
