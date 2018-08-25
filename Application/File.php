<?php namespace DevApp\Application;

/*
 * ----------------------------------------------------
 * Dev Project | Rolando Gonzalez
 *      ---------------
 *          File.php
 * ----------------------------------------------------
 */

use SplFileObject;
use NoRewindIterator;
use DevApp\Application\Error;

class File 
{
    private static $file;
    private static $ftpConn;

    /**
     * Set File.
     *
     * @param string $file File path.
     * @return void.
     */
    private static function setFile($file) : void
    {
        if ( ! file_exists( $file ) ) 
        {
            Error::renderErrorHandleView([
                'user'  => 'File error code: 10. Error: File does not exists.',
                'admin' => 'File error code: 10. Error: File does not exists. ' . $file
            ]);
        }
        
        self::$file = new SplFileObject( $file, 'r' );
    }
    
    /**
     * Get CSV content.
     * 
     * @param string $file File path.
     * @param bool   $hdr  Include file header.
     * @return array.
     */
    public static function getCsv($file, $hdr = false) : array
    {
        /**
         * Check file and set object.
         */
        self::setFile( $file );
        
        $header   = false;
        $iterator = new NoRewindIterator( self::fileCsv() );
        
        /**
         * Include file header.
         */
        if ( $hdr )
        {
            /**
             * Get CSV header.
             */
            $header = self::getHeader( $iterator );

            /**
             * Increment iterator.
             */
            $iterator->next();
        }    
        
        /**
         * Get CSV content.
         */
        $data = self::getContent( $iterator );
        
        self::$file = false;
        
        return [ 'header' => $header, 'data' => $data ];
    }
    
    /**
     * Get File content.
     * 
     * @param string $file File path.
     * @return array.
     */
    public static function getFile($file) : array
    {
        /**
         * Check file and set object.
         */
        self::setFile( $file );
        
        $iterator = new NoRewindIterator( self::fileLine() );

        $data = self::getContent( $iterator );
        
        self::$file = false;
        
        return $data;
    }
    
    /* ====================================================================================================================================
    * =====================================================================================================================================
    *
    *             FTP HELPERS
    *
    * =====================================================================================================================================
    * =====================================================================================================================================
    */

    /**
     * Open FTP server connection.
     *
     * @param string $host FTP server address.
     * @param string $user Optional, FTP login user.
     * @param string $pass Optional, FTP login pass.
     * @param string $file Optional, File path.
     * @return void.
     */
    private static function openFtp(string $host, string $user, string $pass, $file = false) : void
    {
        $file ? self::openFile( $file ) : '';

        self::$ftpConn = ftp_connect( $host );
        
        if ( ! self::$ftpConn ) 
        {
            Error::renderErrorHandleView([
                'user'  => 'File error code: 30. Error: FTP connection fails.',
                'admin' => 'File error code: 30. Error: FTP connection fails. ' . $host
            ]);
        }

        @$ftpLogin = ftp_login( self::$ftpConn, $user, $pass );

        ftp_pasv( self::$ftpConn, true );

        if ( ! $ftpLogin ) 
        {
            Error::renderErrorHandleView([
                'user'  => 'File error code: 40. Error: FTP login fails.',
                'admin' => 'File error code: 40. Error: FTP login fails. ' . $host
            ]);
        }
    }

    /**
     * Get FTP File.
     *
     * Connects to FTP server and copy a server file to specific location.
     * Optional pass user and login if the ftp server require login access.
     *
     * @param string $host       FTP server Address.
     * @param string $localFile  Local file path.
     * @param string $serverFile Server file path.
     * @param string $user       Optional. FTP user login.
     * @param string $pass       Optional. FTP user pass.
     * @return @void.
     */
    public static function getFtpFile(string $host, string $localFile, string $serverFile, string $user = 'anonymous', string $pass = 'roglecas@gmail.com') : void
    {
        self::openFtp( $host, $user, $pass, $localFile );

        if ( ! @ftp_fget( self::$ftpConn, self::$file, $serverFile, FTP_BINARY )) 
        {
            Error::renderErrorHandleView([
                'user'  => 'File error code: 50. Error: Could not get FTP file.',
                'admin' => 'File error code: 50. Error: Could not get FTP file. ' . $serverFile
            ]);
        }

        self::closeFtp();
    }

    /**
     * Open a file and locked.
     *
     * @param string $file File path.
     * @param string $perm Optional, permission to open file.
     * @return void.
     */
    private static function openFile(string $file, string $perm = 'w') : void
    {
        @self::$file = fopen( $file, $perm );
        
        if ( ! self::$file ) 
        {
            Error::renderErrorHandleView([
                'user'  => 'File error code: 50. Error: When open file.',
                'admin' => 'File error code: 50. Error: When open file. ' . $file
            ]);
        }

        flock( self::$file, LOCK_EX );
    }

    /**
     * Close FTP server connection.
     *
     * @return void.
     */
    private static function closeFtp() : void
    {
        if ( self::$ftpConn )
        {
            ftp_quit( self::$ftpConn );
            
            self::$ftpConn = false;
        }

        self::closeFile();
    }

    /**
     * Release lock and close file.
     *
     * @return void.
     */
    private static function closeFile() : void
    {
        if ( self::$file )
        {
            flock( self::$file, LOCK_UN );
            fclose( self::$file );
 
            self::$file = false;
        }
    }

    /**
     * Delete File.
     *
     * @param string $file File Path.
     * @return void.
     */
    public static function removeFile(string $file) : void
    {
        if ( is_array($file) )
        {
            foreach ( $file as $f )
            {
                unlink( $f );
            }

            return;
        }

        unlink( $file );
    }
    
    /* ====================================================================================================================================
    * =====================================================================================================================================
    *
    *             FILE HELPERS
    *
    * =====================================================================================================================================
    * =====================================================================================================================================
    */
    
    /**
     * Get file content.
     * 
     * @param obj $iterator Iterator object.
     * @reutnr array.
     */
    private static function getContent($iterator) : array
    {
        $data = [];
        
        foreach( $iterator as $row ) 
        {
            if ( empty( ! is_array( $row ) ? $row : array_filter( $row )))
            { 
                continue; 
            }
            
            $data[] = $row;
        }
        
        return $data;
    }

    /**
     * Get CSV file header.
     * 
     * @param obj $iterator Iterator object.
     * @reutnr array.
     */
    private static function getHeader($iterator) : array
    {
        /**
         * Start iterator and get 1st line usually header.
         */
        $headers   = $iterator->current();
        $hIterator = new \ArrayIterator;
        
        foreach ( $headers as $header ) 
        {
            $hIterator->append( $header );
        }
        
        return $hIterator->getArrayCopy();
    }
    
    /**
     * CSV file.
     * 
     * Read the file with fgetcsv function.
     * 
     * @return iterable.
     */
    private static function fileCsv() : iterable
    {
        $c =  0;

        while ( ! self::$file->eof() ) 
        {
            yield self::$file->fgetcsv();
            
            $c ++;
        }
        
        return $c;
    }

    /**
     * Read the file by line with fgets function.
     *
     * Suitable for smaller text files like CSV and / or
     * include line feeds.
     * 
     * @return iterable.
     */
    private static function fileLine() : iterable
    {
        $c = 0;

        while ( ! self::$file->eof() ) 
        {
            yield self::$file->fgets();
            
            $c ++;
        }
        
        return $c;
    }
}