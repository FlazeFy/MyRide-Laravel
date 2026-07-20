<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Helper
use App\Helpers\Generator;

/**
 * @OA\Schema(
 *     schema="ChatHistory",
 *     type="object",
 *     required={"id", "question", "is_success", "created_at", "created_by"},
 *
 *     @OA\Property(property="id", type="string", format="uuid", description="Primary key for the chat history record"),
 *     @OA\Property(property="question", type="string", description="User's chat / prompt"),
 *     @OA\Property(property="answer", type="string", nullable=true, description="System generated answer based on fetch result"),
 *     @OA\Property(property="intent", type="string", nullable=true, description="Category / topic of the prompt"),
 *     @OA\Property(property="is_success", type="boolean", description="Indicates whether the question were understandable"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Timestamp when the chat history record was created"),
 *     @OA\Property(property="created_by", type="string", format="uuid", description="ID of the user who created the chat history record")
 * )
 */

class ChatHistoryModel extends Model
{
    use HasFactory;
    public $incrementing = false;
    public $timestamps = false;
    protected $table = 'chat_history';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'chat_type', 'sql_query', 'question', 'answer', 'intent', 'is_success', 'deleted_at', 'created_at', 'created_by'];

    public static function getAllChatHistoryByType($user_id, $type, $paginate) {
        return ChatHistoryModel::select('question', 'answer', 'is_success', 'created_at')
            ->where('created_by', $user_id)
            ->where('chat_type', $type)
            ->whereNull('deleted_at')
            ->orderby('created_at', 'desc')
            ->paginate($paginate);
    }

    public static function updateChatHistoryByType($user_id, $type, $data) {
        return ChatHistoryModel::where('created_by', $user_id)
            ->where('chat_type', $type)
            ->update($data);
    }

    public static function createChatHistory($data, $user_id) {
        $data['id'] = Generator::getUUID();
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['created_by'] = $user_id;

        return ChatHistoryModel::create($data);
    }
}
