<?php

namespace BookManager\CPT\MetaBox;

use BookManager\Controllers\BookController;

class BookMetaBox extends AbstractMetaBox
{

    public function __construct()
    {
        parent::__construct('book', 'book_details', __('Book Details', 'book-manager'));
    }

    /**
     * @param $post
     * @return void
     */
    public function renderMetaBox($post): void
    {
        $isbn = get_post_meta($post->ID, '_book_isbn', true);

        wp_nonce_field($this->metaBoxId, $this->metaBoxId . '_nonce');

        echo '<p>';
        echo '<label for="book_isbn">' . __('ISBN', 'book-manager') . '</label>';
        echo '<input type="text" id="book_isbn" name="book_isbn" value="' . esc_attr($isbn) . '" />';
        echo '</p>';
    }

    /**
     * @param int $postId
     * @return void
     */
    protected function save(int $postId): void
    {
        $isbn = sanitize_text_field($_POST['book_isbn']);
        if (isset($_POST['book_isbn'])) {
            update_post_meta($postId, '_book_isbn', $isbn);
        }

        //Save isbn in table
        $existingRecord = BookController::readByPostId($postId);
        $existingRecord ? BookController::update($postId, $isbn) : BookController::create($postId, $isbn);
    }
}