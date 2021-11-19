<?php
include_once '..' . DIRECTORY_SEPARATOR . 'config.php';
include_once 'github-api.php';

class FlyReleases {

    private $githubApi;

    public function __construct()
    {
        $this->githubApi = new GithubApi();
    }

    public function getLatest() {
        $packages = array(
            "version" => $this->githubApi->getLatest()->tag_name,
            "linux" => array("name" => "", "url" => "", "size" => "", "content_type" => ""),
            "windows" => array("name" => "", "url" => "", "size" => "", "content_type" => ""),
            "macos" => array("name" => "", "url" => "", "size" => "", "content_type" => ""),
        );

        foreach ($this->githubApi->getLatest()->assets as $asset) {
            if (strpos($asset->name, 'linux') !== false) {
                $packages['linux']['name'] = $asset->name;
                $packages['linux']['size'] = $asset->size;
                $packages['linux']['url'] = $asset->browser_download_url;
                $packages['linux']['content_type'] = $asset->content_type;
            } else if (strpos($asset->name, 'win') !== false) {
                $packages['windows']['name'] = $asset->name;
                $packages['windows']['size'] = $asset->size;
                $packages['windows']['url'] = $asset->browser_download_url;
                $packages['windows']['content_type'] = $asset->content_type;
            } else if (strpos($asset->name, 'macos') !== false) {
                $packages['macos']['name'] = $asset->name;
                $packages['macos']['size'] = $asset->size;
                $packages['macos']['url'] = $asset->browser_download_url;
                $packages['macos']['content_type'] = $asset->content_type;
            }
        }
        return $packages;
    }
}
