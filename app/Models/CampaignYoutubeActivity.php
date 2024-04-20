<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampaignYoutubeActivity extends Model
{
    use HasFactory;

    protected $table = 'campaign_youtube_activity';

    protected $fillable = [
        'link',
        'campaign_id',
        'product_id',
        'phone_number',
        'email',
    ];
}
