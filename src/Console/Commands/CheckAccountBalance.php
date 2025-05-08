<?php

declare(strict_types=1);

namespace Cgrate\Laravel\Console\Commands;

use Cgrate\Laravel\Exceptions\ConnectionException;
use Cgrate\Laravel\Exceptions\InvalidResponseException;
use Cgrate\Laravel\Facades\Cgrate;
use Illuminate\Console\Command;
use Illuminate\Validation\ValidationException;

use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\table;

class CheckAccountBalance extends Command
{
    protected $signature = 'cgrate:balance';

    protected $description = 'Check current Cgrate account balance';

    public function handle(): int
    {
        info('Checking Cgrate account balance...');

        try {
            $response = Cgrate::getAccountBalance();

            if ($response->isSuccessful()) {
                info('Account Information:');
                table(
                    ['Response Code', 'Response Message', 'Account Balance', 'Environment'],
                    [
                        $response->toArray() + [
                            (config('cgrate.test_mode') ? 'Testing' : 'Production'),
                        ],
                    ]
                );
            } else {
                error('Error retrieving balance: '.$response->responseMessage);
                error('Response Code: '.$response->responseCode->value);

                return self::FAILURE;
            }

            return self::SUCCESS;
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
        } catch (ConnectionException|InvalidResponseException $e) {
            error('Error: '.$e->getMessage());

            return self::FAILURE;
        } catch (\Exception $e) {
            error('An unexpected error occurred: '.$e->getMessage());

            return self::FAILURE;
        }
    }
}
