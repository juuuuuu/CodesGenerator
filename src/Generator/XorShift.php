<?php

namespace Generator;

class XorShift {
    private int $w;
    private int $x;
    private int $y;
    private int $z;

    public function __construct($seed = false) {
        $this->x = 123456789;
        $this->y = 362436069;
        $this->z = 521288629;

        if ($seed !== false) {
            $this->w = $seed;
        }
        else {
            $this->w = 88675123;
        }
    }

    public function random(): int
    {
        $t = ($this->x ^ ($this->x << 11)) & 0x7fffffff;

        $this->x = $this->y;
        $this->y = $this->z;
        $this->z = $this->w;

        $this->w = ($this->w ^ ($this->w >> 19) ^ ($t ^ ($t >> 8)));

        return $this->w;
    }
}
