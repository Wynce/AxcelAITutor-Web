<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
use App\Models\User;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\ApnsConfig;
use Kreait\Firebase\Messaging\Topic;
use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    public function sendFCM($topic, $title, $body, $image = null)
    {
        try {
            $firebase = (new Factory)->withServiceAccount(env('FIREBASE_CREDENTIALS'));
            $messaging = $firebase->createMessaging();

            $imageUrl = $image ?? url('assets/settings/axcel_logo.png');

            // Common data payload (works for Android & background on iOS)
            $dataPayload = [
                'title' => $title,
                'body' => $body,
                'image_url' => $imageUrl,
                'priority' => 'high',
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ];

            // $notification = array(
            //     'title' => $title,
            //     'body' => $body,
            //     'image' => $image ?? url('assets/settings/axcel_logo.png')
            // );

            // Notification for both Android/iOS
            $notification = FirebaseNotification::create($title, $body, $imageUrl);

            // iOS APNs config
            $apnsConfig = ApnsConfig::fromArray([
                'headers' => [
                    'apns-priority' => '10',
                ],
                'payload' => [
                    'aps' => [
                        'alert' => [
                            'title' => $title,
                            'body' => $body,
                        ],
                        'sound' => 'default',
                        'badge' => 1,
                    ],
                ],
            ]);

            // Helper to send to a topic
            $sendToTopic = function ($singleTopic) use ($messaging, $notification, $dataPayload, $apnsConfig) {
                $message = CloudMessage::withTarget('topic', $singleTopic)
                    ->withNotification($notification)
                    ->withData($dataPayload)
                    ->withApnsConfig($apnsConfig);

                $response = $messaging->send($message);
                Log::info('FCM sent to topic: ' . $singleTopic);
                return $response;
            };

            if ($topic === 'all') {
                $sendToTopic('all');
            } else {
                $topics = explode(',', $topic);
                foreach ($topics as $singleTopic) {
                    $sendToTopic(trim($singleTopic));
                }
            }

            return response()->json([
                'success' => 'Notification sent successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Firebase Messaging Error: ' . $e->getMessage(), [
                'exception' => $e,
                'topic' => $topic,
                'title' => $title,
                'body' => $body,
            ]);

            return response()->json([
                'status' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTrace(),
            ], 500);
        }
    }

    public function sendFCMToTopic(Request $req)
    {

        try {
            $topic = $req->topic;
            $title = $req->title;
            $body = $req->body;
            $image = $req->image;

            $sent = $this->sendFCM($topic, $title, $body, $image);
            // return $sent;
            return redirect()->back()->with('success', 'Notification sent successfully!');
        } catch (\Exception $e) {
            // Log the full exception for debugging
            Log::error('Firebase Messaging Error: ' . $e->getMessage(), [
                'exception' => $e,
                'topic' => $topic,
                'title' => $title,
                'body' => $body,
            ]);

            return response()->json([
                'status' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTrace(),  // Include stack trace for more details
            ], 500);
        }
    }

    public function index()
    {
        $notifications = Notification::orderBy('id', 'desc')->get();

        return view('Admin.notifications.index', [
            'pageTitle' => 'Notification',
            'module_name' => 'create',
            'module_url' => '/admin/notifications/create',
            'notifications' => $notifications
        ]);
    }

    public function create()
    {
        try {
            $users = User::where(['status' => 'active'])->whereNotNull('birth_year')->orderBy('birth_year', 'desc')->distinct()->pluck('birth_year')->toArray();

            return view('Admin.notifications.create', [
                'pageTitle' => 'notification',
                'module_name' => 'create',
                'module_url' => '/admin/notifications',
                'users' => $users
            ]);
        } catch (\Exception $e) {
            return $e->getResponse();
        }
    }


    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        if ($request->type == 'years') {
            $request->validate([
                'yearsSel' => 'required|array',
            ]);
        }
        // return $request->all();
        $type = null;

        if ($request->type == 'all') {
            $type = $request->type;
        } else if ($request->type == 'years') {
            $type = implode(',', $request->yearsSel);
        }

        $fileName = "";
        $path = "";
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/'), $fileName);
            $path = url('/uploads/' . $fileName);
        }

        // Notification::create($request->all());
        Notification::create([
            'title' => $request->title,
            'message' => $request->message,
            'type' => $type,
            'image' => $fileName,
            'path' => $path
        ]);
        $this->sendFCM($type, $request->title, $request->message, $path);

        return redirect()->back()->with('success', 'Notification saved & sent successfully!');
    }
}