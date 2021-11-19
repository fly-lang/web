<?php
include_once '..' . DIRECTORY_SEPARATOR . 'config.php';

class GithubApi {
    private $GITHUB_API_RELEASES;
    private $TMP_PATH;
    private $json;

    public function __construct()
    {
        global $GITHUB_API_RELEASES;
        $this->GITHUB_API_RELEASES = $GITHUB_API_RELEASES;
        global $TMP_PATH;
        $this->TMP_PATH = $TMP_PATH;
    }

    public function callApiReleases() {
        $curl = curl_init($this->GITHUB_API_RELEASES);

        curl_setopt($curl, CURLOPT_URL, $this->GITHUB_API_RELEASES);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = array(
            "Accept: application/json",
            "User-Agent: PHP"
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $this->json = curl_exec($curl);
        curl_close($curl);
    }

    public function getReleases() {
        $filename = $this->TMP_PATH . "github-fly-releases.json";
        $lastModifiedTimestamp = filemtime($filename);
        $size = filesize($filename);
        if ($size == 0 || time() - $lastModifiedTimestamp > 86400) {
            $this->callApiReleases();
            $file = fopen($filename, "w");
            fwrite($file, $this->json);
        } else {
            $file = fopen($filename, "r");
            $this->json = fread($file, $size);
        }
        fclose($file);
        return json_decode($this->json);
    }

    public function getLatest() {
        return $this->getReleases()[0];
    }

}
