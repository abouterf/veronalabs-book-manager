<?php

namespace BookManager\Admin\Page;

use Illuminate\Database\Capsule\Manager as DB;
use WP_List_Table;

if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}


class BooksTablePage extends WP_List_Table
{
    public function __construct()
    {
        parent::__construct([
            'singular' => __('Book', 'book-manager'),
            'plural' => __('Books', 'book-manager'),
            'ajax' => false,
        ]);
    }

    /**
     * @return array
     */
    public function get_columns(): array
    {
        return [
            'cb' => '<input type="checkbox" />',
            'post_id' => __('Post ID', 'book-manager'),
            'isbn' => __('ISBN', 'book-manager'),
        ];
    }

    /**
     * @return array[]
     */
    protected function get_sortable_columns(): array
    {
        return [
            'post_id' => ['post_id', false],
            'isbn' => ['isbn', false],
        ];
    }

    /**
     * @param $item
     * @param $column_name
     * @return string|void
     */
    protected function column_default($item, $column_name)
    {
        return $item->$column_name ?? '';
    }

    /**
     * @param $item
     * @return string
     */
    protected function column_cb($item): string
    {
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            $this->_args['singular'],
            esc_attr($item->id)
        );
    }

    /**
     * @param $item
     * @return string
     */
    protected function column_post_id($item): string
    {
        $post_edit_link = get_edit_post_link($item->post_id);

        $actions = [
            'edit' => sprintf(
                '<a href="%s">%s</a>',
                esc_url($post_edit_link),
                __('Edit', 'book-manager')
            ),
            'delete' => sprintf(
                '<a href="?page=books-table&action=delete&id=%d" onclick="return confirm(\'%s\')">%s</a>',
                $item->id,
                esc_js(__('Are you sure?', 'book-manager')),
                __('Delete', 'book-manager')
            ),
        ];

        return sprintf(
            '%1$s %2$s',
            esc_html($item->post_id),
            $this->row_actions($actions)
        );
    }

    /**
     * @return array
     */
    protected function get_bulk_actions(): array
    {
        return [
            'delete' => __('Delete', 'book-manager'),
        ];
    }

    /**
     * @return void
     */
    protected function process_bulk_action(): void
    {
        $action = $this->current_action();

        if ($action === 'delete') {
            $ids = isset($_REQUEST['book']) ? array_map('intval', (array)$_REQUEST['book']) : [];

            if (!empty($ids)) {
                DB::table('books_info')->whereIn('id', $ids)->delete();
            }
        }

        if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'delete') {
            $id = intval($_GET['id']);
            if ($id) {
                DB::table('books_info')->where('id', $id)->delete();
            }
        }
    }

    /**
     * @return void
     */
    public function prepare_items(): void
    {
        $columns = $this->get_columns();
        $sortable = $this->get_sortable_columns();
        $hidden = [];

        $this->_column_headers = [$columns, $hidden, $sortable];

        $this->process_bulk_action();

        $current_page = $this->get_pagenum();
        $per_page = 5;

        $total_items = DB::table('books_info')->count();

        $data = DB::table('books_info')
            ->orderBy(
                $_REQUEST['orderby'] ?? 'id',
                $_REQUEST['order'] ?? 'asc'
            )
            ->offset(($current_page - 1) * $per_page)
            ->limit($per_page)
            ->get()
            ->toArray();

        $this->items = $data;

        $this->set_pagination_args([
            'total_items' => $total_items,
            'per_page' => $per_page,
            'total_pages' => ceil($total_items / $per_page),
        ]);
    }

    /**
     * @return void
     */
    public static function render(): void
    {
        $table = new self();
        $table->prepare_items();

        echo '<div class="wrap">';
        echo '<h1>' . esc_html__('Books Info Table', 'book-manager') . '</h1>';
        echo '<form method="post">';
        $table->search_box(__('Search Books', 'book-manager'), 'search_id');
        echo '<input type="hidden" name="page" value="books-table">';
        $table->display();
        echo '</form>';
        echo '</div>';
    }
}