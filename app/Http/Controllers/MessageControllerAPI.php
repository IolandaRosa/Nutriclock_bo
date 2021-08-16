<?php

namespace App\Http\Controllers;

use App\Http\Resources\Message as MessageResource;
use App\Message;
use App\Ufc;
use App\User;
use App\UsersUfc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class MessageControllerAPI extends Controller
{
    /**
     * @OA\Post(
     *      path="/api/messages",
     *      operationId="Creates new message",
     *      tags={"Message"},
     *      summary="Creates new message",
     *      description="Creates new message",
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="senderId",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="senderName",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="senderPhotoUrl",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="receiverId",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="receiverName",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="receiverPhotoUrl",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="message",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="refMessageId",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="read",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="fromModal",
     *                     type="string"
     *                 ),
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="return message"
     *       )
     *     )
     */
    public function store(Request $request)
    {
        $message = new Message();
        $message->fill($request->all());
        $message->save();

        $messageMarkReads = Message::where('senderId', $request->receiverId)->where('receiverId', $request->senderId)->get();
        if ($messageMarkReads) {
            foreach ($messageMarkReads as $messageMarkRead) {
                $messageMarkRead->read = true;
                $messageMarkRead->save();
            }
        }

        return new MessageResource($message);

    }

    /**
     * @OA\Get(
     *      path="/api/messages/unread",
     *      operationId="messages unread",
     *      tags={"Message"},
     *      summary="Return this list of unread messages for auth user",
     *      description="Return this list of unread messages for auth user",
     *      @OA\Response(
     *          response=200,
     *          description="return the list of unread messages for auth user"
     *       )
     *     )
     */
    public function getUnreadMessagesForAuthUser()
    {
        $messages = Message::where('receiverId', Auth::guard('api')->id())->where('read', false)->get();
        return MessageResource::collection($messages);
    }

    /**
     * @OA\Get(
     *      path="/api/messages/unread-count",
     *      operationId="count messages unread",
     *      tags={"Message"},
     *      summary="Return total of unread messages for auth user",
     *      description="Return total of unread messages for auth user",
     *      @OA\Response(
     *          response=200,
     *          description="return total of unread messages for auth user"
     *       )
     *     )
     */
    public function countUnreadMessagesForAuthUser()
    {
        $count = Message::where('receiverId', Auth::guard('api')->id())->where('read', false)->count();
        return Response::json(['data' => $count], 200);
    }

    /**
     * @OA\Put(
     *      path="/api/messages/read/{id}",
     *      operationId="Mark message as read",
     *      tags={"Message"},
     *      summary="Mark message as read",
     *      description="Mark message as read",
     *      @OA\Parameter(
     *         description="ID of message",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *           type="integer",
     *           format="int64"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Mark message as read"
     *       )
     *     )
     */
    public function markAsRead(Request $request, $id)
    {
        $message = Message::find($id);

        if (!$message) {
            return Response::json(['error' => 'Messagem nao encontrada'], 404);
        }

        $message->read = true;
        $message->save();

        return new MessageResource($message);
    }

    /**
     * @OA\Get(
     *      path="/api/messages",
     *      operationId="get messages",
     *      tags={"Message"},
     *      summary="Return the message history",
     *      description="Return the message history",
     *      @OA\Parameter(
     *         description="ID of message",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *           type="integer",
     *           format="int64"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Return  the message history"
     *       )
     *     )
     */
    public function messagesHistory(Request $request)
    {
        $contacts = [];
        $messagesHistory = [];
        $authId = Auth::guard('api')->id();
        $skipPages = $request->query('skip', '0');

        $userUfcs = UsersUfc::where('user_id', $authId)->get();
        if ($userUfcs) {
            foreach ($userUfcs as $ufc) {
                $users = User::where('role', 'PATIENT')->where('ufc_id', $ufc->ufc_id)->get(['id as senderId', 'name as senderName', 'avatarUrl as senderPhotoUrl']);
                if (count($contacts) == 0) $contacts = $users;
                else $contacts = $contacts->concat($users);
            }

            foreach ($contacts as $c) {
                $messages = Message::whereIn('receiverId', [$authId, $c->senderId])
                    ->whereIn('senderId', [$authId, $c->senderId])
                    ->orderBy('id', 'desc')
                    ->take(10)
                    ->skip($skipPages)
                    ->get();

                $hasUnread = false;

                foreach ($messages as $m) {
                    if ($m->read == false) $hasUnread = true;
                }

                $c->hasUnread = $hasUnread;

                $messagesHistory[$c->senderId] = $messages;
            }

            return Response::json(['contacts' => $contacts, 'messagesHistory' => $messagesHistory], 200);
        }

        return Response::json(['contacts' => [], 'messagesHistory' => []], 200);
    }

    /**
     * @OA\Get(
     *      path="/api/messagesFromUser/{id}",
     *      operationId="get messages by receiver id",
     *      tags={"Message"},
     *      summary="Return the list of messages by receiver id",
     *      description="Return the list of messages by receiver id",
     *      @OA\Response(
     *          response=200,
     *          description="Return the list of messages by receiver id"
     *       )
     *     )
     */
    public function getMessagesFromUser(Request $request, $id)
    {
        $authId = Auth::guard('api')->id();
        $skipPages = $request->query('skip', '0');
        $messages = Message::whereIn('receiverId', [$authId, $id])
            ->whereIn('senderId', [$authId, $id])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->skip($skipPages)
            ->get();

        foreach ($messages as $m) {
            if ($m->senderId == $id) {
                $m->read = 1;
                $m->save();
            }
        }

        return \App\Http\Resources\Message::collection($messages);
    }

    public function destroy($id)
    {
        if (Auth::guard('api')->user()->role != 'PROFESSIONAL') {
            return Response::json(['error' => 'Accesso proibido!'], 401);
        }

        $message = Message::find($id);

        if (!$message) {
            return Response::json(['error' => 'A mensagem não existe!'], 404);
        }

        if ($message->senderId != Auth::guard('api')->user()->id || $message->read) {
            return Response::json(['error' => 'A mensagem não pode ser eliminada!'], 400);
        }

        $message->forceDelete();
        return new \App\Http\Resources\Message($message);
    }

    public function update(Request $request, $id)
    {
        if (Auth::guard('api')->user()->role != 'PROFESSIONAL') {
            return Response::json(['error' => 'Accesso proibido!'], 401);
        }

        $message = Message::find($id);

        if (!$message) {
            return Response::json(['error' => 'A mensagem não existe!'], 404);
        }

        $message->message = $request->message;
        $message->save();

        return new \App\Http\Resources\Message($message);
    }
}
