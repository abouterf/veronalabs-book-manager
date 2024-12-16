<?php

namespace BookManager\ServiceProviders;

use BookManager\Admin\Menu\BookInfoMenu;
use BookManager\Admin\Page\BooksTablePage;
use BookManager\Database\BookSchema;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;
use BookManager\CPT\BookPostType;


class BookServiceProvider extends AbstractServiceProvider implements BootableServiceProviderInterface
{

    /**
     * @var string[]
     */
    protected $provides = [
        'book.post_type',
        'book.schema',
        'book.info.table',
        'book.info.menu'
    ];


    /**
     * @return void
     */
    public function register(): void
    {
        //silence is golden
    }


    /**
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function boot(): void
    {
        $container = $this->getContainer();

        $container->add('book.post_type', function () {
            return new BookPostType();
        });

        $bookPostType = $container->get('book.post_type');

        // Register the custom post type, taxonomies, and meta boxes
        add_action('init', [$bookPostType, 'register']);

        //Table initialize
        $container->add('book.schema', function () use ($container) {
            return new BookSchema($container->get('database'));
        });

        //Add menu page
        $container->add('book.info.table', function () use ($container) {
            return new BooksTablePage();
        });

        $container->add('book.info.menu', function () use ($container) {
            return new BookInfoMenu($container);
        });

        add_action('admin_menu', [$container->get('book.info.menu'), 'register']);
    }
}