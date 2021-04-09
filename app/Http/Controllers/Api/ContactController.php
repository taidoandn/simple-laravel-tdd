<?php

namespace App\Http\Controllers\Api;

use App\Models\Contact;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ContactRequest;
use App\Http\Resources\ContactResource;

class ContactController extends Controller
{
    public function store(ContactRequest $request)
    {
        Contact::create($request->validated());
    }

    public function show(Contact $contact)
    {
        return new ContactResource($contact);
    }

    public function update(ContactRequest $request, Contact $contact)
    {
        $contact->update($request->validated());
    }
}
