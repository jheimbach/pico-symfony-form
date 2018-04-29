<?php
/**
 * Created by 4/29/18 7:59 PM.
 * @author Mediengstalt Heimbach - Johannes Heimbach
 */

namespace PicoSymForm;

class Form
{

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $confDir;

    /**
     * @var \Pico
     */
    private $pico;

    /**
     * @var FormElement[]
     */
    private $formElements;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Form constructor.
     * @param string $name
     * @param string $confDir
     */
    public function __construct($pico, $name, $confDir)
    {
        $this->pico = $pico;
        $this->name = $name;
        $this->confDir = $confDir;

        $this->loadConfig();
    }

    private function loadConfig()
    {
        $yamlParser = $this->pico->getYamlParser();

        $loadConfigClosure = function ($configFile) use ($yamlParser) {
            $yaml = file_get_contents($configFile);
            $config = $yamlParser->parse($yaml);
            return is_array($config) ? $config : array();
        };

        $filename = $this->confDir . $this->name . '.yml';
        if (file_exists($filename)) {
            $form = $loadConfigClosure($filename);
        } else {
            throw new \RuntimeException('Form config for ' . $this->name . ' not found');
        }

        $this->formElements = $this->parse($form);
    }

    private function parse($form)
    {
        $array_map = [];
        foreach ($form as $key => $el) {
            $array_map[] = new FormElement($key, $el);
        }
        return $array_map;
    }

    /**
     * @return FormElement[]
     */
    public function getFormElements()
    {
        return $this->formElements;
    }
}