<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plan;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use GuzzleHttp\Client;
use App\Models\Transaction;


class CheckoutController extends Controller
{
    public function stripe(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
        ]);

        $plan = Plan::findOrFail($request->plan_id);

        Stripe::setApiKey(config('stripe.secret'));

        $session = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => strtolower($plan->currency),
                    'product_data' => [
                        'name' => $plan->name,
                    ],
                    'unit_amount' => $plan->price * 100, // in cents
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => url('/success'),
            'cancel_url' => url('/cancel'),
        ]);
        $userId = $request->input('user_id');  // get from request

        Transaction::create([
            'user_id' => $userId,
            'plan_id' => $plan->id,
            'amount' => $plan->price,
            'currency' => $plan->currency,
            'provider' => 'stripe',
            'status' => 'pending', // or 'initiated'
        ]);
        return response()->json([
            'url' => $session->url
        ]);
    }

    public function paypal(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
        ]);

        $plan = Plan::findOrFail($request->plan_id);

        $client = new Client();

        // Step 1: Get access token
        $response = $client->post(config('paypal.base_url') . '/v1/oauth2/token', [
            'auth' => [config('paypal.client_id'), config('paypal.client_secret')],
            'form_params' => [
                'grant_type' => 'client_credentials',
            ],
        ]);

        $body = json_decode($response->getBody(), true);
        $accessToken = $body['access_token'];

        // Step 2: Create order
        $order = $client->post(config('paypal.base_url') . '/v2/checkout/orders', [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'amount' => [
                        'currency_code' => $plan->currency,
                        'value' => $plan->price,
                    ],
                    'description' => $plan->description,
                ]],
                'application_context' => [
                    'return_url' => url('/success'),
                    'cancel_url' => url('/cancel'),
                ],
            ],
        ]);

        $orderBody = json_decode($order->getBody(), true);

        // Step 3: Find approval link
        $approvalLink = collect($orderBody['links'])->firstWhere('rel', 'approve')['href'] ?? null;

$userId = $request->input('user_id');  // get from request

        Transaction::create([
            'user_id' => $userId,
            'plan_id' => $plan->id,
            'amount' => $plan->price,
            'currency' => $plan->currency,
            'provider' => 'paypal',
            'status' => 'pending', // or 'initiated'
        ]);

        return response()->json([
            'url' => $approvalLink
        ]);
        
    }
    public function myTransactions(Request $request)
{
    $transactions = $request->user()->transactions()
        ->with(['plan', 'user'])  // eager load related models
        ->orderByDesc('created_at')
        ->get();

    return response()->json($transactions);
}}