<?php

namespace Tests\Feature;

use App\Models\User;
use App\UserStatus;
use App\UserType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class SearchControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $artistUser;

    protected User $venueUser;

    protected User $promoterUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create users of different types with ACTIVE status
        $this->artistUser = User::factory()->create([
            'name' => 'John Artist',
            'user_type_id' => UserType::ARTIST,
            'status' => UserStatus::ACTIVE,
        ]);

        $this->venueUser = User::factory()->create([
            'name' => 'Main Venue',
            'user_type_id' => UserType::VENUE,
            'status' => UserStatus::ACTIVE,
        ]);

        $this->promoterUser = User::factory()->create([
            'name' => 'Event Promoter',
            'user_type_id' => UserType::PROMOTER,
            'status' => UserStatus::ACTIVE,
        ]);
    }

    // Tests for index() method

    public function test_search_route_requires_authentication(): void
    {
        $response = $this->get('/search?q=test');

        $response->assertRedirect('/login');
    }

    public function test_user_cannot_search_themselves(): void
    {
        $this->actingAs($this->artistUser);

        // Search for a name match
        $response = $this->get('/search?q=john');

        $response->assertStatus(200);
        // User should not appear in their own search results (filtered by id != Auth::id())
        $resultIds = $response->viewData('results')->pluck('id')->toArray();
        $this->assertNotContains($this->artistUser->id, $resultIds);
    }

    public function test_search_with_empty_query_shows_message(): void
    {
        $this->actingAs($this->artistUser);

        $response = $this->get('/search?q=');

        $response->assertStatus(200);
        $response->assertViewHas('message', 'Please enter a search term');
        // Results should be empty
        $results = $response->viewData('results');
        $this->assertEquals(0, $results->total());
    }

    public function test_venue_user_type_filter_excludes_other_venues(): void
    {
        // Create another venue user
        $anotherVenue = User::factory()->create([
            'name' => 'Secondary Venue Space',
            'user_type_id' => UserType::VENUE,
            'status' => UserStatus::ACTIVE,
        ]);

        $this->actingAs($this->venueUser);

        // Search with 'venue' keyword - this matches the typeMap and filters by type
        $response = $this->get('/search?q=venue');

        $response->assertStatus(200);
        $resultIds = $response->viewData('results')->pluck('id')->toArray();

        // Should not contain the authenticated venue user (always filtered by id != Auth::id())
        $this->assertNotContains($this->venueUser->id, $resultIds);

        // Should not contain another venue user when type filter is applied
        // (This tests that user_type_id filter is applied when search term matches)
        $this->assertNotContains($anotherVenue->id, $resultIds);
    }

    public function test_promoter_user_type_filter_excludes_other_promoters(): void
    {
        // Create another promoter user
        $anotherPromoter = User::factory()->create([
            'name' => 'Secondary Event Promoter',
            'user_type_id' => UserType::PROMOTER,
            'status' => UserStatus::ACTIVE,
        ]);

        $this->actingAs($this->promoterUser);

        // Search with 'promoter' keyword - this matches the typeMap and filters by type
        $response = $this->get('/search?q=promoter');

        $response->assertStatus(200);
        $resultIds = $response->viewData('results')->pluck('id')->toArray();

        // Should not contain the authenticated promoter user
        $this->assertNotContains($this->promoterUser->id, $resultIds);

        // Should not contain another promoter user (because user_type_id filter applies)
        $this->assertNotContains($anotherPromoter->id, $resultIds);
    }

    public function test_inactive_users_excluded_from_search(): void
    {
        $inactiveVenue = User::factory()->create([
            'name' => 'Inactive Venue',
            'user_type_id' => UserType::VENUE,
            'status' => UserStatus::BLOCKED,
        ]);

        $this->actingAs($this->artistUser);

        $response = $this->get('/search?q=inactive');

        $response->assertStatus(200);
        $resultIds = $response->viewData('results')->pluck('id')->toArray();

        // Inactive user should not appear in results (filtered by status = ACTIVE)
        $this->assertNotContains($inactiveVenue->id, $resultIds);
    }

    public function test_search_results_are_paginated(): void
    {
        $this->actingAs($this->artistUser);

        $response = $this->get('/search?q=main');

        $response->assertStatus(200);
        $results = $response->viewData('results');

        // Should be a paginated result set
        $this->assertInstanceOf(LengthAwarePaginator::class, $results);
    }

    // Tests for searchSuggestions() method

    public function test_user_suggestions_exclude_self(): void
    {
        $this->actingAs($this->artistUser);

        $response = $this->get('/api/search-suggestions?q=john');

        $response->assertStatus(200);
        $suggestions = $response->json();

        $suggestionIds = collect($suggestions)->pluck('id')->toArray();

        // The authenticated user should not appear in suggestions (filtered by id != Auth::id())
        $this->assertNotContains($this->artistUser->id, $suggestionIds);
    }

    public function test_venue_suggestions_exclude_other_venues(): void
    {
        // Create another venue user
        $anotherVenue = User::factory()->create([
            'name' => 'Alternative Venue Space',
            'user_type_id' => UserType::VENUE,
            'status' => UserStatus::ACTIVE,
        ]);

        $this->actingAs($this->venueUser);

        $response = $this->get('/api/search-suggestions?q=venue');

        $response->assertStatus(200);
        $suggestions = $response->json();

        $suggestionIds = collect($suggestions)->pluck('id')->toArray();

        // Neither venue should appear in suggestions (whereNotIn filters by user_type_id)
        $this->assertNotContains($this->venueUser->id, $suggestionIds);
        $this->assertNotContains($anotherVenue->id, $suggestionIds);

        // Suggestions should only be artists or promoters
        $acceptableTypes = [UserType::ARTIST->value, UserType::PROMOTER->value];
        foreach ($suggestionIds as $id) {
            $user = User::find($id);
            $this->assertContains($user->user_type_id->value, $acceptableTypes);
        }
    }

    public function test_promoter_suggestions_exclude_other_promoters(): void
    {
        // Create another promoter
        $anotherPromoter = User::factory()->create([
            'name' => 'Secondary Event Promoter',
            'user_type_id' => UserType::PROMOTER,
            'status' => UserStatus::ACTIVE,
        ]);

        $this->actingAs($this->promoterUser);

        $response = $this->get('/api/search-suggestions?q=event');

        $response->assertStatus(200);
        $suggestions = $response->json();

        $suggestionIds = collect($suggestions)->pluck('id')->toArray();

        // Neither promoter should appear in suggestions
        $this->assertNotContains($this->promoterUser->id, $suggestionIds);
        $this->assertNotContains($anotherPromoter->id, $suggestionIds);

        // Suggestions should only be artists or venues
        $acceptableTypes = [UserType::ARTIST->value, UserType::VENUE->value];
        foreach ($suggestionIds as $id) {
            $user = User::find($id);
            $this->assertContains($user->user_type_id->value, $acceptableTypes);
        }
    }

    public function test_suggestions_returns_at_most_5_results(): void
    {
        // Create many venue users
        User::factory(10)->create([
            'user_type_id' => UserType::VENUE,
            'status' => UserStatus::ACTIVE,
            'name' => 'Venue Place Name',
        ]);

        $this->actingAs($this->artistUser);

        $response = $this->get('/api/search-suggestions?q=venue');

        $response->assertStatus(200);
        $suggestions = $response->json();

        // Should return at most 5 results
        $this->assertLessThanOrEqual(5, count($suggestions));
    }

    public function test_suggestions_returns_empty_for_short_query(): void
    {
        $this->actingAs($this->artistUser);

        $response = $this->get('/api/search-suggestions?q=a');

        $response->assertStatus(200);
        $suggestions = $response->json();

        // Should return empty array for queries less than 2 characters
        $this->assertEmpty($suggestions);
    }

    public function test_suggestions_only_include_active_users(): void
    {
        // Create an inactive user that might match
        $inactiveArtist = User::factory()->create([
            'name' => 'Inactive Artist Name',
            'user_type_id' => UserType::ARTIST,
            'status' => UserStatus::BLOCKED,
        ]);

        $this->actingAs($this->venueUser);

        $response = $this->get('/api/search-suggestions?q=inactive');

        $response->assertStatus(200);
        $suggestions = $response->json();

        $suggestionIds = collect($suggestions)->pluck('id')->toArray();

        // Inactive user should not appear
        $this->assertNotContains($inactiveArtist->id, $suggestionIds);

        // All returned suggestions should be active users
        foreach ($suggestionIds as $id) {
            $user = User::find($id);
            $this->assertEquals(UserStatus::ACTIVE->value, $user->status->value);
        }
    }

    public function test_artist_suggestions_exclude_other_artists(): void
    {
        // Create another artist user
        $anotherArtist = User::factory()->create([
            'name' => 'Bob Artist Band',
            'user_type_id' => UserType::ARTIST,
            'status' => UserStatus::ACTIVE,
        ]);

        $this->actingAs($this->artistUser);

        $response = $this->get('/api/search-suggestions?q=artist');

        $response->assertStatus(200);
        $suggestions = $response->json();

        $suggestionIds = collect($suggestions)->pluck('id')->toArray();

        // Both artists should be excluded
        $this->assertNotContains($this->artistUser->id, $suggestionIds);
        $this->assertNotContains($anotherArtist->id, $suggestionIds);

        // Suggestions should only be venues or promoters
        $acceptableTypes = [UserType::VENUE->value, UserType::PROMOTER->value];
        foreach ($suggestionIds as $id) {
            $user = User::find($id);
            $this->assertContains($user->user_type_id->value, $acceptableTypes);
        }
    }
}
