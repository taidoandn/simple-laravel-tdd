<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\Contact;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_a_contact_can_be_added()
    {
        $this->post('/api/contacts', $this->data());

        $contact = Contact::first();

        $this->assertEquals('Test name', $contact->name);
        $this->assertEquals('test@email.com', $contact->email);
        $this->assertEquals('03/28/1997', $contact->birthday->format('m/d/Y'));
        $this->assertEquals('ABC Company', $contact->company);
    }

    /** @test */
    public function test_fields_are_required()
    {
        collect(['name', 'email', 'birthday', 'company'])->each(function ($field) {
            $response = $this->post('/api/contacts', array_merge($this->data(), [$field => '']));

            $response->assertSessionHasErrors($field);
            $this->assertCount(0, Contact::all());
        });
    }

    /** @test */
    public function test_email_must_be_a_valid_email()
    {
        $response = $this->post('/api/contacts', array_merge($this->data(), ['email' => 'test email']));

        $response->assertSessionHasErrors('email');
        $this->assertCount(0, Contact::all());
    }

    /** @test */
    public function test_birthday_is_properly_stored()
    {
        $this->post('/api/contacts', array_merge($this->data()));

        $this->assertCount(1, Contact::all());
        $this->assertInstanceOf(Carbon::class, Contact::first()->birthday);
        $this->assertEquals('03-28-1997', Contact::first()->birthday->format('m-d-Y'));
    }

    /** @test */
    public function test_a_contact_can_be_fetched()
    {
        $contact = factory(Contact::class)->create();
        $response = $this->get('/api/contacts/' . $contact->id);

        $response->assertStatus(200)->assertExactJson([
            'data' => [
                'name' => $contact->name,
                'email' => $contact->email,
                'birthday' => $contact->birthday->format('m/d/Y'),
                'company' => $contact->company,
            ]
        ]);
    }

    private function data()
    {
        return [
            'name' => 'Test name',
            'email' => 'test@email.com',
            'birthday' => '03/28/1997',
            'company' => 'ABC Company'
        ];
    }
}
