<?php

namespace Tests\Integration;

use Tests\TestCase;
use App\Services\SlackNotificationService;
use App\Jobs\SendSlackNotificationJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Config;

/**
 * Integration Tests for Slack Notifications
 * Tests Slack notification service and job processing
 */
class SlackNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Mock Slack webhook URL
        Config::set('logging.channels.slack.url', 'https://hooks.slack.com/services/test/webhook');
        Config::set('logging.channels.slack.username', 'Test Bot');
    }

    /** @test */
    public function slack_service_can_send_error_notification()
    {
        Http::fake([
            'hooks.slack.com/*' => Http::response(['ok' => true], 200),
        ]);

        $service = new SlackNotificationService();
        $result = $service->error('Test error message', ['context' => 'test']);

        $this->assertTrue($result);
    }

    /** @test */
    public function slack_service_can_send_warning_notification()
    {
        Http::fake([
            'hooks.slack.com/*' => Http::response(['ok' => true], 200),
        ]);

        $service = new SlackNotificationService();
        $result = $service->warning('Test warning', ['user_id' => 1]);

        $this->assertTrue($result);
    }

    /** @test */
    public function slack_service_can_send_success_notification()
    {
        Http::fake([
            'hooks.slack.com/*' => Http::response(['ok' => true], 200),
        ]);

        $service = new SlackNotificationService();
        $result = $service->success('Operation successful', ['task_id' => 123]);

        $this->assertTrue($result);
    }

    /** @test */
    public function slack_service_can_send_info_notification()
    {
        Http::fake([
            'hooks.slack.com/*' => Http::response(['ok' => true], 200),
        ]);

        $service = new SlackNotificationService();
        $result = $service->info('Information message', ['data' => 'test']);

        $this->assertTrue($result);
    }

    /** @test */
    public function slack_job_can_be_dispatched_to_queue()
    {
        Queue::fake();

        SendSlackNotificationJob::dispatch(
            'Test message',
            ['context' => 'test'],
            'info'
        );

        Queue::assertPushed(SendSlackNotificationJob::class, function ($job) {
            return $job->message === 'Test message' && $job->level === 'info';
        });
    }

    /** @test */
    public function slack_service_handles_webhook_failure_gracefully()
    {
        Http::fake([
            'hooks.slack.com/*' => Http::response(['error' => 'Invalid webhook'], 400),
        ]);

        $service = new SlackNotificationService();
        $result = $service->error('Test error');

        $this->assertFalse($result);
    }

    /** @test */
    public function slack_service_returns_false_when_webhook_not_configured()
    {
        Config::set('logging.channels.slack.url', '');

        $service = new SlackNotificationService();
        $result = $service->error('Test error');

        $this->assertFalse($result);
    }

    /** @test */
    public function slack_service_can_send_task_due_notification()
    {
        Http::fake([
            'hooks.slack.com/*' => Http::response(['ok' => true], 200),
        ]);

        $service = new SlackNotificationService();
        $result = $service->taskDueSoon('Important Task', '2024-12-31', 'user@example.com');

        $this->assertTrue($result);
    }

    /** @test */
    public function slack_service_can_send_new_user_notification()
    {
        Http::fake([
            'hooks.slack.com/*' => Http::response(['ok' => true], 200),
        ]);

        $service = new SlackNotificationService();
        $result = $service->newUserRegistered('John Doe', 'john@example.com');

        $this->assertTrue($result);
    }
}

