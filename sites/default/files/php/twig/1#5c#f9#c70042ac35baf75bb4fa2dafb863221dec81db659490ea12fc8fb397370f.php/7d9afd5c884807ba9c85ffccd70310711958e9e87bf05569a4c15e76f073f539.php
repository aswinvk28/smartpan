<?php

/* themes/flatui/templates/menu/secondary-menu.html.twig */
class __TwigTemplate_5cf9c70042ac35baf75bb4fa2dafb863221dec81db659490ea12fc8fb397370f extends Drupal\Core\Template\TwigTemplate
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
        // line 1
        echo "<div class=\"btn-group\">
    <button data-toggle=\"dropdown\" class=\"btn btn-success dropdown-toggle\">";
        // line 2
        echo twig_render_var($this->getContextReference($context, "text"));
        echo " <span class=\"caret\"></span></button>
    <ul class=\"dropdown-menu btn-success\" role=\"menu\" aria-labelledby=\"dropdownMenu\">
        ";
        // line 4
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["links"]) ? $context["links"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["link"]) {
            // line 5
            echo "        <li class=\"white\"><a class=\"nounderline white\" href=\"";
            echo twig_render_var($this->getAttribute($this->getContextReference($context, "link"), "href"));
            echo "\">";
            echo twig_render_var($this->getAttribute($this->getContextReference($context, "link"), "title"));
            echo "</a></li>
        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['link'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 7
        echo "    </ul>
</div>";
    }

    public function getTemplateName()
    {
        return "themes/flatui/templates/menu/secondary-menu.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  42 => 7,  31 => 5,  27 => 4,  22 => 2,  19 => 1,);
    }
}
