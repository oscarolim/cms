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
    protected $fillable = ['name', 'slug', 'published', 'parent_id', 'template_id', 'structure', 'structure_block_key'];

    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    protected static function booted()
    {
        static::deleted(function($sitemap){
            if ($sitemap->trashed()) 
            {
                $sitemap->slug = uniqid('deleted-');
                $sitemap->save();
            }
        });
    }

    public static function sitemap_selector($ignore_id, $root = NULL)
    {
        if($ignore_id === $root)
            return array();
        
        if($root == NULL)
            return self::whereNull('parent_id')->where('id', '!=', $ignore_id)->orderBy('position', 'asc')->get();
        else
            return self::where('parent_id', $root)->where('id', '!=', $ignore_id)->orderBy('position', 'asc')->get();
    }

    public static function frontend_navigation($root = NULL)
    {
        if($root == NULL)
            return self::whereNull('parent_id')->where('published', 1)->orderBy('position', 'asc')->with('published_children')->get();
        else
            return self::where('parent_id', $root)->where('published', 1)->orderBy('position', 'asc')->with('published_children')->get();
    }

    public function content()
    {
        return $this->hasMany('App\Content')->with('file');
    }

    public function template()
    {
        return $this->hasOne('App\Templates');
    }

    public function published_children()
    {
        return $this->hasMany(static::class, 'parent_id')->where('published', 1)->with('published_children')->orderBy('position', 'asc');
    }

    public function children()
    {
        return $this->hasMany(static::class, 'parent_id')->with('children')->orderBy('position', 'asc');
    }

    public function parent()
    {
        return $this->belongsTo(static::class, 'parent_id')->with('parent');
    }

    public function published_parent()
    {
        return $this->belongsTo(static::class, 'parent_id')->where('published', 1)->with('published_parent');
    }
}