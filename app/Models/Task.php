<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    
    protected $fillable = ['content' ,'status']; //fillableで「一気の保存可能なものを設定」
    
    //この投稿を所有するユーザ。（Userモデルとの関係を定義）
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
