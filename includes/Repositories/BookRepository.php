<?php


namespace BookManager\Repositories;

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Collection;

class BookRepository
{

    /**
     * @param int $postId
     * @param string $isbn
     * @return int
     */
    public static function create(int $postId, string $isbn): int
    {
        return DB::table('books_info')->insertGetId([
            'post_id' => $postId,
            'isbn' => $isbn,
        ]);
    }


    /**
     * @param int $postId
     * @return object|null
     */
    public static function readByPostId(int $postId): object|null
    {
        return DB::table('books_info')
            ->where('post_id', $postId)
            ->first();
    }

    /**
     * @param int $postId
     * @param string $isbn
     * @return int
     */
    public static function update(int $postId, string $isbn): int
    {
        return DB::table('books_info')
            ->where('post_id', $postId)
            ->update([
                'isbn' => $isbn,
            ]);
    }


    /**
     * @param int $postId
     * @return int
     */
    public static function delete(int $postId): int
    {
        return DB::table('books_info')
            ->where('post_id', $postId)
            ->delete();
    }

    /**
     * @return Collection
     */
    public static function getAll(): Collection
    {
        return DB::table('books_info')->get();
    }
}