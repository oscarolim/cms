<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Sitemap;

class HomeController extends Controller
{
    public function __construct()
    {
        
    }

    public function index($url = '')
    {
        if($url == '')
            $sitemap = Sitemap::where(array('parent_id' => NULL, 'published' => 1))->orderBy('position', 'asc')->with('content')->firstOrFail();
        else
        {
            $url = explode('/', $url);
            $sitemap = Sitemap::where(array('slug' => $url[count($url) - 1], 'published' => 1))->with('published_parent')->with('content')->firstOrFail();
            $valid_request = function($sitemap) use (&$valid_request){
                if($sitemap->parent_id == NULL)
                    return;
                    
                if($sitemap->parent_id != NULL && $sitemap->published_parent == NULL)
                    abort(404);
                    
                return $valid_request($sitemap->published_parent);
            };
            $valid_request($sitemap);
        }

        return view('page', [
            'navigation' => Sitemap::frontend_navigation(),
            'content' => $this->parse_content($sitemap),
            'current_route' => $url == '' ? array($sitemap->slug) : $url
        ]);
    }

    private function parse_content($sitemap)
    {
        if($sitemap->structure == NULL)
            return '';
        $html = '';
        $content = $sitemap->content;
        foreach(json_decode($sitemap->structure, true) as $block)
        {
            

            switch($block['type'])
            {
                case 'text':
                    $block_text_content = $content->where('block_id', $block['id'])->where('block_tag', 'text')->first();
                    $settings = $block_text_content != NULL ? json_decode($block_text_content->block_settings, TRUE) : ['border' => 'none'];
                    if($block_text_content->block_content != NULL)
                        $html .= '<div class="container mt-5">
                                    <div class="row justify-content-center">
                                        <div class="col-8'.($settings['border'] ?? '' == 'top' ? ' border-top pt-5' : '').'">
                                            '.$block_text_content->block_content .'
                                        </div>
                                    </div>
                                </div>';
                break;
                case 'image':
                    $block_image_content = $content->where('block_id', $block['id'])->where('block_tag', 'image')->first();
                    $image = $block_image_content != NULL && $block_image_content->file != NULL ? $block_image_content->file->where('id', $block_image_content->block_content)->first() : NULL;
                    if($image != NULL)
                        $html .= '<div class="container mt-5">
                                    <div class="row justify-content-center">
                                        <div class="col-8">
                                            <img class="w-100" src="'.asset($image->folder.$image->filename).'" alt="'.$image->name.'">
                                        </div>
                                    </div>
                                </div>';
                break;
                case 'text+image':
                    $block_text_content = $content->where('block_id', $block['id'])->where('block_tag', 'text')->first();
                    $block_image_content = $content->where('block_id', $block['id'])->where('block_tag', 'image')->first();
                    $settings = $block_image_content != NULL ? json_decode($block_image_content->block_settings, TRUE) : ['position' => 'left'];
                    $image = $block_image_content != NULL && $block_image_content->file != NULL ? $block_image_content->file->where('id', $block_image_content->block_content)->first() : NULL;
                    $image_element = $image != NULL ? '<img class="w-100" src="'.asset($image->folder.$image->filename).'" alt="'.$image->name.'">' : '';

                    switch($settings['position'])
                    {
                        case 'left':
                        case 'right':
                            $html .= '<div class="container mt-5">
                                        <div class="row justify-content-center">
                                            <div class="col-4">
                                                '.($settings['position'] == 'left' ? $image_element : $block_text_content->block_content ?? '').'
                                            </div>
                                            <div class="col-4">
                                                '.($settings['position'] == 'left' ? $block_text_content->block_content ?? '' : $image_element).'
                                            </div>
                                        </div>
                                    </div>';
                        break;
                        case 'full':
                            $html .= '<div class="full-width-image-container" '.($image != NULL ? 'style="background-image:url('.asset($image->folder.$image->filename).')"' : '').'>
                                        <div class="container h-100">
                                        <div class="row h-100 justify-content-center align-items-center">
                                            <div class="col-6 text-center px-5 py-5" style="background-color: rgba(255, 255, 255, 0.7)">
                                                '.($block_text_content->block_content ?? '').'
                                            </div>
                                        </div>
                                        </div>
                                    </div>';
                        break;
                    }
                break;
                case 'text+video':
                    $block_text_content = $content->where('block_id', $block['id'])->where('block_tag', 'text')->first();
                    $block_video_content = $content->where('block_id', $block['id'])->where('block_tag', 'video')->first();
                    $settings = $block_video_content != NULL ? json_decode($block_video_content->block_settings, TRUE) : ['position' => 'left'];
                    $iframe = '';
                    if($block_video_content->block_content != '' && $block_video_content->block_content != NULL)
                    {
                        $iframe = '<div class="embed-responsive embed-responsive-16by9">
                                        <iframe class="embed-responsive-item" src="'.$block_video_content->block_content.'" allowfullscreen></iframe>
                                    </div>';
                    }

                    switch($settings['position'])
                    {
                        case 'left':
                        case 'right':
                            $html .= '<div class="container mt-5">
                                        <div class="row justify-content-center">
                                            <div class="col-4">
                                                '.($settings['position'] == 'left' ? $iframe : $block_text_content->block_content ?? '') .'
                                            </div>
                                            <div class="col-4">
                                            '.($settings['position'] == 'left' ? $block_text_content->block_content ?? '' : $iframe) .'
                                            </div>
                                        </div>
                                    </div>';
                        break;
                        case 'top':
                        case 'bottom':
                            $html .= '<div class="container mt-5">
                                        <div class="row justify-content-center">
                                            <div class="col-8">
                                                '.($settings['position'] == 'top' ? $iframe : $block_text_content->block_content ?? '') .'
                                            </div>
                                            <div class="col-8">
                                            '.($settings['position'] == 'top' ? $block_text_content->block_content ?? '' : $iframe) .'
                                            </div>
                                        </div>
                                    </div>';
                        break;
                    }
                break;
            }
        }
        return $html;
    }
}
