<?php

if ( !function_exists('ptask_autoload') ) {

    /**
     * Try to autoload classes from $rootDir
     *
     * @param string $rootDir
     */
    function ptask_autoload( $rootDir ) {

        spl_autoload_register(function( $className ) use ( $rootDir ) {

            $file = sprintf(
                '%s/%s.php',
                $rootDir,
                str_replace( '\\', '/', $className )
            );

            if ( file_exists($file) ) {
                require $file;
            }

        });

    }

}

ptask_autoload( __DIR__ );
