<?php

namespace BookManager\CPT;


interface PostTypeInterface
{

    /**
     * @return void
     */
    public function register(): void;


    /**
     * @return string
     */
    public function getPostType(): string;
}