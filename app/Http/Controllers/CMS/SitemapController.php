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
        return $this->edit($sitemap);
    }

    public function store(Request $request)
    {
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
        $sitemap->update($this->validateSitemap($request));

        return redirect(route('sitemap'));
    }

    public function published(Sitemap $sitemap)
    {
        $sitemap->update(['published' => !$sitemap->published]);

        return redirect(route('sitemap'));
    }

    public function move(Sitemap $sitemap, $direction)
    {
        Forms::move('sitemap', $direction, $sitemap->id, $sitemap->parent_id == NULL ? 'parent_id IS NULL' : 'parent_id = '.$sitemap->parent_id);
        return redirect(route('sitemap'));
    }

    public function destroy($id)
    {
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

    public function updateBlock(Request $request, Sitemap $sitemap)
    {
        $data = [
            'sitemap_id' => $sitemap->id,
            'block_id' => $request->input('block-id'),
            'block_settings' => $request->input('block-settings')
        ];
        switch($request->input('block-type'))
        {
            case 'text':
                $data['block_tag'] = 'text';
                $data['block_content'] = $request->input('text');
            break;
            case 'image':

            break;
            case 'text+image':

            break;
        }

        if($request->input('new-block') === 'no')
        {
            $content = Content::where([
                'sitemap_id' => $data['sitemap_id'],
                'block_id' => $data['block_id'],
                'block_tag' => $data['block_tag']
            ])->firstOrFail();
            $content->update($data);
        }
        else
            Content::create($data);
    }
}

?>