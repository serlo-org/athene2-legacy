<?php
/**
 * 
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author	Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @license	LGPL-3.0
 * @license	http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link		https://github.com/serlo-org/athene2 for the canonical source repository
 * @copyright Copyright (c) 2013 Gesellschaft für freie Bildung e.V. (http://www.open-education.eu/)
 */
namespace Token;

use Token\Provider;

class Tokenizer implements TokenizerInterface
{

    /**
     *
     * @var Provider\ProviderInterface
     */
    protected $provider;

    /**
     *
     * @return Provider\ProviderInterface $provider
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     *
     * @param ProviderInterface $provider            
     * @return $this
     */
    public function setProvider(Provider\ProviderInterface $provider)
    {
        $this->provider = $provider;
        return $this;
    }
    
    public function transliterate($tokenString){
        // WHY DO YOU NOT WORK WHEN { IS THE FIRST CHAR
        $tokenString = ':'.$tokenString;
        
        $returnString = $tokenString;
        
        $token = strtok($tokenString, '{');
        while($token !== FALSE){
            $token = strtok('}');
            $replace = '{'.$token.'}';
            $with = $this->transliterateToken($token);
            $limit = 1;
            $returnString = str_replace($replace, $with, $returnString, $limit);
            $token = strtok('{');
        }
        
        // WHY DO YOU NOT WORK WHEN { IS THE FIRST CHAR
        return substr($returnString, 1);
    }
    
    protected function transliterateToken($token){
        $data = $this->getProvider()->getData();
        if(!array_key_exists($token, $data))
            throw new \RuntimeException(sprintf('Token `%s` not provided by `%s`', $token, get_class($this->getProvider())));
        return $data[$token];
    }
}