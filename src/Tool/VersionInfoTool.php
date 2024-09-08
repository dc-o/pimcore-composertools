<?php
namespace DCO\ComposerTools\Tool;

class VersionInfoTool {
    /**
     * get composer package info for the specified $packageName - if installed
     *
     * returns null if there is no composer-managed project root or if the package is not installed
     * @param $packageName string
     * @return mixed|null
     */
    private static function getPackageInfo(string $packageName) {
        $lock_file = @json_decode(@file_get_contents(PIMCORE_PROJECT_ROOT.'/composer.lock'), JSON_OBJECT_AS_ARRAY);
        if (empty($lock_file))
            return null;
        foreach ($lock_file['packages'] as $package) {
            if ($package['name'] == $packageName) {
                return $package;
            }
        }
        return null;
    }

    /**
     * returns version string with numeric and vcs version of a package
     * @param $packageName string
     * @return mixed|string
     */
    public static function getVersion(string $packageName) {
        $version = 'unknown';
        $pack = self::getPackageInfo($packageName);
        if (empty($pack))
            return $version;
        if (isset($pack['dist'])) {
            return $pack['version'].' ('.substr($pack['dist']['reference'], 0, 7).')';
        }
        else if (isset($pack['source'])) {
            return $pack['version'].' ('.substr($pack['source']['reference'], 0, 7).')';
        }
        else {
            return $pack['version'];
        }
    }

    /**
     * get time of last installed update of the specified package
     * @param string $packageName
     * @return false|int|null
     */
    public static function getLastUpdated(string $packageName) {
        $pack = self::getPackageInfo($packageName);
        if (empty($pack) || empty($pack['time']))
            return null;
        return strtotime($pack['time']);
    }
}
