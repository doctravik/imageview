<?php

namespace App\Image;

interface ImageHandler
{
    public function make($path);
    public function resize($width, $height);
    public function width();
    public function height();
}