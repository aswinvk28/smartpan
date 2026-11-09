<?php

/* core/modules/system/templates/feed-icon.html.twig */
class __TwigTemplate_d0cd87a9d52c9aa7bf23cf310db39e9cbc490eff681230dd23db876218b806e1 extends Drupal\Core\Template\TwigTemplate
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 18
        echo "<a href=\"";
        echo twig_render_var($this->getContextReference($context, "url"));
        echo "\"";
        echo twig_render_var($this->getContextReference($context, "attributes"));
        echo ">";
        echo twig_render_var($this->getContextReference($context, "icon"));
        echo "</a>
";
    }

    public function getTemplateName()
    {
        return "core/modules/system/templates/feed-icon.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  19 => 18,);
    }
}
