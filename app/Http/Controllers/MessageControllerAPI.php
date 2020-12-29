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
        $contacts = Message::where('receiverId', Auth::guard('api')->user()->id)->distinct('senderInd')->get(['senderId', 'senderName', 'senderPhotoUrl']);


        return Response::json(['contacts' => $contacts], 200);
    }
}
