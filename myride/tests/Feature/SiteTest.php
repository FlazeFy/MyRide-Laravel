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
    
        // Audit Test
        Audit::auditRecordText("Test - Smoke Site Test", "All Web Routes", $summary);
        Audit::auditRecordSheet("Test - Smoke Site Test", "All Web Routes", 'ALL', $summary);
    
        $this->assertNotEmpty($summary);
    }

    public function test_all_api_routes() {
        $summary = '';
    
        $routes = [
            // Public Routes
            ['uri' => '/api/v1/stats/summary', 'type' => 'public'],
            ['uri' => '/api/v1/stats/total/trip/monthly/2024', 'type' => 'public'],
            ['uri' => '/api/v1/stats/total/fuel/monthly/fuel_volume/2024', 'type' => 'public'],
            ['uri' => '/api/v1/stats/total/service/monthly/service_price/2024', 'type' => 'public'],
            ['uri' => '/api/v1/stats/total/wash/monthly/wash_price/2024', 'type' => 'public'],
            ['uri' => '/api/v1/question/faq', 'type' => 'public'],
            ['uri' => '/api/v1/dictionary/type/fuel', 'type' => 'public'],
            ['uri' => '/api/v1/trip/discovered', 'type' => 'public'],
    
            // Private Routes
            ['uri' => '/api/v1/vehicle/header', 'type' => 'private'],
            ['uri' => '/api/v1/vehicle/name', 'type' => 'private'],
            ['uri' => '/api/v1/vehicle/fuel', 'type' => 'private'],
            ['uri' => '/api/v1/vehicle/detail/1', 'type' => 'private'],
            ['uri' => '/api/v1/vehicle/detail/full/1', 'type' => 'private'],
            ['uri' => '/api/v1/vehicle/trip/summary/1', 'type' => 'private'],
            ['uri' => '/api/v1/vehicle/readiness', 'type' => 'private'],
            ['uri' => '/api/v1/wash', 'type' => 'private'],
            ['uri' => '/api/v1/wash/last/1', 'type' => 'private'],
            ['uri' => '/api/v1/wash/summary', 'type' => 'private'],
            ['uri' => '/api/v1/history', 'type' => 'private'],
            ['uri' => '/api/v1/chat', 'type' => 'private'],
            ['uri' => '/api/v1/error', 'type' => 'private'],
            ['uri' => '/api/v1/reminder', 'type' => 'private'],
            ['uri' => '/api/v1/reminder/next', 'type' => 'private'],
            ['uri' => '/api/v1/reminder/recently', 'type' => 'private'],
            ['uri' => '/api/v1/reminder/vehicle/1', 'type' => 'private'],
            ['uri' => '/api/v1/service', 'type' => 'private'],
            ['uri' => '/api/v1/service/next', 'type' => 'private'],
            ['uri' => '/api/v1/service/spending', 'type' => 'private'],
            ['uri' => '/api/v1/service/vehicle/1', 'type' => 'private'],
            ['uri' => '/api/v1/driver', 'type' => 'private'],
            ['uri' => '/api/v1/driver/name', 'type' => 'private'],
            ['uri' => '/api/v1/driver/vehicle', 'type' => 'private'],
            ['uri' => '/api/v1/driver/vehicle/list', 'type' => 'private'],
            ['uri' => '/api/v1/stats/total/trip/all', 'type' => 'private'],
            ['uri' => '/api/v1/stats/journey/1', 'type' => 'private'],
            ['uri' => '/api/v1/stats/partner', 'type' => 'private'],
            ['uri' => '/api/v1/stats/place', 'type' => 'private'],
            ['uri' => '/api/v1/stats/total/most_person_trip_with', 'type' => 'private'],
            ['uri' => '/api/v1/stats/total/inventory/all', 'type' => 'private'],
            ['uri' => '/api/v1/stats/total/vehicle/all', 'type' => 'private'],
            ['uri' => '/api/v1/stats/total/service/all', 'type' => 'private'],
            ['uri' => '/api/v1/stats/total/trip/monthly/2024/1', 'type' => 'private'],
            ['uri' => '/api/v1/fuel', 'type' => 'private'],
            ['uri' => '/api/v1/fuel/last', 'type' => 'private'],
            ['uri' => '/api/v1/fuel/summary/2024-01', 'type' => 'private'],
            ['uri' => '/api/v1/trip', 'type' => 'private'],
            ['uri' => '/api/v1/trip/last', 'type' => 'private'],
            ['uri' => '/api/v1/trip/calendar', 'type' => 'private'],
            ['uri' => '/api/v1/trip/coordinate', 'type' => 'private'],
            ['uri' => '/api/v1/trip/driver/1', 'type' => 'private'],
            ['uri' => '/api/v1/inventory', 'type' => 'private'],
            ['uri' => '/api/v1/inventory/vehicle/1', 'type' => 'private'],
            ['uri' => '/api/v1/user', 'type' => 'private'],
            ['uri' => '/api/v1/user/my_year', 'type' => 'private'],
            ['uri' => '/api/v1/user/my_profile', 'type' => 'private'],
            ['uri' => '/api/v1/export/wash', 'type' => 'private'],
            ['uri' => '/api/v1/export/trip', 'type' => 'private'],
            ['uri' => '/api/v1/export/fuel', 'type' => 'private'],
            ['uri' => '/api/v1/export/inventory', 'type' => 'private'],
            ['uri' => '/api/v1/export/service', 'type' => 'private'],
            ['uri' => '/api/v1/export/driver', 'type' => 'private'],
        ];
    
        foreach ($routes as $route) {
            auth()->logout();
    
            $start = microtime(true);
            $response = $this->followingRedirects(false)->get($route['uri']);
            $duration = microtime(true) - $start;
    
            // Check if return 401 status code if access protected route without auth header
            if ($route['type'] === 'public') {
                $this->assertNotEquals(401, $response->status(), "Public route returned 401: {$route['uri']}");
            } else {
                $response->assertStatus(401);
                $response->assertJson([
                    'status' => 'failed',
                    'message' => 'you need to include the authorization token from login',
                ]);
            }
    
            // Prevent silent 500
            $this->assertNotEquals(500, $response->status(), "Route crashed: {$route['uri']}");

            // Performance guard
            $this->assertTrue($duration < 1.5, "Slow route: {$route['uri']} ({$duration}s)");
    
            $ms = round($duration * 1000, 4);
            $line = "{$ms}ms | {$response->status()} | [{$route['type']}] {$route['uri']}";
            $summary .= $line . "\n";
        }
    
        // Audit Test
        Audit::auditRecordText("Test - Smoke Site Test", "All API Routes", $summary);
        Audit::auditRecordSheet("Test - Smoke Site Test", "All API Routes", 'ALL', $summary);
    
        $this->assertNotEmpty($summary);
    }
}
