<?php

namespace Recca0120\LaravelErd\Tests\Console\Commands;

use GuzzleHttp\Psr7\Response;
use Http\Mock\Client;
use Illuminate\Support\Facades\File;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;
use Psr\Http\Client\ClientInterface;
use Recca0120\LaravelErd\Console\Commands\InstallBinary;
use Recca0120\LaravelErd\Platform;
use Recca0120\LaravelErd\Tests\TestCase;

class InstallBinaryTest extends TestCase
{
    /**
     * @dataProvider osProvider
     */
    #[DataProvider('osProvider')]
    public function test_download_binary(string $platform, string $arch, array $expected): void
    {
        File::spy();
        File::expects('exists')->andReturn(false)->twice();

        $this->givenOs($platform, $arch);
        $client = $this->givenClient();

        $this->artisan('erd:install')
            ->assertSuccessful()
            ->execute();

        self::assertEquals(
            InstallBinary::ERD_GO_DOWNLOAD_URL.$expected['erd-go'],
            (string) $client->getRequests()[0]->getUri()
        );

        self::assertEquals(
            InstallBinary::DOT_DOWNLOAD_URL.$expected['dot'],
            (string) $client->getRequests()[1]->getUri()
        );
    }

    public static function osProvider(): array
    {
        return [
            [
                'platform' => Platform::DARWIN,
                'arch' => Platform::ARM,
                'expected' => [
                    'erd-go' => 'darwin_amd64_erd-go',
                    'dot' => 'graphviz-dot-macos-x64',
                ],
            ],
            [
                'platform' => Platform::DARWIN,
                'arch' => '64',
                'expected' => [
                    'erd-go' => 'darwin_amd64_erd-go',
                    'dot' => 'graphviz-dot-macos-x64',
                ],
            ],
            [
                'platform' => Platform::LINUX,
                'arch' => Platform::ARM,
                'expected' => [
                    'erd-go' => 'linux_arm_erd-go',
                    'dot' => 'graphviz-dot-linux-x64',
                ],
            ],
            [
                'platform' => Platform::LINUX,
                'arch' => '64',
                'expected' => [
                    'erd-go' => 'linux_amd64_erd-go',
                    'dot' => 'graphviz-dot-linux-x64',
                ],
            ],
            [
                'platform' => Platform::WINDOWS,
                'arch' => '64',
                'expected' => [
                    'erd-go' => 'windows_amd64_erd-go.exe',
                    'dot' => 'graphviz-dot-win-x64.exe',
                ],
            ],
        ];
    }

    private function givenOs(string $platform, string $arch): void
    {
        $os = Mockery::mock(Platform::class);
        $os->expects('platform')->andReturn($platform);
        $os->expects('arch')->andReturn($arch);
        $this->swap(Platform::class, $os);
    }

    private function givenClient(): Client
    {
        $client = new Client;
        $client->addResponse(new Response(200, [], 'ok'));
        $this->app->addContextualBinding(InstallBinary::class, ClientInterface::class, fn () => $client);

        return $client;
    }
}
