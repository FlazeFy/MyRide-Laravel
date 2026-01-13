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

    protected $table = 'chat_history';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'question', 'answer', 'intent', 'created_at', 'created_by'];
}
