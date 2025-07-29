<?php

// app/Http/Controllers/ContactController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function show()
    {
        return view('contact');
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string',
            'email'   => 'required|email',
            'subject' => 'required|string',
            'message' => 'required|string',
        ]);

        Mail::raw("Message from: {$validated['name']} <{$validated['email']}>\n\n{$validated['message']}", function ($mail) use ($validated) {
            $mail->to('admin@emvigotech.com')
                 ->subject($validated['subject']);
        });

        return back()->with('success', 'Your message has been sent successfully!');
    }
}
