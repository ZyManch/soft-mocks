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
        require_once(__DIR__ . "/../../vendor/PHP-Parser/lib/PhpParser/Autoloader.php");
        \PhpParser\Autoloader::register(true);
        $this->_loadDir(__DIR__ . '/../../vendor/PHP-Parser/lib/PhpParser/');
        $this->_loadDir(__DIR__ . '/../../src/QA/');
        SoftMocks::setLockFilePath(sys_get_temp_dir().'/soft_mocks_rewrite.lock');
        SoftMocks::setPhpunitPath(realpath($root.'/vendor/phpunit'));
        SoftMocks::addIgnorePath(array_map('realpath',[
            dirname(dirname(__DIR__)),
            $root.'/vendor/codeception/codeception/src',
            $root.'/vendor/behat',
            $root.'/vendor/sebastian',
            $root .'/tests'
        ]));
        SoftMocks::ignoreFiles(array_map('realpath',$ignoreFiles));
        SoftMocks::init();
    }

    protected function _loadDir($dir) {
        $dir = realpath($dir);
        $files = scandir($dir, SCANDIR_SORT_NONE );
        foreach ($files as $file) {
            if ($file[0]==='.') {
                continue;
            }
            $fullName = $dir.'/'.$file;
            if (is_dir($fullName)) {
                $this->_loadDir($fullName);
            } else if ($file!=='SoftMockLoader.php') {
                require_once($fullName);
            }

        }
    }

    public function includeFile($fileName, $args = []) {
        extract($args, EXTR_OVERWRITE);
        require_once(SoftMocks::rewrite(realpath($fileName)));
    }
}