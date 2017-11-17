<?php

namespace App\Game;

use App\Game\Loader\LoaderInterface;
use Psr\Log\LoggerInterface;

class WordList implements DictionaryLoaderInterface, WordListInterface
{
    private $words;
    private $loaders;
    private $logger;
    private $customDictionnaryPath;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->words   = array();
        $this->loaders = array();
        $this->customDictionnaryPath = '/home/theo/Documents/ProjetsInfo/hangman/data/customwords.txt';
    }

    public function addLoader($type, LoaderInterface $loader)
    {
        $this->loaders[strtolower($type)] = $loader;
    }

    public function loadDictionaries(array $dictionaries)
    {
        foreach ($dictionaries as $dictionary) {
            $this->loadDictionary($dictionary);
        }
        $this->loadDictionary($this->customDictionnaryPath);
        $this->logger->debug('LOAD DICTIONNARIES',$this->words);
    }

    private function loadDictionary($path)
    {
        $loader = $this->findLoader(pathinfo($path, PATHINFO_EXTENSION));

        $words = $loader->load($path);
        foreach ($words as $word) {
            $this->addWord($word);
        }
    }

    private function getWordsDictionnary($path) {
        $loader = $this->findLoader(pathinfo($path, PATHINFO_EXTENSION));
        $words = $loader->load($path);
        $arrayDictionnary = array();

        foreach ($words as $word) {
            $length = strlen($word);
            
            if (!isset($arrayDictionnary[$length])) {
                $arrayDictionnary[$length] = array();
            }
    
            if (!in_array($word, $arrayDictionnary[$length])) {
                $arrayDictionnary[$length][] = $word;
            }
        }

        $this->logger->debug('Get Words Dictionnary',$arrayDictionnary);
        return $arrayDictionnary;
    }

    private function findLoader($type)
    {
        $type = strtolower($type);
        if (!isset($this->loaders[$type])) {
            throw new \RuntimeException(sprintf('There is no loader able to load a %s dictionary.', $type));
        }

        return $this->loaders[$type];
    }

    public function getRandomWord($length)
    {
        if (!isset($this->words[$length])) {
            throw new \InvalidArgumentException(sprintf('There is no word of length %u.', $length));
        }

        $key = array_rand($this->words[$length]);

        return $this->words[$length][$key];
    }

    public function addWord($word)
    {
        $length = strlen($word);

        if (!isset($this->words[$length])) {
            $this->words[$length] = array();
        }

        if (!in_array($word, $this->words[$length])) {
            $this->words[$length][] = $word;
        }
    }

    public function removeCustomWord($newWord)
    {
        $length = strlen($newWord);
        $customDictionnary = $this->getCustomWords();

        $keyWordToRemove = array_search($newWord,$customDictionnary[$length]);
        if( $keyWordToRemove !== false ) 
        {
            unset($customDictionnary[$length][$keyWordToRemove]);
        }
        else 
        {
            throw new Exception('The words' . $newWord . 'doesn\'t exist in the word list');
        }
        
        $this->overwriteCustomDictionnary($customDictionnary);
    }

    public function getCustomWords() {
        ksort($this->words); // We order the Array be key
        return $this->getWordsDictionnary($this->customDictionnaryPath);
    }

    public function addCustomWord($word) {
        $customDictionnary = $this->getCustomWords();        
        $this->logger->debug('Before Add Custom Word',$customDictionnary);
        $length = strlen($word);

        if (!isset($customDictionnary[$length])) {
            $customDictionnary[$length] = array();
        }

        if (!in_array($word, $customDictionnary[$length])) {
            $customDictionnary[$length][] = $word;
        }

        $this->logger->debug('Add Custom Word',$customDictionnary);
        $this->overwriteCustomDictionnary($customDictionnary);
    }

    public function overwriteCustomDictionnary($newArrayCustomWords) {
        $stringToWrite = "";

        foreach($newArrayCustomWords as $newArrayCustomWordsByLenght) {
            foreach($newArrayCustomWordsByLenght as $word) {
                $stringToWrite = $stringToWrite . $word . PHP_EOL;
            }
        }

        $this->logger->debug('Strings to write' . $stringToWrite);
        file_put_contents($this->customDictionnaryPath,$stringToWrite);
    }
}