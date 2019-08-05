<?php

namespace Tests\JobBoy\Test\Util;

class FsUtil
{

    protected static $projectDir;

    public static function projectDir(): string
    {
        if (!self::$projectDir) {
            $dir = __DIR__;
            $i = 0;
            while (!file_exists($dir . '/composer.json') && $i++ < 10) {
                $dir = \dirname($dir);
            }
            $dir = realpath($dir);
        }

        return $dir;
    }

    public static function tmpDir()
    {
        $dir = self::projectDir() . '/.tmp';
        if (!file_exists($dir)) {
            mkdir($dir, 0775, true);
        }
        return $dir;
    }

}