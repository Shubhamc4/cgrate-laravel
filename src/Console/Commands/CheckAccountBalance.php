<?php

declare(strict_types=1);

namespace CGrate\Laravel\Console\Commands;

use CGrate\Laravel\Facades\CGrate;
use CGrate\Php\Exceptions\ConnectionException;
use CGrate\Php\Exceptions\InvalidResponseException;
use CGrate\Php\Exceptions\ValidationException;
use Illuminate\Console\Command;

use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\note;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\table;

class CheckAccountBalance extends Command
{
    protected $signature = 'cgrate:balance';

    protected $description = 'Check current CGrate account balance';

    public function handle(): int
    {
        info('Checking CGrate account balance...');

        $result = null;

        try {
            $result = spin(
                fn () => CGrate::getAccountBalance(),
                'Connecting to CGrate API...'
            );

            if ($result->isSuccessful()) {
                info('✓ Successfully retrieved account balance');

                $environment = config('cgrate.test_mode') ? 'Testing (Sandbox)' : 'Production (Live)';

                $data = [
                    'Environment' => $environment,
                    'Response Code' => $result->responseCode->value,
                    'Response Message' => $result->responseMessage,
                    'Balance' => $result->displayBalance(),
                    'Timestamp' => now()->format('Y-m-d H:i:s'),
                ];

                table(
                    ['Property', 'Value'],
                    collect($data)->map(fn ($value, $key) => [$key, $value])->all()
                );

                return self::SUCCESS;
            } else {
                error("Error retrieving balance: {$result->responseMessage}");

                note(
                    "Response Code: {$result->responseCode->value}\n".
                    'Environment: '.(config('cgrate.test_mode') ? 'Testing' : 'Production')
                );

                return self::FAILURE;
            }
        } catch (ValidationException $e) {
            error('Configuration Error: '.$e->getMessage());

            if (! empty($e->errors())) {
                $this->newLine();
                error('Configuration Issues:');

                foreach ($e->errors() as $key => $error) {
                    error(" - {$key}: {$error}");
                }
            }

            return self::FAILURE;
        } catch (ConnectionException $e) {
            error('Connection Error: '.$e->getMessage());

            return self::FAILURE;
        } catch (InvalidResponseException $e) {
            error('Invalid Response: '.$e->getMessage());

            return self::FAILURE;
        } catch (\Exception $e) {
            error('An unexpected error occurred: '.$e->getMessage());

            return self::FAILURE;
        }
    }
}
