<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use App\Sitemap;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SitemapController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $sitemap = Sitemap::where(array('parent_id' => NULL))->with('children')->get();

        return view('cms.sitemap.index', ['sitemap' => $sitemap]);
    }

    public function create()
    {
        return view('cms.sitemap.form', ['sections' => $this->sitemap_selector(0)]);
    }

    public function store(Request $request)
    {
        Sitemap::create($this->validateSitemap($request));
        
        return redirect(route('sitemap'));
    }

    public function show($id)
    {
        
    }

    public function edit(Sitemap $sitemap)
    {   
        return view('cms.sitemap.form', [
            'sitemap' => $sitemap,
            'sections' => $this->sitemap_selector($sitemap->id)
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
}

?>