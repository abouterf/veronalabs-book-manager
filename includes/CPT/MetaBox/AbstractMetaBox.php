<?php

namespace BookManager\CPT\MetaBox;

abstract class AbstractMetaBox
{
    /**
     * @var string
     */
    protected string $postType;
    /**
     * @var string
     */
    protected string $metaBoxId;
    /**
     * @var string
     */
    protected string $metaBoxTitle;

    /**
     * @param string $postType
     * @param string $metaBoxId
     * @param string $metaBoxTitle
     */
    public function __construct(string $postType, string $metaBoxId, string $metaBoxTitle)
    {
        $this->postType = $postType;
        $this->metaBoxId = $metaBoxId;
        $this->metaBoxTitle = $metaBoxTitle;

        add_action('add_meta_boxes', [$this, 'registerMetaBox']);
        add_action('save_post_' . $this->postType, [$this, 'saveMetaBox']);
    }

    /**
     * @return void
     */
    public function registerMetaBox(): void
    {
        add_meta_box(
            $this->metaBoxId,
            $this->metaBoxTitle,
            [$this, 'renderMetaBox'],
            $this->postType
        );
    }

    /**
     * @param int $postId
     * @return void
     */
    public function saveMetaBox(int $postId): void
    {
        if (!isset($_POST[$this->metaBoxId . '_nonce']) ||
            !wp_verify_nonce($_POST[$this->metaBoxId . '_nonce'], $this->metaBoxId)) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $postId)) {
            return;
        }

        $this->save($postId);
    }

    /**
     * @param $post
     * @return void
     */
    abstract public function renderMetaBox($post): void;

    /**
     * @param int $postId
     * @return void
     */
    abstract protected function save(int $postId): void;
}