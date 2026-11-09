<?php

/* themes/flatui/templates/html.html.twig */
class __TwigTemplate_f15928372c9761d7292bc15e011bf32e735b7ca039d361e7ade7149779dddaba extends Drupal\Core\Template\TwigTemplate
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
        echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML+RDFa 1.0//EN\"
  \"http://www.w3.org/MarkUp/DTD/xhtml-rdfa-1.dtd\">
<html ";
        // line 3
        echo twig_render_var($this->getContextReference($context, "html_attributes"));
        echo ">

    <head>
        ";
        // line 6
        echo twig_render_var($this->getContextReference($context, "head"));
        echo "
        <title>";
        // line 7
        echo twig_render_var($this->getContextReference($context, "head_title"));
        echo "</title>
        ";
        // line 8
        echo twig_render_var($this->getContextReference($context, "styles"));
        echo "
        ";
        // line 9
        echo twig_render_var($this->getContextReference($context, "scripts"));
        echo "
        <!--[if lte IE 8]>
            <script src=\"/core/vendor/html5shiv/html5.js\" type=\"text/javascript\"></script>
            <script src=\"/sites/web/packages/PIE/PIE.js\" type=\"text/javascript\"></script>
        <![endif]-->
    </head>
    <body class=\"body-patternbg\">
        ";
        // line 16
        echo twig_render_var($this->getContextReference($context, "page_top"));
        echo "
        ";
        // line 17
        echo twig_render_var($this->getContextReference($context, "page"));
        echo "
        ";
        // line 18
        echo twig_render_var($this->getContextReference($context, "page_bottom"));
        echo "
    </body>
</html>";
    }

    public function getTemplateName()
    {
        return "themes/flatui/templates/html.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  59 => 18,  37 => 8,  33 => 7,  23 => 3,  72 => 18,  68 => 17,  63 => 15,  51 => 16,  47 => 11,  40 => 7,  25 => 4,  289 => 110,  282 => 106,  274 => 104,  272 => 103,  267 => 100,  261 => 97,  258 => 96,  255 => 95,  249 => 92,  246 => 91,  244 => 90,  239 => 88,  234 => 87,  228 => 84,  225 => 83,  223 => 82,  218 => 81,  212 => 78,  209 => 77,  206 => 76,  200 => 73,  196 => 72,  192 => 71,  187 => 70,  185 => 69,  178 => 65,  175 => 64,  164 => 62,  158 => 59,  153 => 57,  145 => 54,  139 => 51,  136 => 50,  133 => 49,  129 => 47,  123 => 44,  108 => 39,  97 => 34,  89 => 29,  73 => 22,  71 => 21,  66 => 18,  61 => 16,  54 => 13,  45 => 10,  39 => 8,  34 => 6,  31 => 5,  29 => 6,  22 => 2,  26 => 3,  21 => 2,  155 => 58,  149 => 56,  146 => 89,  143 => 88,  137 => 85,  134 => 84,  131 => 83,  125 => 81,  122 => 80,  116 => 43,  113 => 42,  110 => 75,  104 => 73,  102 => 36,  99 => 35,  93 => 68,  90 => 67,  84 => 21,  81 => 63,  79 => 62,  76 => 23,  70 => 58,  67 => 57,  64 => 17,  58 => 15,  55 => 17,  52 => 51,  46 => 48,  43 => 9,  41 => 9,  36 => 7,  30 => 43,  28 => 42,  24 => 41,  19 => 1,);
    }
}
