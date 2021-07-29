<?php

namespace Rh36\EmailApiPackage\Tests\Feature;

use Illuminate\Http\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Rh36\EmailApiPackage\Models\EmailTemplate;
use Rh36\EmailApiPackage\Tests\BaseTestCase;
use Rh36\EmailApiPackage\Tests\User;

class TemplateRouteTest extends BaseTestCase
{
    use RefreshDatabase;

    /** @test */
    function authenticated_users_can_create_a_template()
    {
        // To make sure we don't start with a template
        $this->assertCount(0, EmailTemplate::all());

        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(route('templates.store'), [
            'template_name' => 'My first template',
            'template_body'  => 'Hi, {{ $planet }}!',
        ]);

        $this->assertCount(1, EmailTemplate::all());

        tap(EmailTemplate::first(), function ($template) use ($response, $user) {
            $this->assertEquals('My first template', $template->template_name);
            $this->assertEquals('Hi, {{ $planet }}!', $template->template_body);
            $this->assertTrue($template->user->is($user));
            $response->assertStatus(200)
                ->assertJson([
                    'status' => 'OK',
                    'message' => 'Email template created.'
                ]);
        });
    }

    /** @test */
    function a_template_requires_a_name_and_a_body()
    {
        $user = User::factory()->create();

        $response1 = $this->actingAs($user)->postJson(route('templates.store'), [
            'template_name' => '',
            'template_body'  => 'Some valid body',
        ]);
        $response1->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonPath('errors.template_name', ['The template name field is required.']);

        $response2 = $this->actingAs($user)->postJson(route('templates.store'), [
            'template_name' => 'Some valid name',
            'template_body'  => '',
        ]);
        $response2->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonPath('errors.template_body', ['The template body field is required.']);
    }

    /** @test */
    function guests_can_not_create_templates()
    {
        // We're starting from an unauthenticated state
        $this->assertFalse(auth()->check());

        $this->postJson(route('templates.store'), [
            'template_name' => 'My first template',
            'template_body'  => 'Hi, {{ $planet }}!',
        ])->assertForbidden();
    }

    /** @test */
    function authenticated_users_can_update_a_template()
    {
        $user = User::factory()->create();
        $temp = $user->emailTemplates()->create([
            'template_name' => 'My first template',
            'template_body'  => 'Hi, {{ $planet }}!',
        ]);

        $response = $this->actingAs($user)->putJson('/emailapi/templates/' . $temp->id, [
            'template_name' => 'Updated template name',
            'template_body'  => 'Hello, {{ $firstname }}!',
        ]);

        tap(EmailTemplate::first(), function ($template) use ($response, $user) {
            $this->assertEquals('Updated template name', $template->template_name);
            $this->assertEquals('Hello, {{ $firstname }}!', $template->template_body);
            $this->assertTrue($template->user->is($user));
            $response->assertStatus(200)
                ->assertJson([
                    'status' => 'OK',
                    'message' => 'Update successfully',
                ]);
        });
    }

    /** @test */
    function authenticated_users_can_delete_a_template()
    {
        $user = User::factory()->create();
        $temp = $user->emailTemplates()->create([
            'template_name' => 'My first template',
            'template_body'  => 'Hi, {{ $planet }}!',
        ]);

        $response = $this->actingAs($user)->delete('/emailapi/templates/' . $temp->id);
        $response->assertStatus(204);
        $this->assertCount(0, EmailTemplate::all());
    }
}
