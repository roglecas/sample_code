<?php namespace DevApp\Application;

/*
 * ----------------------------------------------------
 * Dev Project | Rolando Gonzalez
 *    ------------------
 *      LocalConfig.php
 * ----------------------------------------------------
 */

/**
 * Local Project Configuration.
 */
define('URL_PATH', 'https://localhost/');

/**
 * Environment Server variable.
 */
define('APP_INSTANCE',   'DEV');
define('DEVAPP_ENV_VAL', 'Development');

/**
 * Oracle Development Database.
 */
define('ORA_DEV_USER',   'USER');
define('ORA_DEV_PASS',   'PASS');
define('ORA_DEFT_CHAR',   'AL32UTF8');
define('ORA_DEV_PORT',   ':1521');
define('ORA_DEV_INST',   '/DEV');
define('ORA_DEV_SERVER', 'DB_IP');