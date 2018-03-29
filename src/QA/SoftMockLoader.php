<?php
namespace QA;
/**
 * Created by PhpStorm.
 * User: ZyManch
 * Date: 10.01.2017
 * Time: 18:28
 */
class SoftMockLoader {

    public function __construct($root, $ignoreFiles = []) {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            define('SOFTMOCKS_ROOT_PATH', '');
        } else {
            define('SOFTMOCKS_ROOT_PATH', '/');
        }
        $this->_loadDir(__DIR__ . '/vendor/PHP-Parser/lib/PhpParser/');
        $this->_loadDir(__DIR__ . '/src/QA/');
        \PhpParser\Autoloader::register(true);
        SoftMocks::setLockFilePath(sys_get_temp_dir().'/soft_mocks_rewrite.lock');
        SoftMocks::setPhpunitPath(realpath($root.'/vendor/phpunit'));
        SoftMocks::addIgnorePath([
             __DIR__,
             $root.'/vendor/codeception',
             $root.'/vendor/behat',
             $root.'/vendor/sebastian',
             $root .'/tests'
        ]);
        SoftMocks::ignoreFiles(array_map('realpath',$ignoreFiles));
        SoftMocks::init();
    }

    protected function _loadDir($dir) {
        $out = [];
        $command = sprintf(
            "find %s -type f -name '*.php'",
            escapeshellarg($dir)
        );
        @exec($command, $out);
        foreach ($out as $f) {
            if (substr($f,-strlen('SoftMockLoader.php')) !== 'SoftMockLoader.php') {
                require_once($f);
            }
        }
    }

    public function includeFile($fileName) {
        require_once(SoftMocks::rewrite($fileName));
    }
}