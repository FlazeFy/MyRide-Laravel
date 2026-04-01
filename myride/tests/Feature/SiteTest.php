<?php

namespace Tests\Feature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

// Helper
use App\Helpers\Audit;
use App\Models\UserModel;

class SiteTest extends TestCase
{
    public function test_all_web_routes() {
        $summary = '';
        $user = UserModel::factory()->create();
    
        $routes = [
            // Public Routes
            ['uri' => '/', 'status' => 200, 'auth' => false, 'type' => 'public'],
            ['uri' => '/register', 'status' => 200, 'auth' => false, 'type' => 'public'],
            ['uri' => '/login', 'status' => 200, 'auth' => false, 'type' => 'public'],
            ['uri' => '/help', 'status' => 200, 'auth' => false, 'type' => 'public'],
            ['uri' => '/about', 'status' => 200, 'auth' => false, 'type' => 'public'],
            ['uri' => '/embed/app_summary', 'status' => 200, 'auth' => false, 'type' => 'public'],
            ['uri' => '/embed/trip_discovered', 'status' => 200, 'auth' => false, 'type' => 'public'],
        
            // Private Routes - Guest (should redirect to login)
            ['uri' => '/dashboard', 'status' => 200, 'auth' => false, 'type' => 'private'],
            ['uri' => '/garage', 'status' => 200, 'auth' => false, 'type' => 'private'],
            ['uri' => '/garage/add', 'status' => 200, 'auth' => false, 'type' => 'private'],
            ['uri' => '/garage/edit/1', 'status' => 200, 'auth' => false, 'type' => 'private'],
            ['uri' => '/garage/detail/1', 'status' => 200, 'auth' => false, 'type' => 'private'],
            ['uri' => '/wash', 'status' => 200, 'auth' => false, 'type' => 'private'],
            ['uri' => '/wash/add', 'status' => 200, 'auth' => false, 'type' => 'private'],
            ['uri' => '/trip', 'status' => 200, 'auth' => false, 'type' => 'private'],
            ['uri' => '/trip/calendar', 'status' => 200, 'auth' => false, 'type' => 'private'],
            ['uri' => '/trip/add', 'status' => 200, 'auth' => false, 'type' => 'private'],
            ['uri' => '/reminder', 'status' => 200, 'auth' => false, 'type' => 'private'],
            ['uri' => '/reminder/add', 'status' => 200, 'auth' => false, 'type' => 'private'],
            ['uri' => '/inventory', 'status' => 200, 'auth' => false, 'type' => 'private'],
            ['uri' => '/inventory/add', 'status' => 200, 'auth' => false, 'type' => 'private'],
            ['uri' => '/history', 'status' => 200, 'auth' => false, 'type' => 'private'],
            ['uri' => '/driver', 'status' => 200, 'auth' => false, 'type' => 'private'],
            ['uri' => '/driver/add', 'status' => 200, 'auth' => false, 'type' => 'private'],
            ['uri' => '/service', 'status' => 200, 'auth' => false, 'type' => 'private'],
            ['uri' => '/service/add', 'status' => 200, 'auth' => false, 'type' => 'private'],
            ['uri' => '/journey', 'status' => 200, 'auth' => false, 'type' => 'private'],
            ['uri' => '/partner', 'status' => 200, 'auth' => false, 'type' => 'private'],
            ['uri' => '/place', 'status' => 200, 'auth' => false, 'type' => 'private'],
            ['uri' => '/fuel', 'status' => 200, 'auth' => false, 'type' => 'private'],
            ['uri' => '/fuel/add', 'status' => 200, 'auth' => false, 'type' => 'private'],
            ['uri' => '/stats', 'status' => 200, 'auth' => false, 'type' => 'private'],
            ['uri' => '/profile', 'status' => 200, 'auth' => false, 'type' => 'private'],
        
            // Private Routes - Authenticated
            ['uri' => '/dashboard', 'status' => 200, 'auth' => true, 'type' => 'private'],
            ['uri' => '/garage', 'status' => 200, 'auth' => true, 'type' => 'private'],
            ['uri' => '/garage/add', 'status' => 200, 'auth' => true, 'type' => 'private'],
            ['uri' => '/garage/edit/1', 'status' => 200, 'auth' => true, 'type' => 'private'],
            ['uri' => '/garage/detail/1', 'status' => 200, 'auth' => true, 'type' => 'private'],
            ['uri' => '/wash', 'status' => 200, 'auth' => true, 'type' => 'private'],
            ['uri' => '/wash/add', 'status' => 200, 'auth' => true, 'type' => 'private'],
            ['uri' => '/trip', 'status' => 200, 'auth' => true, 'type' => 'private'],
            ['uri' => '/trip/calendar', 'status' => 200, 'auth' => true, 'type' => 'private'],
            ['uri' => '/trip/add', 'status' => 200, 'auth' => true, 'type' => 'private'],
            ['uri' => '/reminder', 'status' => 200, 'auth' => true, 'type' => 'private'],
            ['uri' => '/reminder/add', 'status' => 200, 'auth' => true, 'type' => 'private'],
            ['uri' => '/inventory', 'status' => 200, 'auth' => true, 'type' => 'private'],
            ['uri' => '/inventory/add', 'status' => 200, 'auth' => true, 'type' => 'private'],
            ['uri' => '/history', 'status' => 200, 'auth' => true, 'type' => 'private'],
            ['uri' => '/driver', 'status' => 200, 'auth' => true, 'type' => 'private'],
            ['uri' => '/driver/add', 'status' => 200, 'auth' => true, 'type' => 'private'],
            ['uri' => '/service', 'status' => 200, 'auth' => true, 'type' => 'private'],
            ['uri' => '/service/add', 'status' => 200, 'auth' => true, 'type' => 'private'],
            ['uri' => '/journey', 'status' => 200, 'auth' => true, 'type' => 'private'],
            ['uri' => '/partner', 'status' => 200, 'auth' => true, 'type' => 'private'],
            ['uri' => '/place', 'status' => 200, 'auth' => true, 'type' => 'private'],
            ['uri' => '/fuel', 'status' => 200, 'auth' => true, 'type' => 'private'],
            ['uri' => '/fuel/add', 'status' => 200, 'auth' => true, 'type' => 'private'],
            ['uri' => '/stats', 'status' => 200, 'auth' => true, 'type' => 'private'],
            ['uri' => '/profile', 'status' => 200, 'auth' => true, 'type' => 'private'],
        ];
    
        foreach ($routes as $route) {
            auth()->logout();
    
            if ($route['auth']) $this->actingAs($user);
    
            $start = microtime(true);
            $response = $this->followingRedirects(false)->get($route['uri']);
            $duration = microtime(true) - $start;

            // Check redirect to /login for unauthenticated private routes
            if ($route['type'] === 'private' && !$route['auth']) {
                $response = $this->followingRedirects(true)->get($route['uri']);
                $response->assertStatus(200);
                $this->assertEquals(url('/login'), url()->current());
            } else {
                // Status check
                $response = $this->followingRedirects(false)->get($route['uri']);
                $response->assertStatus($route['status']);
            }
    
            // Prevent silent 500
            $this->assertNotEquals(500, $response->status(), "Route crashed: {$route['uri']}");
    
            // Performance guard
            $this->assertTrue($duration < 1.5, "Slow route: {$route['uri']} ({$duration}s)");
    
            $authLabel = $route['auth'] ? 'auth' : 'guest';
            $ms = round($duration * 1000, 4);
            $line = "{$ms}ms | {$route['status']} | [{$authLabel}] {$route['uri']}";
            $summary .= $line . "\n";
        }
    
        Audit::auditRecordText("Test - Site Test", "All Web Routes", $summary);
        Audit::auditRecordSheet("Test - Site Test", "All Web Routes", 'ALL', $summary);
    
        $this->assertNotEmpty($summary);
    }
}
