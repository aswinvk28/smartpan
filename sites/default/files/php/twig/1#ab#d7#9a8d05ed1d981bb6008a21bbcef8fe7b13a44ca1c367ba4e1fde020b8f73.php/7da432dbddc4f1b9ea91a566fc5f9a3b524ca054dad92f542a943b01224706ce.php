<?php

/* core/modules/system/templates/form-element.html.twig */
class __TwigTemplate_abd79a8d05ed1d981bb6008a21bbcef8fe7b13a44ca1c367ba4e1fde020b8f73 extends Drupal\Core\Template\TwigTemplate
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
        // line 39
        echo "<div";
        echo twig_render_var($this->getContextReference($context, "attributes"));
        echo ">
  ";
        // line 40
        if (twig_in_filter((isset($context["label_display"]) ? $context["label_display"] : null), array(0 => "before", 1 => "invisible"))) {
            // line 41
            echo "    ";
            echo twig_render_var($this->getContextReference($context, "label"));
            echo "
  ";
        }
        // line 43
        echo "  ";
        if ((!twig_test_empty((isset($context["prefix"]) ? $context["prefix"] : null)))) {
            // line 44
            echo "    <span class=\"field-prefix\">";
            echo twig_render_var($this->getContextReference($context, "prefix"));
            echo "</span>
  ";
        }
        // line 46
        echo "  ";
        echo twig_render_var($this->getContextReference($context, "children"));
        echo "
  ";
        // line 47
        if ((!twig_test_empty((isset($context["suffix"]) ? $context["suffix"] : null)))) {
            // line 48
            echo "    <span class=\"field-suffix\">";
            echo twig_render_var($this->getContextReference($context, "suffix"));
            echo "</span>
  ";
        }
        // line 50
        echo "  ";
        if (((isset($context["label_display"]) ? $context["label_display"] : null) == "after")) {
            // line 51
            echo "    ";
            echo twig_render_var($this->getContextReference($context, "label"));
            echo "
  ";
        }
        // line 53
        echo "  ";
        if ($this->getAttribute((isset($context["description"]) ? $context["description"] : null), "content")) {
            // line 54
            echo "    <div";
            echo twig_render_var($this->getAttribute($this->getContextReference($context, "description"), "attributes"));
            echo ">
      ";
            // line 55
            echo twig_render_var($this->getAttribute($this->getContextReference($context, "description"), "content"));
            echo "
    </div>
  ";
        }
        // line 58
        echo "</div>
";
    }

    public function getTemplateName()
    {
        return "core/modules/system/templates/form-element.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  77 => 58,  71 => 55,  66 => 54,  63 => 53,  57 => 51,  54 => 50,  35 => 44,  26 => 41,  24 => 40,  51 => 32,  48 => 48,  46 => 47,  43 => 27,  41 => 46,  36 => 24,  34 => 23,  32 => 43,  25 => 20,  23 => 19,  19 => 39,);
    }
}
