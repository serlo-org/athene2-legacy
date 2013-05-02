<?php
/**
 *
 * @author Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @copyright 2013 by www.serlo.org
 * @license LGPL
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
namespace Taxonomy;

interface SharedTaxonomyManagerInterface
{

    public function get ($name, $languageService = NULL);

    /**
     *
     * @param
     *            TaxonomyManagerInterface
     */
    public function add ($name, TaxonomyManagerInterface $manager);
}