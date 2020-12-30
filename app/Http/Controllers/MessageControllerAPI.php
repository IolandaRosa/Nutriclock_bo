<?php

namespace App\Http\Controllers;

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
            $messageMarkRead = Message::find($request->refMessageId);
            if ($messageMarkRead) {
                $messageMarkRead->read = true;
                $messageMarkRead->save();
            }
        }

        return new MessageResource($message);

    }

    public function getUnreadMessagesForAuthUser(Request $request) {
        $messages = Message::where('receiverId', Auth::guard('api')->user()->id)->where('read', false)->get();
        return MessageResource::collection($messages);
    }

    public function countUnreadMessagesForAuthUser(Request $request) {
        $count = Message::where('receiverId', Auth::guard('api')->user()->id)->where('read', false)->count();
        return Response::json(['data' => $count], 200);
    }

    public function markAsRead(Request $request, $id) {
        $message = Message::find($id);

        if (!$message) {
            return Response::json(['error' => 'Messagem nao encontrada'], 404);
        }

        $message->read = true;
        $message->save();

        return new MessageResource($message);
    }

    public function messagesHistory(Request $request) {
        $authId = Auth::guard('api')->user()->id;

        $contacts = Message::where('receiverId', $authId)->distinct('senderInd')->get(['senderId', 'senderName', 'senderPhotoUrl']);
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
}
