<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\User;
use App\Models\UserStatusChange;
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

        $this->withoutMiddleware(VerifyCsrfToken::class);

        // UserFactory picks a random user_type_id from the DB.
        UserType::create(['name' => 'Venue']);
    }

    private function assertStatusChangeLogged(Admin $admin, User $user, UserStatus $from, UserStatus $to): void
    {
        $this->assertDatabaseHas('user_status_changes', [
            'admin_id' => $admin->id,
            'user_id' => $user->id,
            'from_status' => $from->value,
            'to_status' => $to->value,
        ]);
    }

    public function test_auth_guard_guest_cannot_access_review_or_post_transitions(): void
    {
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
        $this->assertStatusChangeLogged($admin, $verified, UserStatus::VERIFIED, UserStatus::ACTIVE);

        $this->actingAs($admin, 'admin')
            ->post(route('admin.users.review.activate', $pending))
            ->assertRedirect(route('admin.users.review'));

        $this->assertSame(UserStatus::ACTIVE, $pending->fresh()->status);
        $this->assertStatusChangeLogged($admin, $pending, UserStatus::PENDING, UserStatus::ACTIVE);
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
        $this->assertStatusChangeLogged($admin, $verified, UserStatus::VERIFIED, UserStatus::REJECTED);
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
        $this->assertStatusChangeLogged($admin, $verified, UserStatus::VERIFIED, UserStatus::BLOCKED);

        $this->actingAs($admin, 'admin')
            ->post(route('admin.users.review.block', $active))
            ->assertRedirect(route('admin.users.review'));

        $this->assertSame(UserStatus::BLOCKED, $active->fresh()->status);
        $this->assertStatusChangeLogged($admin, $active, UserStatus::ACTIVE, UserStatus::BLOCKED);
    }

    public function test_transitions_pending_to_rejected_works_and_active_to_rejected_works(): void
    {
        /** @var Admin $admin */
        $admin = Admin::factory()->create();

        $pending = User::factory()->create(['status' => UserStatus::PENDING]);
        $active = User::factory()->create(['status' => UserStatus::ACTIVE]);

        $this->actingAs($admin, 'admin')
            ->post(route('admin.users.review.reject', $pending))
            ->assertRedirect(route('admin.users.review'));

        $this->assertSame(UserStatus::REJECTED, $pending->fresh()->status);
        $this->assertStatusChangeLogged($admin, $pending, UserStatus::PENDING, UserStatus::REJECTED);

        $this->actingAs($admin, 'admin')
            ->post(route('admin.users.review.reject', $active))
            ->assertRedirect(route('admin.users.review'));

        $this->assertSame(UserStatus::REJECTED, $active->fresh()->status);
        $this->assertStatusChangeLogged($admin, $active, UserStatus::ACTIVE, UserStatus::REJECTED);
    }

    public function test_transition_blocked_to_active_works(): void
    {
        /** @var Admin $admin */
        $admin = Admin::factory()->create();

        $blocked = User::factory()->create(['status' => UserStatus::BLOCKED]);

        $this->actingAs($admin, 'admin')
            ->post(route('admin.users.review.activate', $blocked))
            ->assertRedirect(route('admin.users.review'));

        $this->assertSame(UserStatus::ACTIVE, $blocked->fresh()->status);
        $this->assertStatusChangeLogged($admin, $blocked, UserStatus::BLOCKED, UserStatus::ACTIVE);
    }

    public function test_rejected_is_final_and_disallowed_transitions_do_not_log(): void
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
            ->post(route('admin.users.review.reject', $rejected))
            ->assertRedirect(route('admin.users.review.show', $rejected));

        $this->assertSame(UserStatus::REJECTED, $rejected->fresh()->status);

        $this->actingAs($admin, 'admin')
            ->post(route('admin.users.review.block', $rejected))
            ->assertRedirect(route('admin.users.review.show', $rejected));

        $this->assertSame(UserStatus::REJECTED, $rejected->fresh()->status);

        $this->actingAs($admin, 'admin')
            ->post(route('admin.users.review.block', $pending))
            ->assertRedirect(route('admin.users.review.show', $pending));

        $this->assertSame(UserStatus::PENDING, $pending->fresh()->status);

        $this->assertDatabaseMissing('user_status_changes', [
            'user_id' => $rejected->id,
        ]);
        $this->assertDatabaseMissing('user_status_changes', [
            'user_id' => $pending->id,
        ]);
        $this->assertSame(0, UserStatusChange::count());
    }
}

