<?php

use App\Services\Batistack;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

beforeEach(function () {
    // Mock the config
    Config::shouldReceive('get')
        ->with('batistack.saas_endpoint')
        ->andReturn('https://api.test.com');
        
    $this->batistack = new Batistack();
    $this->testEndpoint = 'https://api.test.com';
});

test('get method returns json response', function () {
    // Arrange
    $testUrl = '/test-endpoint';
    $testData = ['param' => 'value'];
    $expectedResponse = ['status' => 'success', 'data' => ['key' => 'value']];
    
    // Mock HTTP facade
    Http::fake([
        $this->testEndpoint . $testUrl . '?param=value' => Http::response($expectedResponse, 200),
    ]);
    
    // Act
    $result = $this->batistack->get($testUrl, $testData);
    
    // Assert
    expect($result)->toBe($expectedResponse);
    Http::assertSent(function ($request) use ($testUrl, $testData) {
        return $request->url() == $this->testEndpoint . $testUrl &&
               $request['param'] == 'value';
    });
});

test('get method returns null on exception', function () {
    // Arrange
    $testUrl = '/test-endpoint';
    
    // Mock HTTP facade to throw exception
    Http::fake(function () {
        throw new \Exception('Test exception');
    });
    
    // Mock Log facade
    Log::shouldReceive('error')
        ->once()
        ->with('Batistack get error: Test exception');
    
    // Act
    $result = $this->batistack->get($testUrl);
    
    // Assert
    expect($result)->toBeNull();
});

test('post method returns json response', function () {
    // Arrange
    $testUrl = '/test-endpoint';
    $testData = ['param' => 'value'];
    $expectedResponse = ['status' => 'success', 'data' => ['key' => 'value']];
    
    // Mock HTTP facade
    Http::fake([
        $this->testEndpoint . $testUrl => Http::response($expectedResponse, 200),
    ]);
    
    // Act
    $result = $this->batistack->post($testUrl, $testData);
    
    // Assert
    expect($result)->toBe($expectedResponse);
    Http::assertSent(function ($request) use ($testUrl, $testData) {
        return $request->url() == $this->testEndpoint . $testUrl &&
               $request['param'] == 'value';
    });
});

test('post method returns null on exception', function () {
    // Arrange
    $testUrl = '/test-endpoint';
    
    // Mock HTTP facade to throw exception
    Http::fake(function () {
        throw new \Exception('Test exception');
    });
    
    // Mock Log facade
    Log::shouldReceive('error')
        ->once()
        ->with('Batistack post error: Test exception');
    
    // Act
    $result = $this->batistack->post($testUrl);
    
    // Assert
    expect($result)->toBeNull();
});