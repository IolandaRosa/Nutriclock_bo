<?php

namespace App\Http\Controllers;

use App\Events\ChatMessageEvent;
use App\Message;
use App\Http\Resources\Message as MessageResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class MessageControllerAPI extends Controller
{
    public function store (Request $request) {
        $message = new Message();
        $message->fill($request->all());
        $message->save();

        if ($request->refMessageId) {
            $messageMarkReads = Message::where('senderId', $request->receiverId)->where('receiverId', $request->senderId)->get();
            if ($messageMarkReads) {
                foreach ($messageMarkReads as $messageMarkRead) {
                    $messageMarkRead->read = true;
                    $messageMarkRead->save();
                }
            }
        }

        broadcast(new ChatMessageEvent('store', $message));

        return new MessageResource($message);

    }

    public function getUnreadMessagesForAuthUser() {
        $messages = Message::where('receiverId', Auth::guard('api')->id())->where('read', false)->get();
        return MessageResource::collection($messages);
    }

    public function countUnreadMessagesForAuthUser() {
        $count = Message::where('receiverId', Auth::guard('api')->id())->where('read', false)->count();
        return Response::json(['data' => $count], 200);
    }

    public function markAsRead(Request $request, $id) {
        $message = Message::find($id);

        if (!$message) {
            return Response::json(['error' => 'Messagem nao encontrada'], 404);
        }

        $message->read = true;
        $message->save();

        broadcast(new ChatMessageEvent('markAsRead', $message));

        return new MessageResource($message);
    }

    public function messagesHistory() {
        $authId = Auth::guard('api')->id();

        $contacts = Message::where('receiverId', $authId)->distinct('senderId')->get(['senderId', 'senderName', 'senderPhotoUrl']);
        $messagesHistory = [];

        if ($contacts) {
            foreach ($contacts as $c) {
                $messages = Message::whereIn('receiverId', [$authId, $c->senderId])->whereIn('senderId', [$authId, $c->senderId])->get();
                $messagesHistory[$c->senderId] = $messages;
            }

            return Response::json(['contacts' => $contacts, 'messagesHistory' => $messagesHistory], 200);
        }

        return Response::json(['contacts' => [], 'messagesHistory' => []], 200);
    }

    public function getMessagesFromUser(Request $request, $id) {
        $authId = Auth::guard('api')->id();
        $messages = Message::whereIn('receiverId', [$authId, $id])->whereIn('senderId', [$authId, $id])->get();

        return \App\Http\Resources\Message::collection($messages);
    }
}
