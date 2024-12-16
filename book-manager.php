<?php
/**
 * Plugin Name:     Book Info Management
 * Plugin URI:      https://www.veronalabs.com
 * Plugin Prefix:   BIM
 * Description:     WordPress plugin that manages book information, integrates with the WordPress admin interface.
 * Author:          Erfan Kargosha
 * Author URI:      https://veronalabs.com
 * Text Domain:     book-manager
 * Domain Path:     /languages
 * Version:         1.0
 */

use BookManager\Database\BookSchema;
use BookManager\ServiceProviders\BookServiceProvider;
use Rabbit\Application;
use Rabbit\Database\DatabaseServiceProvider;
use Rabbit\Plugin;
use Rabbit\Redirects\AdminNotice;
use Rabbit\Utils\Singleton;

if (file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
    require dirname(__FILE__) . '/vendor/autoload.php';
}

class BookManagerPluginInit extends Singleton
{
    /**
     * @var
     */
    private $application;

    /**
     *
     */
    public function __construct()
    {
        $this->application = Application::get()->loadPlugin(__DIR__, __FILE__, 'config');
        $this->init();
    }

    /**
     * @return void
     */
    public function init(): void
    {
        try {
            $this->application->addServiceProvider(DatabaseServiceProvider::class);
            $this->application->addServiceProvider(BookServiceProvider::class);

            $this->application->onActivation(function () {
                $this->application->get('book.schema')->createTable();
            });

            $this->application->onDeactivation(function () {
                $this->application->get('book.schema')->dropTable();
            });

            $this->application->boot(function (Plugin $plugin) {
                $plugin->loadPluginTextDomain();
            });
        } catch (Exception $e) {
            $this->handleError($e);
        }
    }

    /**
     * @param $e
     * @return void
     */
    private function handleError($e): void
    {
        add_action('admin_notices', function () use ($e) {
            AdminNotice::permanent(['type' => 'error', 'message' => $e->getMessage()]);
        });

        add_action('init', function () use ($e) {
            if ($this->application->has('logger')) {
                $this->application->get('logger')->warning($e->getMessage());
            }
        });
    }

    /**
     * @return mixed
     */
    public function getApplication(): mixed
    {
        return $this->application;
    }
}


/**
 * @return Singleton
 */
function bookManagerPlugin(): Singleton
{
    return BookManagerPluginInit::get();
}

bookManagerPlugin();
