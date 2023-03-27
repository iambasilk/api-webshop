<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Payment;
use App\Payments\PaymentProviderFactory;
use App\Payments\PaymentProcessor;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders = Order::all();
        return response()->json($orders);
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
            'customer_id' => 'required|string|max:255',
            'payed' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $order = new Order($request->all());
        $order->save();

        return response()->json($order, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        return response()->json($order);
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
        $order = Order::find($id);

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'customer_id' => 'string|max:255',
            'payed' => 'boolean',
        ]);


        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $order->fill($request->all());
        $order->save();
        $order = Order::find($id);

        return response()->json($order);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        $order->delete();

        return response()->noContent();
    }

    public function addProductToOrder(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => ['required', 'exists:products,id'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $order = Order::where('id', $id)
            ->where('payed', false)
            ->firstOrFail();

        $product = Product::findOrFail($request->product_id);

        $order->products()->attach($product);

        return response()->json(['message' => 'Successfully attached product to the order.']);
    }

    public function payOrder(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        if ($order->payed) {
            return response()->json([
                'message' => 'The order is already payed'
            ], 400);
        }

        $customer = Customer::where('email', $request->customer_email)->first();
        if ($customer->id !== $order->customer_id) {
            return response()->json([
                'message' => 'Not authorized to access this order.'
            ], 403);
        }

        $superPayPaymentProvider = PaymentProviderFactory::createPaymentProvider('SUPER_PAY');
        $paymentProcessor = new PaymentProcessor($superPayPaymentProvider);
        $paymentSuccessful = $paymentProcessor->processPayment($order->id, $request->value, $customer->email);

        // Store payment details to payment table
        $payment = new Payment();
        $payment->paymentable_id = $order->id;
        $payment->paymentable_type = Order::class;
        $payment->amount = $request->value;
        $payment->user_id = $order->customer_id;
        $payment->payment_provider = 'SUPER_PAY';
        $payment->status = $paymentSuccessful ? 'SUCCESS' : 'FAILED';
        $payment->save();

        if ($paymentSuccessful) {
            $order->payed = true;
            $order->save();

            return response()->json([
                'message' => 'Payment Successful'
            ]);
        } else {
            return response()->json([
                'message' => 'Insufficient Funds'
            ], 400);
        }
    }

}
