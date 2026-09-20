<?php

namespace App\Http\Controllers;

use App\Models\PushSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PushSubscriptionController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'endpoint' => ['required', 'string'],
            'public_key' => ['required', 'string'],
            'auth_token' => ['required', 'string'],
        ]);

        PushSubscription::updateOrCreate(
            ['endpoint_hash' => hash('sha256', $data['endpoint'])],
            [
                'user_id' => $request->user()->id,
                'endpoint' => $data['endpoint'],
                'public_key' => $data['public_key'],
                'auth_token' => $data['auth_token'],
            ]
        );

        return response()->json(['status' => 'subscribed']);
    }

    public function destroy(Request $request): JsonResponse
    {
        $data = $request->validate(['endpoint' => ['required', 'string']]);

        PushSubscription::where('user_id', $request->user()->id)
            ->where('endpoint_hash', hash('sha256', $data['endpoint']))
            ->delete();

        return response()->json(['status' => 'unsubscribed']);
    }
}
