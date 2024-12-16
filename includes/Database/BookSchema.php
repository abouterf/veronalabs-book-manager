<?php

namespace BookManager\Database;

/**
 *
 */
class BookSchema
{
    /**
     * @var
     */
    private $database;

    /**
     * @param $database
     */
    public function __construct($database)
    {
        $this->database = $database;
    }


    /**
     * @return void
     */
    public function createTable(): void
    {
        $this->database->schema()->create('books_info', function ($table) {
            $table->bigIncrements('id');
            $table->integer('post_id');
            $table->string('isbn', 13);
        });
    }


    /**
     * @return void
     */
    public function dropTable(): void
    {
        $this->database->schema()->dropIfExists('books_info');
    }
}