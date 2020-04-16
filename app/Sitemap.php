<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;

class Sitemap extends Model
{
    use Sluggable, SoftDeletes;
    
    protected $dates = ['deleted_at'];
    protected $table = 'sitemap';
    protected $fillable = ['name', 'slug', 'published', 'parent_id', 'template_id'];

    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public static function sitemap_selector($ignore_id, $root = NULL)
    {
        if($ignore_id === $root)
            return array();
        
        if($root == NULL)
            return self::whereNull('parent_id')->where('id', '!=', $ignore_id)->get();
        else
            return self::where('parent_id', $root)->where('id', '!=', $ignore_id)->get();
    }

    public function template()
    {
        return $this->hasOne('App\Templates');
    }

    public function children()
    {
        return $this->hasMany(static::class, 'parent_id')->with('children');
    }

    public function parent()
    {
        return $this->belongsTo(static::class, 'parent_id')->whereNull('parent_id')->with('parent');
    }
}