<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use App\Sitemap;
use App\Content;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Helpers\Joom\Forms;

class SitemapController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $sitemap = Sitemap::where(array('parent_id' => NULL))->with('children')->orderBy('position', 'asc')->get();

        return view('cms.sitemap.index', ['sitemap' => $sitemap]);
    }

    public function create()
    {
        //return view('cms.sitemap.form', ['sections' => $this->sitemap_selector(0)]);        
        $sitemap = Sitemap::create([
            'name' => 'Untitled']);
        $sitemap->position = Forms::get_next_order('sitemap', $sitemap->parent_id == NULL ? 'parent_id IS NULL' : 'parent_id = '.$sitemap->parent_id);
        $sitemap->save();
        return $this->edit($sitemap->id);
    }

    public function store(Request $request)
    {
        $request->session()->flash('toast-action', 'item-save');
        $sitemap = Sitemap::create($this->validateSitemap($request));
        $sitemap->position = Forms::get_next_order('sitemap', $sitemap->parent_id == NULL ? 'parent_id IS NULL' : 'parent_id = '.$sitemap->parent_id);
        $sitemap->save();
        
        return redirect(route('sitemap'));
    }

    public function show($id)
    {
        
    }

    public function edit($id)
    {   
        $sitemap = Sitemap::with('content')->findOrFail($id);

        return view('cms.sitemap.form', [
            'sitemap' => $sitemap,
            'sections' => $this->sitemap_selector($id)
        ]);
    }

    public function update(Request $request, Sitemap $sitemap)
    {
        $request->session()->flash('toast-action', 'item-save');
        $sitemap->update($this->validateSitemap($request));

        return redirect(route('sitemap'));
    }

    public function published(Request $request, Sitemap $sitemap)
    {
        $request->session()->flash('toast-action', 'item-published');
        $sitemap->update(['published' => !$sitemap->published]);

        return redirect(route('sitemap'));
    }

    public function move(Request $request, Sitemap $sitemap, $direction)
    {
        $request->session()->flash('toast-action', 'item-move');
        Forms::move('sitemap', $direction, $sitemap->id, $sitemap->parent_id == NULL ? 'parent_id IS NULL' : 'parent_id = '.$sitemap->parent_id);
        return redirect(route('sitemap'));
    }

    public function destroy(Request $request, $id)
    {
        $request->session()->flash('toast-action', 'item-deleted');
        Sitemap::findOrFail($id)->delete();
        return redirect(route('sitemap'));
    }

    protected function validateSitemap($request)
    {
        return $request->validate([
            'slug' => [
                'max:255',
                $request->sitemap ? Rule::unique('sitemap')->ignore($request->sitemap->id) : ''
            ],
            'name' => 'required',
            'parent_id' => 'nullable',
            'published' => 'boolean'
        ]);
    }

    private function sitemap_selector($ignore_id, $root = NULL, $depth = 0)
    {
        $options = array();
        foreach(Sitemap::sitemap_selector($ignore_id, $root) as $section)
        {
            $options[$section->id] = ($depth > 0 ? str_repeat('-', $depth).'&nbsp;' : '').$section->name;
            $options += $this->sitemap_selector($ignore_id, $section->id, $depth + 1);
        }
        return $options;
    }

    public function updateStructure(Request $request, Sitemap $sitemap)
    {
        $sitemap->update([
            'structure' => $request->input('structure'),
            'structure_block_key' => $request->input('block_key')
        ]);
    }

    public function createBlock(Request $request, Sitemap $sitemap)
    {
        switch($request->type)
        {
            case 'text':
                echo view('cms.sitemap.blocks.text', [
                    'block_id' => $request->block_id,
                    'block_text_content' => NULL,
                    'settings' => NULL
                ])->render();
            break;
            case 'image':
                echo view('cms.sitemap.blocks.image', [
                    'block_id' => $request->block_id,
                    'block_image_content' => NULL,
                    'image' => NULL
                ])->render();
            case 'text+image':
                echo view('cms.sitemap.blocks.text-image', [
                    'block_id' => $request->block_id,
                    'block_text_content' => NULL,
                    'block_image_content' => NULL,
                    'image' => NULL,
                    'settings' => NULL
                ])->render();
                case 'text+video':
                    echo view('cms.sitemap.blocks.text-video', [
                        'block_id' => $request->block_id,
                        'block_text_content' => NULL,
                        'block_video_content' => NULL,
                        'settings' => NULL
                    ])->render();
            break;
        }
    }

    public function updateBlock(Request $request, Sitemap $sitemap)
    {
        $data = [
            'sitemap_id' => $sitemap->id,
            'block_id' => $request->input('block-id'),
            'block_settings' => $request->input('block-settings')
        ];
        $new_block = $request->input('new-block');
        switch($request->input('block-type'))
        {
            case 'text':
                $this->storeBlockField('text', $request->text, $new_block, $data, $request->settings);
            break;
            case 'image':
                if($request->image_id > 0)
                    $request->validate(['image_id' => 'exists:files,id']);
                $this->storeBlockField('image', $request->image_id, $new_block, $data, $request->settings);
            break;
            case 'text+image':
                if($request->image_id > 0)
                    $request->validate(['image_id' => 'exists:files,id']);
                $this->storeBlockField('image', $request->image_id, $new_block, $data, $request->settings);
                $this->storeBlockField('text', $request->text, $new_block, $data);
            break;
            case 'text+video':
                $this->storeBlockField('video', $request->video_url, $new_block, $data, $request->settings);
                $this->storeBlockField('text', $request->text, $new_block, $data);
            break;
        }
    }

    private function storeBlockField($block_tag, $content, $new_block, $data, $settings = '{}')
    {
        $data['block_tag'] = $block_tag;
        $data['block_content'] = $content ?? '';
        $data['block_settings'] = $settings;

        if($new_block === 'no')
        {
            $content = Content::where([
                'sitemap_id' => $data['sitemap_id'],
                'block_id' => $data['block_id'],
                'block_tag' => $block_tag
            ])->firstOrFail();
            $content->update($data);
        }
        else
            Content::create($data);
    }
}

?>