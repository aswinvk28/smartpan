<?php

/* core/modules/system/templates/html.html.twig */
class __TwigTemplate_dad80a9619cc75f6c2581dffaaf6e3eeab06eba3966a6ff59e20d431390d473a extends Drupal\Core\Template\TwigTemplate
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
        // line 29
        echo "<!DOCTYPE html>
<html";
        // line 30
        echo twig_render_var($this->getContextReference($context, "html_attributes"));
        echo ">
  <head>
    ";
        // line 32
        echo twig_render_var($this->getContextReference($context, "head"));
        echo "
    <title>";
        // line 33
        echo twig_render_var($this->getContextReference($context, "head_title"));
        echo "</title>
    ";
        // line 34
        echo twig_render_var($this->getContextReference($context, "styles"));
        echo "
    ";
        // line 35
        echo twig_render_var($this->getContextReference($context, "scripts"));
        echo "
  </head>
  <body";
        // line 37
        echo twig_render_var($this->getContextReference($context, "attributes"));
        echo ">
    <a href=\"#main-content\" class=\"visually-hidden focusable skip-link\">
      ";
        // line 39
        echo twig_render_var(t("Skip to main content"));
        echo "
    </a>
    ";
        // line 41
        echo twig_render_var($this->getContextReference($context, "page_top"));
        echo "
    ";
        // line 42
        echo twig_render_var($this->getContextReference($context, "page"));
        echo "
    ";
        // line 43
        echo twig_render_var($this->getContextReference($context, "page_bottom"));
        echo "
  </body>
</html>
";
    }

    public function getTemplateName()
    {
        return "core/modules/system/templates/html.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  58 => 42,  49 => 39,  31 => 33,  22 => 30,  44 => 37,  38 => 49,  81 => 46,  68 => 41,  64 => 39,  45 => 34,  39 => 35,  30 => 47,  33 => 17,  112 => 107,  104 => 103,  98 => 100,  95 => 99,  92 => 98,  86 => 95,  83 => 94,  80 => 93,  74 => 43,  72 => 90,  62 => 43,  56 => 83,  42 => 19,  40 => 75,  29 => 16,  27 => 32,  76 => 58,  70 => 56,  67 => 54,  60 => 52,  55 => 37,  21 => 13,  85 => 44,  79 => 43,  65 => 86,  50 => 32,  47 => 47,  28 => 46,  77 => 44,  71 => 39,  66 => 54,  63 => 36,  57 => 50,  54 => 41,  35 => 34,  26 => 25,  24 => 45,  51 => 36,  48 => 52,  46 => 47,  43 => 46,  41 => 46,  36 => 31,  34 => 23,  32 => 29,  25 => 41,  23 => 27,  19 => 29,);
    }
}
