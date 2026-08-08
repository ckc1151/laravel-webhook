<?php

namespace App\Http\Controllers;

use App\Models\WebhookEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class WebhookReceiverController extends Controller
{
    public function __invoke(Request $request)
    {
        $payload = $request->except(['file']);
        $rawBody = $request->getContent();

        if ($payload === [] && $rawBody !== '') {
            $decoded = json_decode($rawBody, true);

            if (is_array($decoded)) {
                $payload = $decoded;
            }
        }

        $filePath = null;
        $fileOriginalName = null;
        $fileMimeType = null;
        $fileSize = null;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store('webhooks');
            $fileOriginalName = $file->getClientOriginalName();
            $fileMimeType = $file->getClientMimeType();
            $fileSize = $file->getSize();
        }

        $webhookEvent = WebhookEvent::create([
            'provider' => $request->header('X-Provider', Arr::get($payload, 'provider')),
            'event_type' => Arr::get($payload, 'event_type', Arr::get($payload, 'event')),
            'transaction_id' => Arr::get($payload, 'transaction_id', Arr::get($payload, 'transaction.id')),
            'payload' => $payload,
            'headers' => $request->headers->all(),
            'raw_body' => $rawBody,
            'file_path' => $filePath,
            'file_original_name' => $fileOriginalName,
            'file_mime_type' => $fileMimeType,
            'file_size' => $fileSize,
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Webhook captured.',
            'id' => $webhookEvent->id,
        ], 201);
    }
}
