<?php

namespace App\Support;

class Ispu
{ 
    /**
     * Perkiraan ISPU dari PM2.5 (ug/m3), interpolasi linear antar breakpoint
     * (15,5 / 55,4 / 150,4 / 250,4). Sama dengan NA.pm25ToIspu di versi demo.
     */
    public static function fromPm25(float $c): int
    {
        $breakpoints = [
            [0, 15.5, 0, 50],
            [15.5, 55.4, 51, 100],
            [55.4, 150.4, 101, 200],
            [150.4, 250.4, 201, 300],
            [250.4, 500, 301, 500],
        ];

        $v = max(0, $c);

        foreach ($breakpoints as [$cl, $ch, $il, $ih]) {
            if ($v <= $ch) {
                return (int) round($il + (($v - $cl) / ($ch - $cl)) * ($ih - $il));
            }
        }

        return 500;
    }
}
