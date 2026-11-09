<?php

/* themes/flatui/templates/forms/form-autocomplete.html.twig */
class __TwigTemplate_4362707e4ece7db2c09ef9d834a9ec3e8299c109df2a39ff9dd87ad835cca417 extends Drupal\Core\Template\TwigTemplate
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
        echo "<div class=\"input-append\">
    <input ";
        // line 2
        echo twig_render_var($this->getContextReference($context, "attributes"));
        echo " type=\"text\" class=\"input-padding-large input-xlarge search-query\" autocomplete=\"off\" />
    <input type=\"hidden\" ";
        // line 3
        echo twig_render_var($this->getContextReference($context, "hidden_attributes"));
        echo " />
    <button class=\"btn btn-large stock-symbol-search\" type=\"submit\">Search</button>
</div>";
    }

    public function getTemplateName()
    {
        return "themes/flatui/templates/forms/form-autocomplete.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  289 => 110,  282 => 106,  274 => 104,  272 => 103,  267 => 100,  261 => 97,  258 => 96,  255 => 95,  249 => 92,  246 => 91,  244 => 90,  239 => 88,  234 => 87,  228 => 84,  225 => 83,  223 => 82,  218 => 81,  212 => 78,  209 => 77,  206 => 76,  200 => 73,  196 => 72,  192 => 71,  187 => 70,  185 => 69,  178 => 65,  175 => 64,  164 => 62,  158 => 59,  153 => 57,  145 => 54,  139 => 51,  136 => 50,  133 => 49,  129 => 47,  123 => 44,  108 => 39,  97 => 34,  89 => 29,  73 => 22,  71 => 21,  66 => 18,  61 => 16,  54 => 13,  45 => 9,  39 => 8,  34 => 6,  31 => 5,  29 => 4,  22 => 2,  26 => 3,  21 => 15,  155 => 58,  149 => 56,  146 => 89,  143 => 88,  137 => 85,  134 => 84,  131 => 83,  125 => 81,  122 => 80,  116 => 43,  113 => 42,  110 => 75,  104 => 73,  102 => 36,  99 => 35,  93 => 68,  90 => 67,  84 => 26,  81 => 63,  79 => 62,  76 => 23,  70 => 58,  67 => 57,  64 => 17,  58 => 15,  55 => 52,  52 => 51,  46 => 48,  43 => 47,  41 => 46,  36 => 7,  30 => 43,  28 => 42,  24 => 41,  19 => 1,);
    }
}
