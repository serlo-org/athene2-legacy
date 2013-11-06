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
namespace Blog\Manager;

use Language\Service\LanguageServiceInterface;

interface BlogManagerInterface
{

    /**
     *
     * @param int $id            
     * @return PostManagerInterface
     */
    public function getBlog($id);

    /**
     *
     * @param string $name            
     * @param LanguageServiceInterface $language            
     * @return PostManagerInterface
     */
    public function findBlogByCategory($name, LanguageServiceInterface $language);
    
    /**
     * 
     * @param LanguageServiceInterface $languageService
     * @return PostManagerInterface[]
     */
    public function findAllBlogs(LanguageServiceInterface $languageService);
}