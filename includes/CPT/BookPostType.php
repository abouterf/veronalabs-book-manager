<?php

namespace BookManager\CPT;

use BookManager\CPT\MetaBox\MetaBoxFactory;


/**
 *
 */
class BookPostType implements PostTypeInterface
{

    /**
     * @var string
     */
    protected string $postType;


    /**
     *
     */
    public function __construct()
    {
        $this->postType = 'book';
    }


    /**
     * @return void
     */
    public function register(): void
    {
        $this->registerTourPostType();
        $this->registerPublisherTaxonomy();
        $this->registerAuthorsTaxonomy();
        $this->addMetaBoxes();
    }


    /**
     * @return void
     */
    private function registerTourPostType(): void
    {
        register_post_type($this->postType, [
            'labels' => [
                'name' => __('Books', 'book-manager'),
                'singular_name' => __('Book', 'book-manager'),
                'add_new' => __('Add New Book', 'book-manager'),
                'add_new_item' => __('Add New Book', 'book-manager'),
                'edit_item' => __('Edit Book', 'book-manager'),
                'new_item' => __('New Book', 'book-manager'),
                'view_item' => __('View Book', 'book-manager'),
                'search_items' => __('Search Books', 'book-manager'),
                'not_found' => __('No books found', 'book-manager'),
                'not_found_in_trash' => __('No books found in trash', 'book-manager'),
                'all_items' => __('All Books', 'book-manager'),
                'archives' => __('Book Archives', 'book-manager'),
            ],
            'public' => true,
            'has_archive' => true,
            'rewrite' => ['slug' => 'books'],
            'supports' => ['title', 'editor', 'thumbnail', 'custom-fields'],
            'menu_icon' => 'dashicons-book',
        ]);
    }

    /**
     * @return void
     */
    private function registerPublisherTaxonomy(): void
    {
        register_taxonomy('publisher', $this->postType, [
            'labels' => [
                'name' => __('Publishers', 'book-manager'),
                'singular_name' => __('Publisher', 'book-manager'),
                'search_items' => __('Search Publishers', 'book-manager'),
                'all_items' => __('All Publishers', 'book-manager'),
                'edit_item' => __('Edit Publisher', 'book-manager'),
                'update_item' => __('Update Publisher', 'book-manager'),
                'add_new_item' => __('Add New Publisher', 'book-manager'),
                'new_item_name' => __('New Publisher Name', 'book-manager'),
            ],
            'public' => true,
            'hierarchical' => true,
            'rewrite' => ['slug' => 'publishers'],
        ]);
    }

    /**
     * @return void
     */
    private function registerAuthorsTaxonomy(): void
    {
        register_taxonomy('author', $this->postType, [
            'labels' => [
                'name' => __('Authors', 'book-manager'),
                'singular_name' => __('Author', 'book-manager'),
                'search_items' => __('Search Authors', 'book-manager'),
                'all_items' => __('All Authors', 'book-manager'),
                'edit_item' => __('Edit Author', 'book-manager'),
                'update_item' => __('Update Author', 'book-manager'),
                'add_new_item' => __('Add New Author', 'book-manager'),
                'new_item_name' => __('New Author Name', 'book-manager'),
            ],
            'public' => true,
            'hierarchical' => true,
            'rewrite' => ['slug' => 'authors'],
        ]);
    }

    /**
     * @return void
     */
    private function addMetaBoxes(): void
    {
        MetaBoxFactory::create($this->postType);
    }


    /**
     * @return string
     */
    public function getPostType(): string
    {
        return $this->postType;
    }
}
