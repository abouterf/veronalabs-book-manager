<?php

namespace BookManager\Admin\Menu;

use Psr\Container\ContainerInterface;

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class BookInfoMenu
{
    /**
     * @var ContainerInterface
     */
    protected ContainerInterface $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    private function getTable(): mixed
    {
        return $this->container->get('book.info.table');
    }

    /**
     * @return void
     */
    public function register(): void
    {
        add_menu_page(
            __('Books Info', 'book-manager'),
            __('Books Info', 'book-manager'),
            'manage_options',
            'books-table',
            [$this, 'render'],
            'dashicons-book-alt',
            20
        );
    }

    /**
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function render(): void
    {
        $table = $this->getTable();
        $table->prepare_items();

        echo '<div class="wrap">';
        echo '<h1>' . __('Books Info Table', 'book-manager') . '</h1>';
        echo '<form method="get">';
        echo '<input type="hidden" name="page" value="books-table">';
        $table->display();
        echo '</form>';
        echo '</div>';
    }
}