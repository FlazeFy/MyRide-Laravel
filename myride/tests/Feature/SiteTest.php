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
            ['uri' => '/api/v1/stats/summary', 'method' => 'GET', 'type' => 'public'],
            ['uri' => '/api/v1/stats/total/trip/monthly/2024', 'method' => 'GET', 'type' => 'public'],
            ['uri' => '/api/v1/stats/total/fuel/monthly/fuel_volume/2024', 'method' => 'GET', 'type' => 'public'],
            ['uri' => '/api/v1/stats/total/service/monthly/service_price/2024', 'method' => 'GET', 'type' => 'public'],
            ['uri' => '/api/v1/stats/total/wash/monthly/wash_price/2024', 'method' => 'GET', 'type' => 'public'],
            ['uri' => '/api/v1/question/faq', 'method' => 'GET', 'type' => 'public'],
            ['uri' => '/api/v1/dictionary/type/fuel', 'method' => 'GET', 'type' => 'public'],
            ['uri' => '/api/v1/trip/discovered', 'method' => 'GET', 'type' => 'public'],
    
            // Auth Routes
            ['uri' => '/api/v1/login', 'method' => 'POST', 'type' => 'public'],
            ['uri' => '/api/v1/register/token', 'method' => 'POST', 'type' => 'public'],
            ['uri' => '/api/v1/register/account', 'method' => 'POST', 'type' => 'public'],
            ['uri' => '/api/v1/register/regen_token', 'method' => 'POST', 'type' => 'public'],
    
            // Private Routes
            ['uri' => '/api/v1/logout', 'method' => 'POST', 'type' => 'private'],
            ['uri' => '/api/v1/vehicle/header', 'method' => 'GET', 'type' => 'private'],
            ['uri' => '/api/v1/vehicle/name', 'method' => 'GET', 'type' => 'private'],
            ['uri' => '/api/v1/vehicle/fuel', 'method' => 'GET', 'type' => 'private'],
            ['uri' => '/api/v1/vehicle/detail/1', 'method' => 'GET', 'type' => 'private'],
            ['uri' => '/api/v1/vehicle/detail/full/1', 'method' => 'GET', 'type' => 'private'],
            ['uri' => '/api/v1/vehicle/trip/summary/1', 'method' => 'GET', 'type' => 'private'],
            ['uri' => '/api/v1/vehicle/readiness', 'method' => 'GET', 'type' => 'private'],
            ['uri' => '/api/v1/vehicle', 'method' => 'POST', 'type' => 'private'],
            ['uri' => '/api/v1/vehicle/doc/1', 'method' => 'POST', 'type' => 'private'],
            ['uri' => '/api/v1/vehicle/detail/1', 'method' => 'PUT', 'type' => 'private'],
            ['uri' => '/api/v1/vehicle/image/1', 'method' => 'POST', 'type' => 'private'],
            ['uri' => '/api/v1/vehicle/image_collection/1', 'method' => 'POST', 'type' => 'private'],
            ['uri' => '/api/v1/vehicle/recover/1', 'method' => 'PUT', 'type' => 'private'],
            ['uri' => '/api/v1/vehicle/delete/1', 'method' => 'DELETE', 'type' => 'private'],
            ['uri' => '/api/v1/vehicle/destroy/1', 'method' => 'DELETE', 'type' => 'private'],
            ['uri' => '/api/v1/vehicle/document/destroy/1/1', 'method' => 'DELETE', 'type' => 'private'],
            ['uri' => '/api/v1/vehicle/image_collection/destroy/1/1', 'method' => 'DELETE', 'type' => 'private'],
            ['uri' => '/api/v1/dictionary', 'method' => 'POST', 'type' => 'private'],
            ['uri' => '/api/v1/dictionary/1', 'method' => 'DELETE', 'type' => 'private'],
            ['uri' => '/api/v1/wash', 'method' => 'GET', 'type' => 'private'],
            ['uri' => '/api/v1/wash', 'method' => 'POST', 'type' => 'private'],
            ['uri' => '/api/v1/wash/last/1', 'method' => 'GET', 'type' => 'private'],
            ['uri' => '/api/v1/wash/summary', 'method' => 'GET', 'type' => 'private'],
            ['uri' => '/api/v1/wash/finish/1', 'method' => 'PUT', 'type' => 'private'],
            ['uri' => '/api/v1/wash/1', 'method' => 'PUT', 'type' => 'private'],
            ['uri' => '/api/v1/wash/destroy/1', 'method' => 'DELETE', 'type' => 'private'],
            ['uri' => '/api/v1/history', 'method' => 'GET', 'type' => 'private'],
            ['uri' => '/api/v1/history/destroy/1', 'method' => 'DELETE', 'type' => 'private'],
            ['uri' => '/api/v1/chat/nlp', 'method' => 'GET', 'type' => 'private'],
            ['uri' => '/api/v1/chat/ai', 'method' => 'POST', 'type' => 'private'],
            ['uri' => '/api/v1/error', 'method' => 'GET', 'type' => 'private'],
            ['uri' => '/api/v1/error/destroy/1', 'method' => 'DELETE', 'type' => 'private'],
            ['uri' => '/api/v1/reminder', 'method' => 'GET', 'type' => 'private'],
            ['uri' => '/api/v1/reminder', 'method' => 'POST', 'type' => 'private'],
            ['uri' => '/api/v1/reminder/next', 'method' => 'GET', 'type' => 'private'],
            ['uri' => '/api/v1/reminder/recently', 'method' => 'GET', 'type' => 'private'],
            ['uri' => '/api/v1/reminder/vehicle/1', 'method' => 'GET', 'type' => 'private'],
            ['uri' => '/api/v1/reminder/destroy/1', 'method' => 'DELETE', 'type' => 'private'],
            ['uri' => '/api/v1/service', 'method' => 'GET', 'type' => 'private'],
            ['uri' => '/api/v1/service', 'method' => 'POST', 'type' => 'private'],
            ['uri' => '/api/v1/service/next', 'method' => 'GET', 'type' => 'private'],
            ['uri' => '/api/v1/service/spending', 'method' => 'GET', 'type' => 'private'],
            ['uri' => '/api/v1/service/vehicle/1', 'method' => 'GET', 'type' => 'private'],
            ['uri' => '/api/v1/service/1', 'method' => 'PUT', 'type' => 'private'],
            ['uri' => '/api/v1/service/destroy/1', 'method' => 'DELETE', 'type' => 'private'],
            ['uri' => '/api/v1/driver', 'method' => 'GET', 'type' => 'private'],
            ['uri' => '/api/v1/driver', 'method' => 'POST', 'type' => 'private'],
            ['uri' => '/api/v1/driver/name', 'method' => 'GET', 'type' => 'private'],
            ['uri' => '/api/v1/driver/vehicle', 'method' => 'GET', 'type' => 'private'],
            ['uri' => '/api/v1/driver/vehicle/list', 'method' => 'GET', 'type' => 'private'],
            ['uri' => '/api/v1/driver/1', 'method' => 'PUT', 'type' => 'private'],
            ['uri' => '/api/v1/driver/destroy/1', 'method' => 'DELETE', 'type' => 'private'],
            ['uri' => '/api/v1/fuel', 'method' => 'GET', 'type' => 'private'],
            ['uri' => '/api/v1/fuel', 'method' => 'POST', 'type' => 'private'],
            ['uri' => '/api/v1/fuel/last', 'method' => 'GET', 'type' => 'private'],
            ['uri' => '/api/v1/fuel/summary/2024-01', 'method' => 'GET', 'type' => 'private'],
            ['uri' => '/api/v1/fuel/1', 'method' => 'PUT', 'type' => 'private'],
            ['uri' => '/api/v1/fuel/destroy/1', 'method' => 'DELETE', 'type' => 'private'],
            ['uri' => '/api/v1/trip', 'method' => 'GET', 'type' => 'private'],
            ['uri' => '/api/v1/trip', 'method' => 'POST', 'type' => 'private'],
            ['uri' => '/api/v1/trip/last', 'method' => 'GET', 'type' => 'private'],
            ['uri' => '/api/v1/trip/calendar', 'method' => 'GET', 'type' => 'private'],
            ['uri' => '/api/v1/trip/coordinate', 'method' => 'GET', 'type' => 'private'],
            ['uri' => '/api/v1/trip/coordinate/nearest/-6.193307477576132,106.8290024771821', 'method' => 'GET', 'type' => 'private'],
            ['uri' => '/api/v1/trip/driver/1', 'method' => 'GET', 'type' => 'private'],
            ['uri' => '/api/v1/trip/1', 'method' => 'PUT', 'type' => 'private'],
            ['uri' => '/api/v1/trip/destroy/1', 'method' => 'DELETE', 'type' => 'private'],
            ['uri' => '/api/v1/inventory', 'method' => 'GET', 'type' => 'private'],
            ['uri' => '/api/v1/inventory', 'method' => 'POST', 'type' => 'private'],
            ['uri' => '/api/v1/inventory/vehicle/1', 'method' => 'GET', 'type' => 'private'],
            ['uri' => '/api/v1/inventory/1', 'method' => 'PUT', 'type' => 'private'],
            ['uri' => '/api/v1/inventory/destroy/1', 'method' => 'DELETE', 'type' => 'private'],
            ['uri' => '/api/v1/user', 'method' => 'GET', 'type' => 'private'],
            ['uri' => '/api/v1/user/my_year', 'method' => 'GET', 'type' => 'private'],
            ['uri' => '/api/v1/user/my_profile', 'method' => 'GET', 'type' => 'private'],
            ['uri' => '/api/v1/user/update_telegram_id', 'method' => 'PUT', 'type' => 'private'],
            ['uri' => '/api/v1/user/validate_telegram_id', 'method' => 'PUT', 'type' => 'private'],
            ['uri' => '/api/v1/user/update_profile', 'method' => 'PUT', 'type' => 'private'],
            ['uri' => '/api/v1/export/wash', 'method' => 'GET', 'type' => 'private'],
            ['uri' => '/api/v1/export/trip', 'method' => 'GET', 'type' => 'private'],
            ['uri' => '/api/v1/export/fuel', 'method' => 'GET', 'type' => 'private'],
            ['uri' => '/api/v1/export/inventory', 'method' => 'GET', 'type' => 'private'],
            ['uri' => '/api/v1/export/service', 'method' => 'GET', 'type' => 'private'],
            ['uri' => '/api/v1/export/driver', 'method' => 'GET', 'type' => 'private'],
        ];
    
        foreach ($routes as $route) {
            auth()->logout();
    
            $start = microtime(true);
    
            $response = $this->followingRedirects(false)->json(
                $route['method'],
                $route['uri'],
                $route['payload'] ?? []
            );
    
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
            $line = "{$ms}ms | {$response->status()} | [{$route['method']}] {$route['uri']}";
            $summary .= $line . "\n";
        }
    
        // Audit Test
        Audit::auditRecordText("Test - Smoke Site Test", "All API Routes", $summary);
        Audit::auditRecordSheet("Test - Smoke Site Test", "All API Routes", 'ALL', $summary);
    
        $this->assertNotEmpty($summary);
    }
}
