<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Content extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $fillable = ['sitemap_id', 'block_id', 'block_tag', 'block_content', 'block_settings'];

    public function section()
    {
        return $this->hasOne('App\Sitemap');
    }
}
