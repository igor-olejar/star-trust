<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\User;
use App\Models\UserType;
use App\UserStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Tests\TestCase;

class UserReviewTransitionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // UserFactory picks a random user_type_id from the DB.
        UserType::create(['name' => 'Venue']);
    }

    public function test_auth_guard_guest_cannot_access_review_or_post_transitions(): void
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $user = User::factory()->create(['status' => UserStatus::VERIFIED]);

        $this->get(route('admin.users.review'))
            ->assertRedirect('/login');

        $this->post(route('admin.users.review.activate', $user))
            ->assertRedirect('/login');

        $this->post(route('admin.users.review.reject', $user))
            ->assertRedirect('/login');

        $this->post(route('admin.users.review.block', $user))
            ->assertRedirect('/login');
    }

    public function test_queue_filtering_lists_only_verified_users(): void
    {
        /** @var Admin $admin */
        $admin = Admin::factory()->create();

        $verified = User::factory()->create([
            'status' => UserStatus::VERIFIED,
            'email' => 'verified@example.com',
        ]);
        $pending = User::factory()->create([
            'status' => UserStatus::PENDING,
            'email' => 'pending@example.com',
        ]);
        $active = User::factory()->create([
            'status' => UserStatus::ACTIVE,
            'email' => 'active@example.com',
        ]);

        $this->actingAs($admin, 'admin')
            ->get(route('admin.users.review'))
            ->assertOk()
            ->assertSee('verified@example.com')
            ->assertDontSee('pending@example.com')
            ->assertDontSee('active@example.com');
    }

    public function test_transitions_verified_to_active_works_and_pending_to_active_is_rejected(): void
    {
        /** @var Admin $admin */
        $admin = Admin::factory()->create();

        $verified = User::factory()->create(['status' => UserStatus::VERIFIED]);
        $pending = User::factory()->create(['status' => UserStatus::PENDING]);

        $this->actingAs($admin, 'admin')
            ->post(route('admin.users.review.activate', $verified))
            ->assertRedirect(route('admin.users.review'));

        $this->assertSame(UserStatus::ACTIVE, $verified->fresh()->status);

        $this->actingAs($admin, 'admin')
            ->post(route('admin.users.review.activate', $pending))
            ->assertRedirect(route('admin.users.review.show', $pending));

        $this->assertSame(UserStatus::PENDING, $pending->fresh()->status);
    }

    public function test_transitions_verified_to_rejected_works(): void
    {
        /** @var Admin $admin */
        $admin = Admin::factory()->create();

        $verified = User::factory()->create(['status' => UserStatus::VERIFIED]);

        $this->actingAs($admin, 'admin')
            ->post(route('admin.users.review.reject', $verified))
            ->assertRedirect(route('admin.users.review'));

        $this->assertSame(UserStatus::REJECTED, $verified->fresh()->status);
    }

    public function test_transitions_verified_to_blocked_works_and_active_to_blocked_works(): void
    {
        /** @var Admin $admin */
        $admin = Admin::factory()->create();

        $verified = User::factory()->create(['status' => UserStatus::VERIFIED]);
        $active = User::factory()->create(['status' => UserStatus::ACTIVE]);

        $this->actingAs($admin, 'admin')
            ->post(route('admin.users.review.block', $verified))
            ->assertRedirect(route('admin.users.review'));

        $this->assertSame(UserStatus::BLOCKED, $verified->fresh()->status);

        $this->actingAs($admin, 'admin')
            ->post(route('admin.users.review.block', $active))
            ->assertRedirect(route('admin.users.review'));

        $this->assertSame(UserStatus::BLOCKED, $active->fresh()->status);
    }

    public function test_disallowed_transitions_are_rejected(): void
    {
        /** @var Admin $admin */
        $admin = Admin::factory()->create();

        $rejected = User::factory()->create(['status' => UserStatus::REJECTED]);
        $pending = User::factory()->create(['status' => UserStatus::PENDING]);

        $this->actingAs($admin, 'admin')
            ->post(route('admin.users.review.activate', $rejected))
            ->assertRedirect(route('admin.users.review.show', $rejected));

        $this->assertSame(UserStatus::REJECTED, $rejected->fresh()->status);

        $this->actingAs($admin, 'admin')
            ->post(route('admin.users.review.block', $pending))
            ->assertRedirect(route('admin.users.review.show', $pending));

        $this->assertSame(UserStatus::PENDING, $pending->fresh()->status);
    }
}

