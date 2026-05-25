<?php

namespace App\Http\Controllers\Subscription;

use App\Actions\Payments\CreatePixChargeAction;
use App\Actions\Payments\CreateSubscriptionAction;
use App\Actions\Payments\CreateSubscriptionProviderAction;
use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\SubscriptionPayment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PixCheckoutController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'plan_id' => ['required', 'integer', 'exists:plans,id'],
            'cpf'     => ['required', 'string', 'min:11', 'max:14'],
        ]);

        $user = $request->user();
        $plan = Plan::findOrFail($validated['plan_id']);

        $cpf = preg_replace('/\D/', '', $validated['cpf']);

        if (! $user->document) {
            $user->update(['document' => $cpf]);
        }

        $subscription = (new CreateSubscriptionAction)->execute($user, $plan, 'monthly');
        (new CreateSubscriptionProviderAction)->execute($subscription, 'pague_dev');
        $payment = (new CreatePixChargeAction)->execute($subscription);

        return redirect()->route('checkout.pix.show', $payment);
    }

    public function show(SubscriptionPayment $payment): Response
    {
        return Inertia::render('Checkout/Pix', [
            'paymentId'    => $payment->id,
            'pixCode'      => $payment->pix_code,
            'qrCodeBase64' => $payment->qr_code_base64,
            'expiresAt'    => $payment->expires_at?->toIso8601String(),
            'status'       => $payment->status->value,
            'amount'       => (float) $payment->amount,
        ]);
    }

    public function status(SubscriptionPayment $payment, Request $request): JsonResponse
    {
        abort_if($payment->subscription->user_id !== $request->user()->id, 403);

        return response()->json(['status' => $payment->status->value]);
    }
}
