<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProfessionalCategory as ProfessionalCategoryResource;
use App\Message;
use App\Http\Resources\Message as MessageResource;
use App\ProfessionalCategory;
use App\User;
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
            if ($request->fromModal) {
                $m = Message::find($request->refMessageId);

                if ($m) {
                    $m->read = true;
                    $m->save();
                }
            } else {
                $messageMarkReads = Message::where('senderId', $request->receiverId)->where('receiverId', $request->senderId)->get();
                if ($messageMarkReads) {
                    foreach ($messageMarkReads as $messageMarkRead) {
                        $messageMarkRead->read = true;
                        $messageMarkRead->save();
                    }
                }
            }
        }

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

        return new MessageResource($message);
    }

    public function messagesHistory(Request $request) {
        $authId = Auth::guard('api')->id();
        $skipPages = $request->query('skip', '0');
        $contacts = Message::where('receiverId', $authId)->distinct('senderId')->get(['senderId', 'senderName', 'senderPhotoUrl']);
        $messagesHistory = [];

        if ($contacts) {
            foreach ($contacts as $c) {
                $messages = Message::whereIn('receiverId', [$authId, $c->senderId])
                    ->whereIn('senderId', [$authId, $c->senderId])
                    ->orderBy('id', 'desc')
                    ->take(10)
                    ->skip($skipPages)
                    ->get();
                $messagesHistory[$c->senderId] = $messages;
            }

            return Response::json(['contacts' => $contacts, 'messagesHistory' => $messagesHistory], 200);
        }

        return Response::json(['contacts' => [], 'messagesHistory' => []], 200);
    }

    public function getMessagesFromUser(Request $request, $id) {
        $authId = Auth::guard('api')->id();
        $skipPages = $request->query('skip', '0');
        $messages = Message::whereIn('receiverId', [$authId, $id])
            ->whereIn('senderId', [$authId, $id])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->skip($skipPages)
            ->get();

        return \App\Http\Resources\Message::collection($messages);
    }

    public function destroy($id)
    {
        if(Auth::guard('api')->user()->role != 'PROFESSIONAL'){
            return Response::json(['error' => 'Accesso proibido!'], 401);
        }

        $message=Message::find($id);

        if(!$message) {
            return Response::json(['error' => 'A mensagem não existe!'], 404);
        }

        if($message->senderId != Auth::guard('api')->user()->id || $message->read) {
            return Response::json(['error' => 'A mensagem não pode ser eliminada!'], 400);
        }

        $message->forceDelete();
        return new \App\Http\Resources\Message($message);
    }

    public function update(Request $request, $id)
    {
        if(Auth::guard('api')->user()->role != 'PROFESSIONAL'){
            return Response::json(['error' => 'Accesso proibido!'], 401);
        }

        $message=Message::find($id);

        if(!$message) {
            return Response::json(['error' => 'A mensagem não existe!'], 404);
        }

        $message->message = $request->message;
        $message->save();

        return new \App\Http\Resources\Message($message);
    }
}
