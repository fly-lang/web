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
        $file = fopen($filename, "r+");
        if (time() - $lastModifiedTimestamp > 86400) {
            $this->callApiReleases();
            fwrite($file, $this->json);
        } else {
            $this->json = fread($file, filesize($filename));
        }
        fclose($file);
        return json_decode($this->json);
    }

    public function getLatest() {
        return $this->getReleases()[0];
    }

}
