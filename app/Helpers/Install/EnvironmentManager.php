<?php

// Author: John Doe
// License: MIT
// Source: https://github.com/rashidlaasri/LaravelInstaller
// Note: This file has been modified to be included directly in the software rather than via a composer package

namespace App\Helpers\Install;

use Exception;
use Illuminate\Database\SQLiteConnection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Output\BufferedOutput;
use App\Models\User;

class EnvironmentManager
{
    /**
     * @var string
     */
    private $envPath;

    /**
     * @var string
     */
    private $envExamplePath;

    /**
     * Set the .env and .env.example paths.
     */
    public function __construct()
    {
        $this->envPath = base_path('.env');
        $this->envExamplePath = base_path('.env.example');
    }

    /**
     * Get the content of the .env file.
     *
     * @return string
     */
    public function getEnvContent()
    {
        if (! file_exists($this->envPath)) {
            if (file_exists($this->envExamplePath)) {
                copy($this->envExamplePath, $this->envPath);
            } else {
                touch($this->envPath);
            }
        }

        return file_get_contents($this->envPath);
    }

    /**
     * Get the the .env file path.
     *
     * @return string
     */
    public function getEnvPath()
    {
        return $this->envPath;
    }

    /**
     * Get the the .env.example file path.
     *
     * @return string
     */
    public function getEnvExamplePath()
    {
        return $this->envExamplePath;
    }

    /**
     * Save the edited content to the .env file.
     *
     * @param Request $input
     * @return string
     */
    public function saveFileClassic(Request $input)
    {
        $message = trans('installer_messages.environment.success');

        try {
            file_put_contents($this->envPath, $input->get('envConfig'));
        } catch (Exception $e) {
            $message = trans('installer_messages.environment.errors');
        }

        return $message;
    }

    /**
     * Save the form content to the .env file.
     *
     * @param Request $request
     * @return string
     */
    public function updateENV(Request $request)
    {
        $results = trans('installer_messages.environment.success');

        $envFileData =
        'APP_NAME="Inventory Booking System"'."\n".
        'APP_ENV=production'."\n".
        'APP_KEY='.'base64:'.base64_encode(Str::random(32))."\n".
        'APP_DEBUG=false'."\n".
        'APP_URL='.$request->app_url."\n\n".
        'LOG_CHANNEL=stack'."\n".
        'LOG_DEPRECATIONS_CHANNEL=null'."\n".
        'LOG_LEVEL=error'."\n\n".
        'DB_CONNECTION='.$request->database_connection."\n".
        'DB_HOST='.$request->database_hostname."\n".
        'DB_PORT='.$request->database_port."\n".
        'DB_DATABASE='.$request->database_name."\n".
        'DB_USERNAME='.$request->database_username."\n".
        'DB_PASSWORD='.$request->database_password."\n\n".
        'BROADCAST_DRIVER=log'."\n".
        'CACHE_DRIVER=file'."\n".
        'FILESYSTEM_DISK=local'."\n".
        'QUEUE_CONNECTION=database'."\n".
        'SESSION_DRIVER=file'."\n".
        'SESSION_LIFETIME=120'."\n\n".
        'MAIL_MAILER='.$request->mail_driver."\n".
        'MAIL_HOST='.$request->mail_host."\n".
        'MAIL_PORT='.$request->mail_port."\n".
        'MAIL_USERNAME='.$request->mail_username."\n".
        'MAIL_PASSWORD='.$request->mail_password."\n".
        'MAIL_ENCRYPTION='.$request->mail_encryption."\n".
        'MAIL_FROM_ADDRESS='.$request->mail_from_address."\n".
        'MAIL_FROM_NAME=${APP_NAME}'."\n".
        'MAIL_CC_ADDRESS='."\n".
        'MAIL_REPLY_TO_ADDRESS='.$request->mail_from_address."\n".
        'MAIL_REPLY_TO_NAME="${APP_NAME}"'."\n\n".
        'BACKUP_FAILED_NOTIFICATION=mail'."\n".
        'UNHEALTHY_BACKUP_WAS_NOT_FOUND_NOTIFICATION=mail'."\n".
        'CLEANUP_HAS_FAILED_NOTIFICATION=mail'."\n".
        'BACKUP_WAS_SUCCESSFUL_NOTIFICATION=mail'."\n".
        'HEALTHY_BACKUP_WAS_FOUND_NOTIFICATION=mail'."\n".
        'CLEANUP_WAS_SUCCESSFUL_NOTIFICATION=mail'."\n".
        'NOTIFICATION_EMAIL='."\n".
        'NOTIFICATION_OVERDUE_EMAILS=1'."\n".
        'NOTIFICATION_SETUP_EMAILS=1';

        try {
            file_put_contents($this->envPath, $envFileData);
        } catch (Exception $e) {
            $results = trans('installer_messages.environment.errors');
        }

        return $results;
    }

    /**
     * Create the database
     *
     * @param Request $request
     * @return string
     */
    public function createDatabase(Request $request)
    {
        $servername = $request->database_hostname;
        $username = $request->database_username;
        $password = $request->database_password;
        $dbname = $request->database_name;
        $driver = $request->database_connection;

        // Create a new database in MySQL, PostgreSQL, SQL Server, or SQLite
        try {
            // Create connection
            switch ($servername) {
                case 'pgsql':
                    $conn = new \PDO("pgsql:host=$servername", $username, $password);
                    break;
                case 'sqlsrv':
                    $conn = new \PDO("sqlsrv:Server=$servername", $username, $password);
                    break;
                case 'sqlite':
                    $conn = new \PDO("sqlite:$dbname");
                    break;
                default:
                    $conn = new \PDO("mysql:host=$servername", $username, $password);
                    break;
            }

            // Set the \PDO error mode to exception
            $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            // Check if the database already exists
            $exists = false;
            switch ($driver) {
                case 'pgsql':
                    $sql = "SELECT datname FROM pg_database WHERE datistemplate = false AND datname = '$dbname'";
                    $result = $conn->query($sql);
                    $exists = $result->rowCount() > 0;
                    break;
                case 'sqlsrv':
                    $sql = "SELECT name FROM sys.databases WHERE name = '$dbname'";
                    $result = $conn->query($sql);
                    $exists = $result->rowCount() > 0;
                    break;
                case 'sqlite':
                    $exists = file_exists($dbname);
                    break;
                default:
                    $sql = "SHOW DATABASES LIKE '$dbname'";
                    $result = $conn->query($sql);
                    $exists = $result->rowCount() > 0;
                    break;
            }

            // Create database
            if (!$exists) {
                switch ($driver) {
                    case 'pgsql':
                        $sql = "CREATE DATABASE $dbname";
                        break;
                    case 'sqlsrv':
                        $sql = "CREATE DATABASE $dbname";
                        break;
                    case 'sqlite':
                        $sql = "CREATE DATABASE IF NOT EXISTS $dbname";
                        break;
                    default:
                        $sql = "CREATE DATABASE $dbname";
                        break;
                }

                $conn->exec($sql);
                return true;
            } else {
                return "Error creating database: Database already exists";
            }
        } catch (\PDOException $e) {
            return "Error creating database: " . $e->getMessage();
        }

        $conn = null;
    }

    /**
     * Create the global admin account
     *
     * @param Request $request
     * @return string
     */
    public function createGlobalAdmin(Request $request)
    {
        $user = User::create([
            'forename' => $request->account_forename,
            'surname' => $request->account_surname,
            'email' => $request->account_email,
            'has_account' => true,
            'password_set' => true,
        ])->forceFill([
            'password' => Hash::make($request->account_password),
            'role_id' => 2,
        ]);

        $user->save();
    }

    /**
     * Migrate and seed the database.
     *
     * @return array
     */
    public function migrateAndSeed()
    {
        $outputLog = new BufferedOutput;

        return $this->migrate($outputLog);
    }

    /**
     * Run the migration and call the seeder.
     *
     * @param \Symfony\Component\Console\Output\BufferedOutput $outputLog
     * @return array
     */
    private function migrate(BufferedOutput $outputLog)
    {
        try {
            Artisan::call('migrate', ['--force'=> true]);
        } catch (Exception $e) {
            return $this->response($e->getMessage(), 'error', $outputLog);
        }

        return $this->response(trans('installer_messages.final.finished'), 'success', $outputLog);
    }

    /**
     * Seed the database.
     *
     * @param \Symfony\Component\Console\Output\BufferedOutput $outputLog
     * @return array
     */
    private function seed(BufferedOutput $outputLog)
    {
        try {
            Artisan::call('db:seed', ['--force' => true, '--class' => 'InstallSeeder'], $outputLog);
        } catch (Exception $e) {
            return $this->response($e->getMessage(), 'error', $outputLog);
        }

        return $this->response(trans('installer_messages.final.finished'), 'success', $outputLog);
    }

    /**
     * Return a formatted error messages.
     *
     * @param string $message
     * @param string $status
     * @param \Symfony\Component\Console\Output\BufferedOutput $outputLog
     * @return array
     */
    private function response($message, $status, BufferedOutput $outputLog)
    {
        return [
            'status' => $status,
            'message' => $message,
            'dbOutputLog' => $outputLog->fetch(),
        ];
    }

    /**
     * Check database type. If SQLite, then create the database file.
     *
     * @param \Symfony\Component\Console\Output\BufferedOutput $outputLog
     */
    private function sqlite(BufferedOutput $outputLog)
    {
        if (DB::connection() instanceof SQLiteConnection) {
            $database = DB::connection()->getDatabaseName();
            if (! file_exists($database)) {
                touch($database);
                DB::reconnect(Config::get('database.default'));
            }
            $outputLog->write('Using SqlLite database: '.$database, 1);
        }
    }
}
