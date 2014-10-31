<?php

namespace Ahonymous\TimezoneBundle\Storage;

use Symfony\Component\Yaml\Dumper;
use Symfony\Component\Yaml\Yaml;

class YamlStorage
{
    /**
     * @var string
     */
    private $yamlFile;

    /**
     * @param string $yamlFile
     */
    public function __construct($yamlFile = __DIR__ . "/../Resources/data/timezones.yml")
    {
        $this->yamlFile = $yamlFile;
    }

    /**
     * @return array
     */
    public function getContent()
    {
        $content = Yaml::parse($this->yamlFile);

        return is_array($content) ? $content : [];
    }

    /**
     * @param  array $yaml
     * @param  int   $inline
     * @return int
     */
    public function setContent($yaml = [], $inline = 1)
    {
        $dumper = new Dumper();
        $yaml = $dumper->dump($yaml, $inline);

        return file_put_contents($this->yamlFile, $yaml);
    }

    /**
     * @param $key
     * @return null
     */
    public function getRecord($key)
    {
        return array_key_exists($key, $this->getContent()) ? $this->getContent()[$key] : null;
    }

    /**
     * @param $key
     * @return bool
     */
    public function checkRecord($key)
    {
        return array_key_exists($key, $content = $this->getContent());
    }

    /**
     * @param $key
     * @param $value
     * @return int
     * @throws \Exception
     */
    public function addRecord($key, $value)
    {
        if ($this->checkRecord($key)) {
            throw new \Exception(["Key \"$key\" is exist."]);
        }

        $content = $this->getContent();
        $content[$key] = $value;

        return $this->setContent($content);
    }

    /**
     * @param $key
     * @return array
     * @throws \Exception
     */
    public function removeRecord($key)
    {
        if (!$this->checkRecord($key)) {
            throw new \Exception(["Key \"$key\" is not exist."]);
        }

        $content = $this->getContent();

        return array_diff_key($content, $content[$key]);
    }
}
