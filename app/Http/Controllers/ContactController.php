<?php

namespace App\Http\Controllers;

use App\Sitemap;
use App\Mail\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function show()
    {
        return view('contact', [
            'navigation' => Sitemap::frontend_navigation(),
            'current_route' => array('contact')
        ]);
    }

    public function send(Request $request)
    {
        $values = $request->validate([
            'name' => 'required',
            'email' => 'email|required',
            'text' => 'required'
        ]);

        Mail::to('me@oscarolim.com')->send(new Contact($values));

        return view('contact-sent', [
            'navigation' => Sitemap::frontend_navigation(),
            'current_route' => array('contact')
        ]);
    }
}
