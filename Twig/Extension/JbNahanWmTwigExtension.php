<?php
namespace JbNahan\Bundle\WorkflowManagerBundle\Twig\Extension;

use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Extension Twig ajoutant des filtres.
 */
class JbNahanWmTwigExtension extends \Twig_Extension
{

    private $defManager;

    public function __construct($defManager)
    {
        $this->defManager = $defManager;
    }
    /**
     * get filters list
     * @return array
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('definition', array($this, 'definitionNameFilter')),
        );
    }

    /**
     * Retourne le nom d'une définition
     *
     * @param \DateTime $date
     * @param string    $format
     *
     * @return string
     * @throws \Twig_Error
     *
     * @see \strftime()
     */
    public function definitionNameFilter($id)
    {
        $obj = $this->defManager->getById($id);
        if (null === $obj) {
            return $id;
        }
        return $obj->getName();
    }


    /**
     * Returne le nom de l'extension.
     * @return string
     */
    public function getName()
    {
        return 'jb_nahan_wm_twig_extension';
    }
}
