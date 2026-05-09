<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Invitation;
use App\Models\Url;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UrlShortenerTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_cannot_create_short_urls()
    {
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => bcrypt('password'),
            'role' => 'superadmin',
        ]);

        $response = $this->actingAs($superAdmin)->post('/urls', [
            'original_url' => 'https://google.com',
        ]);

        $response->assertSessionHasErrors(['error']);
        $this->assertEquals(0, Url::count());
    }

    public function test_admin_and_member_can_create_short_urls()
    {
        $company = Company::create(['name' => 'Test Company']);
        
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'company_id' => $company->id,
        ]);

        $member = User::create([
            'name' => 'Member User',
            'email' => 'member@example.com',
            'password' => bcrypt('password'),
            'role' => 'member',
            'company_id' => $company->id,
        ]);

        $this->actingAs($admin)->post('/urls', ['original_url' => 'https://google.com']);
        $this->assertEquals(1, Url::count());

        $this->actingAs($member)->post('/urls', ['original_url' => 'https://yahoo.com']);
        $this->assertEquals(2, Url::count());
    }

    public function test_superadmin_can_see_all_short_urls()
    {
        $c1 = Company::create(['name' => 'C1']);
        $c2 = Company::create(['name' => 'C2']);
        
        $u1 = User::create(['name' => 'U1', 'email' => 'u1@e.c', 'password' => 'p', 'role' => 'admin', 'company_id' => $c1->id]);
        $u2 = User::create(['name' => 'U2', 'email' => 'u2@e.c', 'password' => 'p', 'role' => 'admin', 'company_id' => $c2->id]);

        Url::create(['original_url' => 'h1', 'short_code' => 's1', 'user_id' => $u1->id, 'company_id' => $c1->id]);
        Url::create(['original_url' => 'h2', 'short_code' => 's2', 'user_id' => $u2->id, 'company_id' => $c2->id]);

        $superAdmin = User::create(['name' => 'SA', 'email' => 'sa@e.c', 'password' => 'p', 'role' => 'superadmin']);

        $response = $this->actingAs($superAdmin)->get('/urls');
        $response->assertSee('s1');
        $response->assertSee('s2');
    }

    public function test_admin_can_only_see_own_company_urls()
    {
        $c1 = Company::create(['name' => 'C1']);
        $c2 = Company::create(['name' => 'C2']);
        
        $u1 = User::create(['name' => 'U1', 'email' => 'u1@e.c', 'password' => 'p', 'role' => 'admin', 'company_id' => $c1->id]);
        $u2 = User::create(['name' => 'U2', 'email' => 'u2@e.c', 'password' => 'p', 'role' => 'admin', 'company_id' => $c2->id]);

        Url::create(['original_url' => 'h1', 'short_code' => 's1', 'user_id' => $u1->id, 'company_id' => $c1->id]);
        Url::create(['original_url' => 'h2', 'short_code' => 's2', 'user_id' => $u2->id, 'company_id' => $c2->id]);

        $response = $this->actingAs($u1)->get('/urls');
        $response->assertSee('s1');
        $response->assertDontSee('s2');
    }

    public function test_member_can_only_see_own_urls()
    {
        $c1 = Company::create(['name' => 'C1']);
        
        $admin = User::create(['name' => 'Admin', 'email' => 'a@e.c', 'password' => 'p', 'role' => 'admin', 'company_id' => $c1->id]);
        $member = User::create(['name' => 'Member', 'email' => 'm@e.c', 'password' => 'p', 'role' => 'member', 'company_id' => $c1->id]);

        Url::create(['original_url' => 'h1', 'short_code' => 's1', 'user_id' => $admin->id, 'company_id' => $c1->id]);
        Url::create(['original_url' => 'h2', 'short_code' => 's2', 'user_id' => $member->id, 'company_id' => $c1->id]);

        $response = $this->actingAs($member)->get('/urls');
        $response->assertSee('s2');
        $response->assertDontSee('s1');
    }

    public function test_urls_are_publicly_resolvable()
    {
        $c1 = Company::create(['name' => 'C1']);
        $u1 = User::create(['name' => 'U1', 'email' => 'u1@e.c', 'password' => 'p', 'role' => 'admin', 'company_id' => $c1->id]);
        $url = Url::create(['original_url' => 'https://google.com', 'short_code' => 'goog12', 'user_id' => $u1->id, 'company_id' => $c1->id]);

        $response = $this->get('/goog12');
        $response->assertRedirect('https://google.com');
        $this->assertEquals(1, $url->fresh()->clicks);
    }

    public function test_superadmin_can_invite_admin_in_new_company()
    {
        $superAdmin = User::create(['name' => 'SA', 'email' => 'sa@e.c', 'password' => 'p', 'role' => 'superadmin']);

        $response = $this->actingAs($superAdmin)->post('/invite', [
            'email' => 'newadmin@example.com',
            'company_name' => 'New Company',
            'role' => 'admin',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('companies', ['name' => 'New Company']);
        $this->assertDatabaseHas('invitations', ['email' => 'newadmin@example.com', 'role' => 'admin']);
    }

    public function test_superadmin_cannot_invite_member()
    {
        $superAdmin = User::create(['name' => 'SA', 'email' => 'sa@e.c', 'password' => 'p', 'role' => 'superadmin']);

        $response = $this->actingAs($superAdmin)->post('/invite', [
            'email' => 'newmember@example.com',
            'company_name' => 'New Company',
            'role' => 'member',
        ]);

        $response->assertSessionHasErrors(['role']);
    }

    public function test_admin_can_invite_admin_or_member_in_own_company()
    {
        $c1 = Company::create(['name' => 'C1']);
        $admin = User::create(['name' => 'Admin', 'email' => 'a@e.c', 'password' => 'p', 'role' => 'admin', 'company_id' => $c1->id]);

        $this->actingAs($admin)->post('/invite', [
            'email' => 'otheradmin@e.c',
            'role' => 'admin',
        ]);
        $this->assertDatabaseHas('invitations', ['email' => 'otheradmin@e.c', 'company_id' => $c1->id]);

        $this->actingAs($admin)->post('/invite', [
            'email' => 'member@e.c',
            'role' => 'member',
        ]);
        $this->assertDatabaseHas('invitations', ['email' => 'member@e.c', 'company_id' => $c1->id]);
    }

    public function test_member_cannot_invite()
    {
        $c1 = Company::create(['name' => 'C1']);
        $member = User::create(['name' => 'Member', 'email' => 'm@e.c', 'password' => 'p', 'role' => 'member', 'company_id' => $c1->id]);

        $response = $this->actingAs($member)->post('/invite', [
            'email' => 'someone@e.c',
            'role' => 'member',
        ]);

        $response->assertStatus(403);
    }

    public function test_duplicate_company_name_is_handled()
    {
        $superAdmin = User::create(['name' => 'SA', 'email' => 'sa@e.c', 'password' => 'p', 'role' => 'superadmin']);
        Company::create(['name' => 'Existing Company']);

        $this->actingAs($superAdmin)->post('/invite', [
            'email' => 'admin1@e.c',
            'company_name' => 'Existing Company',
            'role' => 'admin',
        ]);

        $this->actingAs($superAdmin)->post('/invite', [
            'email' => 'admin2@e.c',
            'company_name' => 'Existing Company',
            'role' => 'admin',
        ]);

        $this->assertEquals(1, Company::where('name', 'Existing Company')->count());
    }
}
