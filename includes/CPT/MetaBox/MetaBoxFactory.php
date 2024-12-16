<?php

namespace BookManager\CPT\MetaBox;

class MetaBoxFactory
{
    /**
     * @param string $postType
     * @return void
     */
    public static function create(string $postType): void
    {
        switch ($postType) {
            case 'book':
                new BookMetaBox();
                break;
            default:
                throw new \InvalidArgumentException(
                    sprintf(
                    /* translators: %s: post type slug */
                        __('No meta box handler found for post type: %s', 'book-manager'),
                        $postType
                    )
                );
        }
    }
}